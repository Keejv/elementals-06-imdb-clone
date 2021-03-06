<?php

namespace App\Http\Controllers;

use App\Movie;
use App\Item;
use App\Review;
use App\Tvshow;
use \Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // Get X movies with highest rating
        $featured = Movie::orderBy('rating', 'desc')->limit(3)->get();

        foreach($featured as $feature) {
            $item[] = Item::find($feature->item_id);
        }
  
        $spotlightMovies = Movie::orderBy('movies.created_at', 'desc')
        ->join('items', 'movies.item_id', '=', 'items.id')            
        ->limit(5)->get();

        $spotlightRated = Movie::orderBy('rating', 'desc')
        ->join('items', 'movies.item_id', '=', 'items.id')            
        ->limit(5)->get();

        $spotlightTv = Tvshow::orderBy('rating', 'desc')
        ->join('items', 'tvshows.item_id', '=', 'items.id')    
        ->limit(5)->get();
        
        $spotlights = [
            'movies' => $spotlightMovies,
            'rated' => $spotlightRated,
            'tvshows' => $spotlightTv
        ];

        $reviews = Review::orderBy('reviews.created_at', 'desc')
            ->join('users', 'author_id', '=', 'users.id')
            ->join('movies', 'reviews.item_id', '=', 'movies.item_id')
            ->limit(8)->get(['reviews.*', 'users.name', 'movies.poster', 'reviews.rating AS review_rating']);

        return view('home', [
            'featured' => $featured, 
            'item' => $item, 
            'reviews' => $reviews,
            'spotlights' => $spotlights]);
    }

    public function splash() 
    {
        if(null !== Auth::user()) {
            return $this->index();
        }

        return view('splash');
    }
}
