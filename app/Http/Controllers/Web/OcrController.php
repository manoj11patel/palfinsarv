<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AadhaarOcrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OcrController extends Controller
{
    public function __construct(private AadhaarOcrService $ocr) {}

    public function extractAadhaar(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);

        // Use PHP's native temp path directly — no storage write, no separator issues on Windows
        $fullPath = $request->file('file')->getRealPath();

        try {
            $text   = $this->ocr->extractText($fullPath);
            $parsed = $this->ocr->parseAddress($text);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => 'ocr_failed',
                'message' => 'OCR processing failed: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data'    => $parsed,
        ]);
    }
}
