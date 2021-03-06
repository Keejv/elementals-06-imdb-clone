<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\MovieRequest as StoreRequest;
use App\Http\Requests\MovieRequest as UpdateRequest;
use DB;

class MovieCrudController extends CrudController
{
    public function setup()
    {

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Movie');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/movie');
        $this->crud->setEntityNameStrings('movie', 'movies');

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */

        $this->crud->setFromDb();

        // ------ CRUD FIELDS
        // $this->crud->addField($options, 'update/create/both');
        // $this->crud->addFields($array_of_arrays, 'update/create/both');
        // $this->crud->removeField('name', 'update/create/both');
        // $this->crud->removeFields($array_of_names, 'update/create/both');

        // ------ CRUD COLUMNS
        // $this->crud->addColumn(); // add a single column, at the end of the stack
        // $this->crud->addColumns(); // add multiple columns, at the end of the stack
        // $this->crud->removeColumn('column_name'); // remove a column from the stack
        // $this->crud->removeColumns(['column_name_1', 'column_name_2']); // remove an array of columns from the stack
        // $this->crud->setColumnDetails('column_name', ['attribute' => 'value']); // adjusts the properties of the passed in column (by name)
        // $this->crud->setColumnsDetails(['column_1', 'column_2'], ['attribute' => 'value']);

        // ------ CRUD BUTTONS
        // possible positions: 'beginning' and 'end'; defaults to 'beginning' for the 'line' stack, 'end' for the others;
        // $this->crud->addButton($stack, $name, $type, $content, $position); // add a button; possible types are: view, model_function
        // $this->crud->addButtonFromModelFunction($stack, $name, $model_function_name, $position); // add a button whose HTML is returned by a method in the CRUD model
        // $this->crud->addButtonFromView($stack, $name, $view, $position); // add a button whose HTML is in a view placed at resources\views\vendor\backpack\crud\buttons
        // $this->crud->removeButton($name);
        // $this->crud->removeButtonFromStack($name, $stack);
        // $this->crud->removeAllButtons();
        // $this->crud->removeAllButtonsFromStack('line');

        // ------ CRUD ACCESS
        // $this->crud->allowAccess(['list', 'create', 'update', 'reorder', 'delete']);
        // $this->crud->denyAccess(['list', 'create', 'update', 'reorder', 'delete']);

        // ------ CRUD REORDER
        // $this->crud->enableReorder('label_name', MAX_TREE_LEVEL);
        // NOTE: you also need to do allow access to the right users: $this->crud->allowAccess('reorder');

        // ------ CRUD DETAILS ROW
        // $this->crud->enableDetailsRow();
        // NOTE: you also need to do allow access to the right users: $this->crud->allowAccess('details_row');
        // NOTE: you also need to do overwrite the showDetailsRow($id) method in your EntityCrudController to show whatever you'd like in the details row OR overwrite the views/backpack/crud/details_row.blade.php

        // ------ REVISIONS
        // You also need to use \Venturecraft\Revisionable\RevisionableTrait;
        // Please check out: https://laravel-backpack.readme.io/docs/crud#revisions
        // $this->crud->allowAccess('revisions');

        // ------ AJAX TABLE VIEW
        // Please note the drawbacks of this though:
        // - 1-n and n-n columns are not searchable
        // - date and datetime columns won't be sortable anymore
        // $this->crud->enableAjaxTable();

        // ------ DATATABLE EXPORT BUTTONS
        // Show export to PDF, CSV, XLS and Print buttons on the table view.
        // Does not work well with AJAX datatables.
        // $this->crud->enableExportButtons();

        // ------ ADVANCED QUERIES
        // $this->crud->addClause('active');
        // $this->crud->addClause('type', 'car');
        // $this->crud->addClause('where', 'name', '==', 'car');
        // $this->crud->addClause('whereName', 'car');
        // $this->crud->addClause('whereHas', 'posts', function($query) {
        //     $query->activePosts();
        // });
        // $this->crud->addClause('withoutGlobalScopes');
        // $this->crud->addClause('withoutGlobalScope', VisibleScope::class);
        // $this->crud->with(); // eager load relationships
        // $this->crud->orderBy();
        // $this->crud->groupBy();
        // $this->crud->limit();
    }

    public function store(StoreRequest $request)
    { 

        $curl = curl_init();
             //array of movies
             
            $parsedTitle = str_replace(" ", "+", $request->title);

             $movies = [
                 $parsedTitle
             ];

            // Get latest insert from items table and set $i to this number. 
             if (DB::select('SELECT id FROM items ORDER BY id DESC LIMIT 1')) {
                 $id = DB::select('SELECT id FROM items ORDER BY id DESC LIMIT 1');
                 $i = $id[0]->id;
                 $i++;
             } else {
                 $i = 1;
             }

            foreach($movies as $movie) {
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://www.omdbapi.com/?t=$movie&plot=full&apikey=8ea32694",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_TIMEOUT => 6000000,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                    ),
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
        
                if ($err) {
                    echo "cURL Error #:" . $err;
                } else { 
                    $obj = json_decode($response);
                    $imdbID = $obj->imdbID;
                    //new request to new API themoviedb using the imdbID that we got from OMDB to get the backdrop for our movies
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.themoviedb.org/3/movie/$imdbID?api_key=cdc32d79384ddc6326eff808e85db1c7",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_TIMEOUT => 6000000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                        ),
                    ));
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                
                    if ($err) {
                        echo "cURL Error #:" . $err;
                    } else { 
                        $movieBackdrop = json_decode($response);

                        if(!isset($movieBackdrop->backdrop_path)) {
                            return;
                        }
        
                        curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.themoviedb.org/3/movie/$imdbID/credits?api_key=ec3cda1b6d80802d7b2222e300f2f846",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_TIMEOUT => 6000000,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                        ),
                    ));
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
            
                    if ($err) {
                        echo "cURL Error #:" . $err;
                    } else {
                        $movie_credits = json_decode($response);
                        $backdrop_url = "http://image.tmdb.org/t/p/w1280";
                    //inserting content of people in db
                    $query = DB::table('movies')->select('title')->where('title', '=', $obj->Title)->get();
                    if(!isset($query[0])){
                        DB::table('items')->insert([
                            'type' => 'movie'
                        ]);
                        DB::table('movies')->insert([
                            'item_id' => $i,
                            'title'=>$obj->Title,
                            'summary'=>$obj->Plot,
                            'release_date'=>date('Y-m-d', strtotime($obj->Released)),
                            'runtime'=>$obj->Runtime,
                            'rating'=>$obj->imdbRating,
                            'poster'=>$obj->Poster,
                            'countries'=>$obj->Country,
                            'imdbID'=>$obj->imdbID,
                            'movieBackdrop'=> $backdrop_url . $movieBackdrop->backdrop_path
                            ]);
                        } else {
                            echo $input . ' already exists in database!';
                            echo (PHP_EOL);
                            return;
                        }
                    //getting the genres of the film, exploiting it and storing in databse
                    $genres = explode(", ", $obj->Genre);
                    //storing the content of movie into database
                    foreach ($genres as $genre) {
                        //just inserting the genre titles we got into the table genres
                        $query = DB::table('genres')->select('genre_title')->where('genre_title', '=', $genre)->get();
                        if(!isset($query[0])) {
                            DB::table('genres')->insert([
                                'genre_title' => $genre
                                ]);
        
                        }
                        //here we are making the connection with pivot table, so it connects the movie and the genres
                        $query = DB::table('genres')->select('id')->where('genre_title', '=', $genre)->get();
                        if(isset($query[0])){
                            $queryPivot = DB::table('genre_item')->select('item_id')->where('item_id', '=', $i)->
                            where('genre_id', '=', $query[0]->id)->get();
                        }
        
                        if(!isset($queryPivot[0])){
                            DB::table('genre_item')->insert([
                                'item_id' => $i,
                                'genre_id' => $query[0]->id
                            ]);
                        }
                    }
                        
                   $profile_url = "http://image.tmdb.org/t/p/w185";
                   $cast_i = 0;
                    foreach($movie_credits->cast as $index => $actors) {
                        $cast_i ++;
                            if($cast_i >= 7){
                                break;
                            }
                        $actor = $actors->name;
                        $character = $actors->character;
                        //inserting actor content into people table, storing name, date of birth and city
                        $query = DB::table('people')->select('name')->where('name', '=', $actor)->get();
                        if(!isset($query[0])){
                            $prof_pic = $movie_credits->cast[$index]->profile_path;
                            DB::table('people')->insert([
                                'name' => $actor,
                                'dob' => date('Y-m-d'),
                                'city' => 'random',
                                'profile_pic' => $profile_url . $prof_pic
                            ]);
                        }

                        $query_character = DB::table('characters')->select('character')->where('character', '=', $character)->get();
                            DB::table('characters')->insert([
                                'character' => $character
                            ]);
                        $query_character = DB::table('characters')->select('id')->latest('id')->get();

                        //inserting movie id and person id(actor) in our pivot table
                        $query = DB::table('people')->select('id')->where('name', '=', $actor)->get();
                        if(isset($query[0])){
                            $queryPivot = DB::table('actor_character_item')->select('person_id')->where('item_id', '=', $i)->
                            where('person_id', '=', $query[0]->id)->where('character_id', '=', $query[0]->id)->get();
                        }
                        if(!isset($queryPivot[0])){
                            DB::table('actor_character_item')->insert([
                                'item_id' => $i,
                                'person_id' => $query[0]->id,
                                'character_id' => $query_character[0]->id
                            ]);
                        }
                    } 
        
                    //$directors = explode(", ", $obj->Director);
                    $cast_i = 0;
                    foreach($movie_credits->crew as $index => $director){
                        $cast_i ++;
                            if($cast_i >= 2){
                                break;
                            }
                        $director = $director->name;
                        /* inserting content of people in database */

                        $query = DB::table('people')->select('name')->where('name', '=', $director)->get();
                        //if we have the data in table rows then it will not store it anymore(no duplicates)
                        if(!isset($query[0])){
                            $director_pic = $movie_credits->crew[$index]->profile_path;
                            DB::table('people')->insert([
                                'name' => $director,
                                'dob' => date('Y-m-d'),
                                'city' => 'random',
                                'profile_pic' => $profile_url . $director_pic
                            ]);
                        }
                        //inserting the id of movie and the id of person(in this case director) into Database
                        $query = DB::table('people')->select('id')->where('name', '=', $director)->get();
                        if(isset($query[0])){
                            $queryPivot = DB::table('director_item')->select('person_id')->where('item_id', '=', $i)->
                            where('person_id', '=', $query[0]->id)->get();
                        }
                        //if we have the data in table rows then it will not store it anymore(no duplicates)
                        if(!isset($queryPivot[0])){
                            DB::table('director_item')->insert([
                                'item_id' => $i,
                                'person_id' => $query[0]->id
                            ]);
                        }
                    }
                
                        }
                    }
                
                }
            }
            curl_close($curl);
            
        echo (PHP_EOL);
        echo '##############################';
        echo (PHP_EOL); 
        echo 'movie added successfully';
        echo (PHP_EOL); 
        echo '##############################';
        echo (PHP_EOL);

        return redirect('admin/movie');
    }

    public function update(UpdateRequest $request)
    {
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }
}
