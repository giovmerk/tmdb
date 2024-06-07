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
        if ($condition!==null)
        {
            $query = $query . "WHERE $condition";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        #var_export manda a schermo in maniera semplificata, fetchAll trasforma l'output in json, PDO::FETCH_ASSOC associa i valori e li rende univoci
        var_export($stmt->fetchAll(PDO::FETCH_ASSOC));
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


for($i=0; $i<100; ++$i) //company
{

    try //alcuni id non esistono dunque si fa un try catch
    {
        $response = $client->request('GET', 'https://api.themoviedb.org/3/company/' . $i, [ //FATTA API
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
                'accept' => 'application/json',
            ],
        ]);


        $jsonobj = $response->getBody();

        $obj = json_decode($jsonobj);
    }
    catch (GuzzleHttp\Exception\ClientException $e)
    {
        //var_export($e);
    }

    //var_export($obj);

    // $company_id = $obj -> id;

    // $origin_country = $tmdb->real_escape_string($obj -> origin_country);
    
    // $logo_path = $tmdb->real_escape_string($obj -> logo_path);
    

    // $name = $tmdb->real_escape_string($obj -> name); 

    // $tmdb -> create('company',
    // 'company_id, origin_country, logo_path, name',
    // [$company_id, $origin_country, $logo_path, $name]);
}

for($i=1; $i<=100; ++$i) #tabella lists e relazioni con movie
{
    try //alcuni id non esistono dunque si fa un try catch
    {
        $response = $client->request('GET', "https://api.themoviedb.org/3/list/$i?language=en-US", [ //FATTA API X2
            'headers' => [
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
            'accept' => 'application/json',
            ],
        ]);
    }
    catch(GuzzleHttp\Exception\ClientException $e)
    {
        //var_export($e);
    }

    $jsonobj = $response->getBody();

    $obj = json_decode($jsonobj);

    $list_id = $obj -> id;

    $description = $tmdb->real_escape_string($obj -> description);

    $created_by = $tmdb->real_escape_string($obj -> created_by);

    $favourite_count = $obj -> favorite_count;

    $item_count = $obj -> item_count;

    $name = $tmdb->real_escape_string($obj -> name);

    $poster_path = $tmdb->real_escape_string($obj -> poster_path);



    $item_ids = $obj -> items;
    //var_export($item_ids);

    
    // $tmdb -> create('lists',
    // 'list_id, created_by, description, favourite_count, item_count, name, poster_path',
    // [$list_id, $created_by, $description, $favourite_count, $item_count, $name, $poster_path]);

    // foreach ($item_ids as $key => $item) //movies_series_lists
    // {
    //     $tmdb->create('4movies_lists',
    //     'movie_id, list_id',
    //     [$item->id, $list_id]);
    // }
}

for($i=1; $i<=100; ++$i) #relazione film aziende produttrici
{
    try
    {
        $response = $client->request('GET', "https://api.themoviedb.org/3/movie/$i?language=en-US", [
            'headers' => [
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
            'accept' => 'application/json',
            ],
        ]);
    }
    catch (GuzzleHttp\Exception\ClientException $e)
    {
       // var_export($e);
    }
    $jsonobj = $response->getBody();

    $obj = json_decode($jsonobj);

    $movie_id = $obj->id;

    $company_ids = $obj -> production_companies;

    // foreach($company_ids as $key => $company)
    // {
    //     $tmdb->create('8movies_company',
    //     'company_id, movie_id',
    //     [$company->id, $movie_id]);
    // }

}

for($i=1; $i<=100; ++$i) #relazione serie aziende produttrici
{
    try
    {
        
        $response = $client->request('GET', "https://api.themoviedb.org/3/tv/$i?language=en-US", [
            'headers' => [
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
            'accept' => 'application/json',
            ],
        ]);
    
    }
    catch (GuzzleHttp\Exception\ClientException $e)
    {
       // var_export($e);
    }
    $jsonobj = $response->getBody();

    $obj = json_decode($jsonobj);

    $series_id = $obj->id;

    $company_ids = $obj -> production_companies;

    // foreach($company_ids as $key => $company)
    // {
    //     $tmdb->create('7series_company',
    //     'company_id, series_id',
    //     [$company->id, $series_id]);
    // }

}
for($i=1; $i<=100; ++$i) #attori dei film
{
    try
    {
        
        $response = $client->request('GET', "https://api.themoviedb.org/3/movie/$i/credits?language=en-US", [
            'headers' => [
              'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
              'accept' => 'application/json',
            ],
        ]);

    }
    catch (GuzzleHttp\Exception\ClientException $e)
    {
        //var_export($e);
    }
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

for($i=1; $i<=100; ++$i) #attori delle serie
{
    try
    {
        
        $response = $client->request('GET', "https://api.themoviedb.org/3/tv/$i/credits?language=en-US", [
            'headers' => [
              'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
              'accept' => 'application/json',
            ],
          ]);

    }
    catch (GuzzleHttp\Exception\ClientException $e)
    {
        //var_export($e);
    }
    $jsonobj = $response->getBody();

    $obj = json_decode($jsonobj);

    $actors = $obj-> cast;

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
    //     $tmdb->create('1series_actors',
    //     'actor_id, series_id',
    //     [$actor -> id, $i]);
    // }

}
?>