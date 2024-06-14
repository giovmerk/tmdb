<?php
#SLIM

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
require_once('./Database.php');

$app = AppFactory::create();

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello");
    return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->get('/3/discover/movie', function (Request $request, Response $response, $args) { //function movie 1st ok
    $queryparams = $request->getQueryParams();

    $tmdb = new database();

    $table = "movies m 
               INNER JOIN 
               6movies_movie_genres mmg
               on m.movie_id = mmg.movie_id
               INNER JOIN 
               movie_genres mg
               on mmg.genre_id=mg.movie_genre_id";

    if ($queryparams !== null) {
        $language = $queryparams['language'];
        $language = $tmdb->real_escape_string($language);
        $page = $queryparams['page'];
        $condition = "language=$language";
        
        $results = $tmdb->read("m.*, mg.name", $table, $condition, $page);
    } 
    else 
    {
        $results = $tmdb->read("m.*, mg.name", $table, null, null);
    }

    $genre_ids = [];

    foreach ($results as $entry) {
        $movie_id = $entry['movie_id'];

        if (!isset($genre_ids[$movie_id])) 
        {
            $genre_ids[$movie_id] = $entry;
            $genre_ids[$movie_id]['genre_ids'] = [];
        }
        
        $genre_ids[$movie_id]['genre_ids'][] = $entry['name'];
    }

    $genre_ids = array_values($genre_ids);

    echo json_encode($genre_ids, JSON_PRETTY_PRINT);

    $test = ["page"=>$page, "results"=>$results];
    $payload = json_encode($test);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/3/discover/tv', function (Request $request, Response $response, $args) { //function movie 1st ok
    $queryparams = $request->getQueryParams();
    
    $tmdb = new database();

    $table = "tv_series s 
                INNER JOIN 
                5series_series_genres ssg
                on s.series_id = ssg.series_id
                INNER JOIN 
                series_genres sg
                on ssg.genre_id=sg.series_genre_id";

    if ($queryparams !== null) {
        $language = $queryparams['language'];
        $language = $tmdb->real_escape_string($language);
        $page = $queryparams['page'];
        $condition = "language=$language";
        
        $results = $tmdb->read("s.*, sg.name", $table, $condition, $page);
    } 
    else 
    {
        $results = $tmdb->read("s.*, sg.name", $table, null, null);
    }

    $genre_ids = [];

    foreach ($results as $entry) {
        $series_id = $entry['series_id'];

        if (!isset($genre_ids[$series_id])) 
        {
            $genre_ids[$series_id] = $entry;
            $genre_ids[$series_id]['genre_ids'] = [];
        }
        
        $genre_ids[$series_id]['genre_ids'][] = $entry['name'];
    }

    $genre_ids = array_values($genre_ids);

    echo json_encode($genre_ids, JSON_PRETTY_PRINT);

    $test = ["page"=>$page, "results"=>$results];
    $payload = json_encode($test);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/3/genre/movie/list', function (Request $request, Response $response, $args) { //function movie 1st ok
    $queryparams = $request->getQueryParams();

    $tmdb = new database();
   
    if ($queryparams !== null) 
    {
         $page = $queryparams['page'];
    
        $results = $tmdb->read("*", "movie_genres", null, $page);
    } 
    else 
    {
        $results = $tmdb->read("*", "movie_genres", null, null);
    }

    $payload = json_encode($results);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/3/genre/tv/list', function (Request $request, Response $response, $args) { //function movie 1st fok
    $queryparams = $request->getQueryParams();

    $tmdb = new database();

    
    if ($queryparams !== null) 
    {
        $page = $queryparams['page'];
        $results = $tmdb->read("*", "series_genres", null, $page);
    } 
    else 
    {
    
        $results = $tmdb->read("*", "series_genres", null, null);
    }

    $payload = json_encode($results);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});


$app->get('/3/movie/{movie_id}/credits', function (Request $request, Response $response, $args) { //attori dei film fix page ok
    $queryparams = $request->getQueryParams();

    $tmdb = new database();

    $table = "movies m
                INNER JOIN
                2movies_actors ma
                ON m.movie_id=ma.movie_id
                INNER JOIN
                actors a
                ON a.actor_id=ma.actor_id";

    if ($queryparams !== null) {
        $id = $args['movie_id'];
        $condition = "ma.movie_id=$id";
    
        $data = $tmdb->read("m.movie_id, a.*", $table, $condition, null);
    } 
    else 
    {
        $data = $tmdb->read("m.movie_id, a.*", $table, null, null);
    }

    $cast = [];

    foreach ($data as $entry) {
        $movie_id = $entry['movie_id'];

        if (!isset($cast[$movie_id])) 
        {
            $cast[$movie_id]['id'] = $movie_id;
            $cast[$movie_id]['cast'] = [];

        }
        unset($entry['movie_id']);
        $cast[$movie_id]['cast'][] = $entry;
    }

    $cast = array_values($cast);
    
    $payload = json_encode($cast);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/3/tv/{series_id}/credits', function (Request $request, Response $response, $args) { //attori delle serie fix page
    $queryparams = $request->getQueryParams();

    $tmdb = new database();

    $table = "tv_series s
                INNER JOIN
                1series_actors sa
                ON s.series_id=sa.series_id
                INNER JOIN
                actors a
                ON a.actor_id=sa.actor_id";

    if ($queryparams !== null) {
        $id = $args['series_id'];
        $condition = "sa.series_id=$id";
    
        $data = $tmdb->read("s.series_id, a.*", $table, $condition, null);
    } 
    else 
    {
        $data = $tmdb->read("s.series_id, a.*", $table, null, null);
    }

    $cast = [];

    foreach ($data as $entry) {
        $series_id = $entry['series_id'];

        if (!isset($cast[$series_id])) 
        {
            $cast[$series_id]['id'] = $series_id;
            $cast[$series_id]['cast'] = [];

        }
        unset($entry['series_id']);
        $cast[$series_id]['cast'][] = $entry;
    }

    $cast = array_values($cast);
    // echo json_encode($cast, JSON_PRETTY_PRINT);

    $payload = json_encode($cast);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');    
});

$app->get('/3/tv/{series_id}/reviews', function (Request $request, Response $response, array $args) {  
    $queryparams = $request->getQueryParams();

    $tmdb = new database();
    
    if ($queryparams !== null) {
        //$page = $queryparams['page'];
        $id = $args['series_id'];
        $condition = "series_id=$id";
    
        $data = $tmdb->read("*", 'series_reviews', $condition, null);
    } 
    else 
    {
        $data = $tmdb->read("*", 'series_reviews', null, null);
    }

    $feed = ['id'=> $id, 'feed'=>[]];
    // var_export($data);


    foreach ($data as $entry) {
        unset($entry['series_id']);
        $feed['feed'][] = $entry;
    }

    $payload = json_encode($feed);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');    
});

$app->get('/3/movie/{movie_id}/reviews', function (Request $request, Response $response, array $args) {  
    $queryparams = $request->getQueryParams();

    $tmdb = new database();
    
    if ($queryparams !== null) {
       // $page = $queryparams['page'];
        $id = $args['movie_id'];
        $condition = "movie_id=$id";
    
        $data = $tmdb->read("*", 'movie_reviews', $condition, null);
    } 
    else 
    {
        $data = $tmdb->read("*", 'movie_reviews', null, null);
    }

    $feed = ['id'=> $id, 'feed'=>[]];
    // var_export($data);


    foreach ($data as $entry) {
        $movie_id = $entry['movie_id'];

        unset($entry['series_id']);
        $feed[$movie_id]['feed'][] = $entry;
    }



    $feed = array_values($feed);
    
    $payload = json_encode($feed);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');    
});


$app->run();

#GUZZLE

// use Psr\Http\Message\RequestInterface;
// use Psr\Http\Message\ResponseInterface;
// use Psr\Http\Message\UriInterface;

// $onRedirect = function(
//     RequestInterface $request,
//     ResponseInterface $response,
//     UriInterface $uri
// ) {
//     echo 'Redirecting! ' . $request->getUri() . ' to ' . $uri . "\n";
// };

// $res = $client->request('GET', '/redirect/3', [
//     'allow_redirects' => [
//         'max'             => 10,        // allow at most 10 redirects.
//         'strict'          => true,      // use "strict" RFC compliant redirects.
//         'referer'         => true,      // add a Referer header
//         'protocols'       => ['https'], // only allow https URLs
//         'on_redirect'     => $onRedirect,
//         'track_redirects' => true
//     ]
// ]);

// echo $res->getStatusCode();

// echo $res->getHeaderLine('X-Guzzle-Redirect-History');

// echo $res->getHeaderLine('X-Guzzle-Redirect-Status-History');

// echo 'ciao git';
