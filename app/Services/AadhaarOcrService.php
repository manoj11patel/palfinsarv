<?php

namespace App\Services;

use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\BatchAnnotateImagesRequest;
use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\Image;

class AadhaarOcrService
{
    private string $credentialsPath;

    public function __construct()
    {
        $this->credentialsPath = env(
            'GOOGLE_APPLICATION_CREDENTIALS',
            storage_path('app/google/credentials.json')
        );
    }

    /**
     * Extract full text from an image using Google Cloud Vision DOCUMENT_TEXT_DETECTION.
     */
    public function extractText(string $imagePath): string
    {
        $client = new ImageAnnotatorClient([
            'credentials' => $this->credentialsPath,
        ]);

        try {
            $image = new Image();
            $image->setContent(file_get_contents($imagePath));

            $feature = new Feature();
            $feature->setType(Type::DOCUMENT_TEXT_DETECTION);

            $annotateRequest = new AnnotateImageRequest();
            $annotateRequest->setImage($image);
            $annotateRequest->setFeatures([$feature]);

            $batchRequest = new BatchAnnotateImagesRequest();
            $batchRequest->setRequests([$annotateRequest]);

            $response   = $client->batchAnnotateImages($batchRequest);
            $annotation = $response->getResponses()[0]->getFullTextAnnotation();

            return $annotation ? $annotation->getText() : '';
        } finally {
            $client->close();
        }
    }

    /**
     * Parse raw OCR text from an Aadhaar card and return structured address fields.
     */
    public function parseAddress(string $text): array
    {
        $lines = array_values(array_filter(
            array_map('trim', explode("\n", $text)),
            fn($l) => $l !== ''
        ));

        $result = [
            'raw_text'   => $text,
            'address'    => '',
            'pincode'    => '',
            'state_name' => '',
            'city_name'  => '',
        ];

        // --- Extract 6-digit Indian pincode ---
        if (preg_match('/\b(\d{6})\b/', $text, $m)) {
            $result['pincode'] = $m[1];
        }

        // --- Find "Address:" block ---
        $addressStart = -1;
        foreach ($lines as $i => $line) {
            if (preg_match('/^addr[ae]ss\s*[:\-]?\s*/i', $line)) {
                $addressStart = $i;
                break;
            }
        }

        $addrLines = [];

        if ($addressStart !== -1) {
            $firstLine = trim(preg_replace('/^addr[ae]ss\s*[:\-]?\s*/i', '', $lines[$addressStart]));
            if ($firstLine !== '') {
                $addrLines[] = $firstLine;
            }
            for ($i = $addressStart + 1; $i < count($lines); $i++) {
                $l = $lines[$i];
                if (preg_match('/^(VID|DOB|Date of Issue|Issue Date|Enrolment|Mobile|Phone)\s*[:\-]/i', $l)) {
                    break;
                }
                $addrLines[] = $l;
            }
        } else {
            $addrLines = $this->guessAddressLines($lines);
        }

        $result['address'] = implode(', ', $addrLines);

        // --- Extract state name from the line that contains the pincode ---
        if ($result['pincode']) {
            foreach ($lines as $line) {
                if (str_contains($line, $result['pincode'])) {
                    $stateLine = trim(preg_replace('/[-\s]*' . $result['pincode'] . '.*$/', '', $line), " ,\t-");
                    if (strlen($stateLine) >= 3) {
                        $result['state_name'] = $stateLine;
                    }
                    break;
                }
            }

            if (!$result['state_name'] && $result['address']) {
                if (preg_match('/,\s*([A-Za-z ]+)\s*[-–]?\s*' . $result['pincode'] . '/u', $result['address'], $m)) {
                    $result['state_name'] = trim($m[1]);
                }
            }

            $result['address'] = trim(
                preg_replace('/[-\s]*' . $result['pincode'] . '\s*$/', '', $result['address']),
                " ,\t-"
            );
        }

        return $result;
    }

    private function guessAddressLines(array $lines): array
    {
        $skipPatterns = [
            '/^\d{4}\s+\d{4}\s+\d{4}$/',
            '/^(Male|Female|Transgender)$/i',
            '/^DOB\s*[:\-]/i',
            '/^(Govt\.?\s*of\s*India|Government of India)/i',
            '/^(Aadhaar|AADHAAR|UNIQUE IDENTIFICATION)/i',
            '/^(my\s*aadhaar|www\.)/i',
        ];

        $addrLines  = [];
        $inAddrZone = false;

        foreach ($lines as $line) {
            $skip = false;
            foreach ($skipPatterns as $pattern) {
                if (preg_match($pattern, $line)) { $skip = true; break; }
            }
            if ($skip) continue;

            if (preg_match('/\d/', $line) && strlen($line) > 4) {
                $inAddrZone = true;
            }
            if ($inAddrZone) {
                $addrLines[] = $line;
            }
        }

        return array_slice($addrLines, 0, 8);
    }
}
