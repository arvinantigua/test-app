<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class OMDbService
{

    public function searchMovies($keyword = null, $page = null) {
        
        $currentPage = $page ?? 1;
        $response = HTTP::get(config('omdb.url'), [
            'apiKey' => config('omdb.key'),
            's' => $keyword ?? config('omdb.default_search'),
            'page' => $currentPage,
        ]);

        if (isset($response['totalResults'])) {
            $totalResults = $response['totalResults'];
            $lastPage = (int) ceil($response['totalResults'] / 10);
            $data = collect($response->json('Search'))->map(function ($movie) {
                $movie['yourRate'] = request()->user()->ratings()->firstWhere('imdb_id', $movie['imdbID'])->rate ?? null;
                return $movie;
            });

            return response()->json([
                "data" => $data,
                "totalResults" => (int) $totalResults,
                "currentPage" => (int)  $currentPage,
                "lastPage" => $lastPage,
            ]);
        }

        return $response->json();
    }
}