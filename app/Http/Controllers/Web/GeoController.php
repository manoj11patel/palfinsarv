<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GeoController extends Controller
{
    public function countries(Request $request): JsonResponse
    {
        $search = trim($request->string('q')->toString());

        $countries = Country::where('status', 1)
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name'])
            ->map(fn($c) => ['id' => $c->id, 'text' => $c->name]);

        return response()->json(['results' => $countries]);
    }

    public function states(Request $request): JsonResponse
    {
        $countryId = $request->integer('country_id');
        $search    = trim($request->string('q')->toString());

        if (!$countryId) {
            return response()->json(['results' => []]);
        }

        $states = State::where('country_id', $countryId)
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($s) => ['id' => $s->id, 'text' => $s->name]);

        return response()->json(['results' => $states]);
    }

    /**
     * Fuzzy lookup: given a state name (+ optional city), return IDs for Select2 pre-fill.
     * Scoped to India (country_id = 32).
     */
    public function findByName(Request $request): JsonResponse
    {
        $stateName = trim($request->string('state')->toString());
        $cityName  = trim($request->string('city')->toString());

        $indiaId = 32;

        $state = null;
        if ($stateName) {
            // Try exact first, then LIKE
            $state = State::where('country_id', $indiaId)
                ->where('name', $stateName)
                ->first()
                ?? State::where('country_id', $indiaId)
                    ->where('name', 'like', '%' . $stateName . '%')
                    ->first();
        }

        $city = null;
        if ($cityName && $state) {
            $city = City::where('state_id', $state->id)
                ->where('name', $cityName)
                ->first()
                ?? City::where('state_id', $state->id)
                    ->where('name', 'like', '%' . $cityName . '%')
                    ->first();
        }

        return response()->json([
            'country' => ['id' => $indiaId, 'text' => 'India'],
            'state'   => $state ? ['id' => $state->id, 'text' => $state->name] : null,
            'city'    => $city  ? ['id' => $city->id,  'text' => $city->name]  : null,
        ]);
    }

    public function cities(Request $request): JsonResponse
    {
        $stateId = $request->integer('state_id');
        $search  = trim($request->string('q')->toString());
        $page    = max(1, $request->integer('page', 1));

        if (!$stateId) {
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }

        $paginated = City::where('state_id', $stateId)
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->orderBy('name')
            ->paginate(30, ['*'], 'page', $page);

        return response()->json([
            'results'    => $paginated->getCollection()->map(fn($c) => ['id' => $c->id, 'text' => $c->name])->values(),
            'pagination' => ['more' => $paginated->hasMorePages()],
        ]);
    }
}
