<?php

namespace App\Http\Controllers;

use App\Services\OMDbService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use App\Models\Rating;
use App\Http\Requests\RateMovieRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\MovieDownloadCode;
use Illuminate\Support\Facades\Cache;

class OMDbController extends Controller
{

	use ResponseTrait;

    protected $omdbService;
 
    public function __construct(OMDbService $omdbService) {
        $this->omdbService = $omdbService;
    }

    public function movies(Request $request) {
        
        return $this->omdbService->searchMovies($request->search, $request->page);
    }

    public function rate(RateMovieRequest $request) {
        
        $rating = Rating::updateOrCreate(
            ["user_id" => $request->user()->id,"imdb_id" => $request->imdb_id], 
            ["rate" => $request->rate]
        );

        return $this->responseSuccess($rating);
    }

    public function download(Request $request) {
        
        if ($request->code) {
            if (Cache::has('user'.$request->user()->id)) {
                if (Cache::get('user'.$request->user()->id) == $request->code) {
                    return response()->file(storage_path('app/movies/sample.mp4'),[
                        'Content-Type' => 'video/mp4',
                        'Content-Disposition' => 'inline; filename="Sample"'
                    ]);
                }
            }
        }

        return $this->responseError('Invalid Code');
    }

    public function sendCode(Request $request) {

        $code = rand(100000,999999);
        $user = $request->user();
        Cache::put('user'.$user->id, $code, now()->addMinutes(5));
        Mail::to($user)->send(new MovieDownloadCode($code));
    }
}
