<?php

namespace App\Http\Controllers;

use App\Movie;
use App\Item;
use App\Watchlist;
use App\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Genre;

class MoviesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $movies = Movie::sortable()->paginate();

        return view('movies.index', ['movies' => $movies]);
    }

    public function genreSelect(Request $request)
    {
        $genreId = $request->input('genre') ? $request->input('genre') : 1;

        $genres = Genre::all();

        $items = Item::whereHas('genres', function ($query) use ($genreId) {
            $query->where('genre_id', '=', $genreId);
        })
        ->where('type', 'movie')
        ->get();

        $movies = [];

        foreach ($items as $item) {
            array_push($movies, Movie::find($item->id));
        }

        return view('categories', [
            'movies' => $movies,
            'genres' => $genres
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function show(Movie $movie)
    {
        $id = $movie->item_id;
        $watchlist = Watchlist::where('user_id', '=', Auth::user()->id)
        ->where('item_id', '=', $id)->first();
        $movie = Movie::find($id);
        $reviews = Review::orderBy('reviews.created_at', 'desc')
            ->where('reviews.item_id', $id)
            ->join('users', 'author_id', '=', 'users.id')
            ->join('movies', 'reviews.item_id', '=', 'movies.item_id')
            ->limit(2)->get(['reviews.*', 'users.name', 'movies.poster', 'reviews.rating AS review_rating']);
        
        $item = Item::find($id)
                ->leftJoin('actor_character_item', 'id', '=', 'actor_character_item.item_id')
                ->leftJoin('characters as character', 'character.id', '=', 'actor_character_item.character_id')
                ->leftJoin('people as actor', 'actor.id', '=', 'actor_character_item.person_id')
                ->where('item_id', $id)
                ->get();
                
        return view('movies.show', ['movie'=>$movie, 'item'=>$item, 'reviews'=>$reviews, 'watchlist' => $watchlist]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Movie $movie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function destroy(Movie $movie)
    {
        //
    }
}
