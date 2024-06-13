<?php
#dichiaro la classe database e dichiaro delle funzioni all'interno
class Database
{
    private $host = '127.0.0.1';
    private $db_name = 'TMDB';
    private $username = 'root';
    private $password = 'root';
    private $port = 3306;
    public $conn;

    public function __construct()
    {
		$conn = null;
		try
		{
			$this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
		}
		catch(PDOException $exception)
		{
			echo "Errore di connessione: " . $exception->getMessage();
		}

	}

    public function read($param, $table, $condition) #manda a schermo (utilizzabile come select)
    {
        $query = "SELECT $param FROM $table";
        if ($condition !== null) {
            // $query = $query . " WHERE $condition";
            $query .= " WHERE $condition";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        #var_export manda a schermo in maniera semplificata, fetchAll trasforma l'output in json, PDO::FETCH_ASSOC associa i valori e li rende univoci
        // var_export($stmt->fetchAll(PDO::FETCH_ASSOC));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function create($table, $param, $data) #inserisce dei datti all'interno di un database (utilizzabile come insert)
    {
        $string = implode(', ', $data);
        // $string = mysql_escape_string($string);
        $query = "INSERT IGNORE INTO $table($param) VALUES ($string)";
        echo $query;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        // var_export($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function update($table, $data, $condition) #aggiorna dei datti all'interno di un database (utilizzabile come update)
    {
        $query = `UPDATE $table SET $data`;
        if ($condition!==null)
        {
            $query = $query . `WHERE $condition`;
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        var_export($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function delete($param, $table, $condition) #cancewlla dei datti all'interno di un database (utilizzabile come delete)
    {
        $query = `DELETE $param FROM $table`;
        if ($condition!==null)
        {
            $query = $query . `WHERE $condition`;
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        var_export($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

   
    public function real_escape_string($string) {
        return $this->conn->quote($string);
    }

}

$tmdb = new database(); #dichiaro esistenza di un database

require_once('vendor/autoload.php');

$client = new \GuzzleHttp\Client();#dichiaro GUZZLE

for($i=1; $i<=5; ++$i) #film e relazioni con i generi
{
$response = $client->request('GET', "https://api.themoviedb.org/3/discover/movie?language=en-US&page=" . $i, [ //movie abbiamo usato le pagine
    'headers' => [
        'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
        'accept' => 'application/json',
    ],
    ]);


    $jsonobj = $response->getBody();

    $obj = json_decode($jsonobj);

    $obj = $obj->results;


    foreach($obj as $key => $value) 
    {
        $movie_id = $value->id;

        $title = $value->title;
        $title = '\'' . $title . '\'';

        $overview = $value->overview;
        $overview = str_replace(array("'", "\""), " ", $overview);
        $overview = "\"" . $overview . "\"" ;


        $release_date = $value->release_date;
        $release_date = '\'' . $release_date . '\'';

        $vote_average = $value->vote_average;

        $backdrop_path = $value->backdrop_path;
        $backdrop_path = '\'' . $backdrop_path . '\'';

        $genre_ids = $value->genre_ids;

        $poster_path = $value->poster_path;
        $poster_path = '\'' . $poster_path . '\'';

        $language = $value->original_language;
        $language = '\'' . $language . '\'';

        $adult = $value -> adult;
        if($adult === false)
        {
            $adult = 0;
        }
        else{
            $adult = 1;
        }
        #$tmdb -> create('movies',
        #'movie_id, title, overview, release_date, vote_average, backdrop_path, poster_path, language, adult',
        # [$movie_id, $title, $overview, $release_date, $vote_average, $backdrop_path, $poster_path, $language, $adult]);




        // foreach ($genre_ids as $key => $genre_id) //movies_movie_genres
        // {
        //     $tmdb->create('6movies_movie_genres',
        //    'movie_id, genre_id',
        //   [$movie_id, $genre_id]);
        // }
    }
}

for($i=1; $i<=5; ++$i) #serie e relazioni con i generi
{
    $response = $client->request('GET', 'https://api.themoviedb.org/3/discover/tv?language=en-US&page=' . $i, [
        'headers' => [
          'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
          'accept' => 'application/json',
        ],
    ]);


    $jsonobj = $response->getBody();

    $obj = json_decode($jsonobj);

    $obj = $obj->results;


    foreach($obj as $key => $value) #adatto i dati alla query
    {
        $series_id = $value -> id;

        $name = $value -> name;
        $name = '"' . $name . '"';

        $first_air_date = $value -> first_air_date;
        $first_air_date = '"' . $first_air_date . '"';

        $backdrop_path = $value -> backdrop_path;
        $backdrop_path = '"' . $backdrop_path . '"';

        $poster_path = $value -> poster_path;
        $poster_path = '"' . $poster_path . '"';

        $language = $value -> original_language;
        $language = '"' . $language . '"';

        $genre_ids = $value -> genre_ids;

        $adult = $value -> adult;
        if($adult === false)
        {
            $adult = 0;
        }
        else{
            $adult = 1;
        }
        #$tmdb -> create('tv_series',
        #'series_id, name, first_air_date, backdrop_path, poster_path, language, adult',
        # [$series_id, $name, $first_air_date, $backdrop_path, $poster_path, $language, $adult]);

        #foreach ($genre_ids as $key => $genre_id) //movies_movie_genres
        #{
        #    $tmdb->create('5series_series_genres',
        #    'series_id, genre_id',
        #    [$series_id, $genre_id]);
        #}
    }

}

//generi dei film
$response = $client->request('GET', 'https://api.themoviedb.org/3/genre/movie/list?language=en', [ //FATTA API
    'headers' => [
      'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
      'accept' => 'application/json',
    ],
]);


$jsonobj = $response->getBody();

$obj = json_decode($jsonobj);

$obj = $obj->genres;


foreach($obj as $key => $value) 
{
    $movie_genre_id = $value -> id;

    $name = $value -> name;
    $name = '"' . $name . '"';

    #$tmdb -> create('movie_genres',
    #'movie_genre_id, name',
    # [$movie_genre_id, $name]);
}

//generi delle serie
$response = $client->request('GET', 'https://api.themoviedb.org/3/genre/tv/list?language=en', [ //FATTA API
    'headers' => [
      'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
      'accept' => 'application/json',
    ],
]);


$jsonobj = $response->getBody();

$obj = json_decode($jsonobj);

$obj = $obj->genres;

foreach($obj as $key => $value) #adatto i dati alla query
{
    $series_genre_id = $value -> id;

    $name = $value -> name;
    $name = '"' . $name . '"';

    // $tmdb -> create('series_genres',
    // 'series_genre_id, name',
    // [$series_genre_id, $name]);// tabella
}

$mResults = $tmdb->read('movie_id', 'movies', null); //company movie
// var_export($mResults);

foreach($mResults as $movie_id)
{       
    $movie = $movie_id['movie_id'];
    
    $response = $client->request('GET', "https://api.themoviedb.org/3/movie/$movie?language=en-US", [
        'headers' => [
          'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
          'accept' => 'application/json',
        ],
      ]);

    $jsonobj = $response->getBody();

    $obj = json_decode($jsonobj);

    $production_companies = $obj->production_companies;

    

    // foreach($production_companies as $key => $value)
    // {
    //     $company_id = $value -> id;

    //     $origin_country = $tmdb->real_escape_string($value -> origin_country);

    //     $logo_path = $tmdb->real_escape_string($value -> logo_path);
        
    //     $name = $tmdb->real_escape_string($value -> name);
        
    //     $tmdb->create('company',
    //     'company_id, origin_country, logo_path, name',
    //     [$company_id, $origin_country, $logo_path, $name]);
    // }

    // foreach($production_companies as $key => $value)
    // {
    //     $tmdb->create('8movies_company',
    //     'movie_id, company_id',
    //     [$movie, $value->id]);
    // }
}

$sResults = $tmdb->read('series_id', 'tv_series', null); //company series
// var_export($sResults);

foreach($sResults as $series_id)
{       
    $series = $series_id['series_id'];

    $response = $client->request('GET', "https://api.themoviedb.org/3/tv/$series?language=en-US", [
        'headers' => [
          'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
          'accept' => 'application/json',
        ],
      ]);

    $jsonobj = $response->getBody();

    $obj = json_decode($jsonobj);

    $production_companies = $obj->production_companies;

    

    // foreach($production_companies as $key => $value)
    // {
    //     $company_id = $value -> id;

    //     $origin_country = $tmdb->real_escape_string($value -> origin_country);

    //     $logo_path = $tmdb->real_escape_string($value -> logo_path);
        
    //     $name = $tmdb->real_escape_string($value -> name);
        
    //     $tmdb->create('company',
    //     'company_id, origin_country, logo_path, name',
    //     [$company_id, $origin_country, $logo_path, $name]);

    //     $tmdb->create('7series_company',
    //     'series_id, company_id',
    //     [$series, $value->id]);
    // }
}

$results = $tmdb->read('movie_id', 'movies', null);
// var_export($results);

foreach($results as $movie_id) //actors
{       
    $movie = $movie_id['movie_id'];
    // var_export($movie_id);
    $response = $client->request('GET', "https://api.themoviedb.org/3/movie/$movie/credits?language=en-US", [
        'headers' => [
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
            'accept' => 'application/json',
        ],
    ]);

    $jsonobj = $response->getBody();

    $obj = json_decode($jsonobj);

    $actors = $obj -> cast;

    // foreach($actors as $key => $actor)
    // {
    //     $name = $tmdb -> real_escape_string($actor -> name);
    //     $profile_path = $tmdb -> real_escape_string($actor -> profile_path); 
        
    //     $tmdb->create('actors',
    //     'actor_id, name, profile_path',
    //     [$actor -> id, $name, $profile_path]);
    // }

    // foreach($actors as $key => $actor)
    // {
    //     $tmdb->create('2movies_actors',
    //     'actor_id, movie_id',
    //     [$actor -> id, $i]);
    // }
}

$results = $tmdb->read('series_id', 'tv_series', null);
// var_export($results);

foreach($results as $series_id)
{       
    $series = $series_id['series_id'];
    // var_export($series_id);
    $response = $client->request('GET', "https://api.themoviedb.org/3/tv/$series/credits?language=en-US", [
        'headers' => [
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
            'accept' => 'application/json',
        ],
    ]);

    $jsonobj = $response->getBody();

    $obj = json_decode($jsonobj);

    $actors = $obj -> cast;

    // foreach($actors as $key => $actor)
    // {
    //     $name = $tmdb -> real_escape_string($actor -> name);
    //     $profile_path = $tmdb -> real_escape_string($actor -> profile_path); 
        
    //     $tmdb->create('actors',
    //     'actor_id, name, profile_path',
    //     [$actor -> id, $name, $profile_path]);
    // }

    // foreach($actors as $key => $actor)
    // {
    //     $tmdb->create('2movies_actors',
    //     'actor_id, movie_id',
    //     [$actor -> id, $i]);
    // }
}

foreach($mResults as $movie_id)
{
    $movie = $movie_id['movie_id'];

    $response = $client->request('GET', "https://api.themoviedb.org/3/movie/$movie/reviews?language=en-US&page=1", [
        'headers' => [
          'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
          'accept' => 'application/json',
        ],
    ]);
    
    $jsonobj = $response->getBody();

    $obj = json_decode($jsonobj);

    $data = $obj -> results;

    // foreach($data as $key => $value)
    // {
    //     $username = $tmdb->real_escape_string($value->author_details->username);

    //     $rating = $value->author_details->rating;
    //     if($rating === null)
    //     {
    //         $rating = 6;
    //     }

    //     $content = $tmdb->real_escape_string($value->content);
    //     $content = str_replace(["(",")"], " ", $content);

    //     $created_at = $tmdb->real_escape_string($value->created_at);

    //     $tmdb->create('movie_reviews', 
    //             'username, rating, content, created_at, movie_id',  
    //             [$username, $rating, $content, $created_at, $movie]);
    // }
}

foreach($sResults as $series_id)
{
    $series = $series_id['series_id'];

    $response = $client->request('GET', "https://api.themoviedb.org/3/tv/$series/reviews?language=en-US&page=1", [
        'headers' => [
          'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
          'accept' => 'application/json',
        ],
      ]);
    
    $jsonobj = $response->getBody();

    $obj = json_decode($jsonobj);

    $data = $obj -> results;

    // foreach($data as $key => $value)
    // {
    //     $username = $tmdb->real_escape_string($value->author_details->username);

    //     $rating = $value->author_details->rating;
    //     if($rating === null)
    //     {
    //         $rating = 6;
    //     }

    //     $content = $tmdb->real_escape_string($value->content);
    //     $content = str_replace(["(",")"], " ", $content);

    //     $created_at = $tmdb->real_escape_string($value->created_at);

    //     $tmdb->create('series_reviews', 
    //             'username, rating, content, created_at, series_id',  
    //             [$username, $rating, $content, $created_at, $series]);
    // }
}
?>