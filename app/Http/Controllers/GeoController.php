<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Queries\SearchCities;
use App\Queries\SearchStates;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON autocomplete for location fields. Only fires from the client at >= 3
 * characters; the controller enforces the same floor so short/empty queries
 * never scan the table.
 */
final class GeoController extends Controller
{
    public function cities(Request $request, SearchCities $search): JsonResponse
    {
        $query = mb_trim((string) $request->query('q', ''));

        return response()->json(
            mb_strlen($query) < 3 ? [] : $search->execute($query),
        );
    }

    public function states(Request $request, SearchStates $search): JsonResponse
    {
        $query = mb_trim((string) $request->query('q', ''));

        return response()->json(
            mb_strlen($query) < 3 ? [] : $search->execute($query),
        );
    }
}
