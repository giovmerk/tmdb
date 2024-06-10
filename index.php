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

$app->get('/3/discover/movie', function (Request $request, Response $response, $args) { //function movie 1st
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
        
        $results = $tmdb->read("m.*, mg.movie_genre_id", $table, $condition, $page);
    } 
    else 
    {
        $results = $tmdb->read("m.*, mg.movie_genre_id", $table, null, null);
    }

    $genre_ids = [];

    foreach ($results as $entry) {
        $movie_id = $entry['movie_id'];

        if (!isset($genre_ids[$movie_id])) 
        {
            $genre_ids[$movie_id] = $entry;
            $genre_ids[$movie_id]['genre_ids'] = [];
        }
        
        $genre_ids[$movie_id]['genre_ids'][] = $entry['movie_genre_id'];
    }

    $genre_ids = array_values($genre_ids);

    echo json_encode($genre_ids, JSON_PRETTY_PRINT);

    $test = ["page"=>$page, "results"=>$results];
    $payload = json_encode($test);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/3/discover/tv', function (Request $request, Response $response, $args) { //function movie 1st
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
        
        $results = $tmdb->read("s.*, sg.series_genre_id", $table, $condition, $page);
    } 
    else 
    {
        $results = $tmdb->read("s.*, sg.series_genre_id", $table, null, null);
    }

    $genre_ids = [];

    foreach ($results as $entry) {
        $series_id = $entry['series_id'];

        if (!isset($genre_ids[$series_id])) 
        {
            $genre_ids[$series_id] = $entry;
            $genre_ids[$series_id]['genre_ids'] = [];
        }
        
        $genre_ids[$series_id]['genre_ids'][] = $entry['series_genre_id'];
    }

    $genre_ids = array_values($genre_ids);

    echo json_encode($genre_ids, JSON_PRETTY_PRINT);

    $test = ["page"=>$page, "results"=>$results];
    $payload = json_encode($test);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/3/discover/movie/moviesgenres', function (Request $request, Response $response, $args) { //function movie 1st
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

$app->get('/3/discover/tv/seriesgenres', function (Request $request, Response $response, $args) { //function movie 1st
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

$app->get('/3/genre/movie/list', function (Request $request, Response $response, $args) { //function movie 1st
    $queryparams = $request->getQueryParams();

    $tmdb = new database();

    if ($queryparams !== null) {
        $id = $queryparams['movie_genre_id'];
        $condition = "movie_genre_id=$id";
        if ($id !== null) {
            $data = $tmdb->read("*", "movie_genres", $condition, null);
        } else {
            $data = $tmdb->read("*", "movie_genres", null, null);
        }
    } else {
        $data = $tmdb->read("*", "movie_genres", null, null);
    }

    $payload = json_encode($data);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/3/genre/tv/list', function (Request $request, Response $response, $args) { //function movie 1st
    $queryparams = $request->getQueryParams();

    $tmdb = new database();

    if ($queryparams !== null) {
        $id = $queryparams['series_genre_id'];
        $condition = "series_genre_id=$id";
        if ($id !== null) {
            $data = $tmdb->read("*", "series_genres", $condition, null);
        } else {
            $data = $tmdb->read("*", "series_genres", null, null);
        }
    } else {
        $data = $tmdb->read("*", "series_genres", null, null);
    }

    $payload = json_encode($data);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/3/company', function (Request $request, Response $response, $args) { //function movie 1st
    $queryparams = $request->getQueryParams();

    $tmdb = new database();

    if ($queryparams !== null) {
        $page = $queryparams['page'];
        $id = $queryparams['company_id'];
        $condition = "company_id=$id";
        if ($id !== null) {
            $data = $tmdb->read("*", "company", $condition, $page);
        } else {
            $data = $tmdb->read("*", "company", null, $page);
        }
    } else {
        $data = $tmdb->read("*", "company", null, null);
    }

    $payload = json_encode($data);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/3/list', function (Request $request, Response $response, $args) { //function movie 1st
    $queryparams = $request->getQueryParams();

    $tmdb = new database();

    if ($queryparams !== null) {
        $page = $queryparams['page'];
        $id = $queryparams['list_id'];
        $condition = "list_id=$id";
        if ($id !== null) {
            $data = $tmdb->read("*", "lists", $condition, $page);
        } else {
            $data = $tmdb->read("*", "lists", null, $page);
        }
    } else {
        $data = $tmdb->read("*", "lists", null, null);
    }

    $payload = json_encode($data);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/3/movieslists', function (Request $request, Response $response, $args) { //function movie 1st
    $queryparams = $request->getQueryParams();

    $tmdb = new database();

    if ($queryparams !== null) {
        $page = $queryparams['page'];
        $id = $queryparams['list_id'];
        $condition = "list_id=$id";
        if ($id !== null) {
            $data = $tmdb->read("*", "4movies_lists", $condition, $page);
        } else {
            $data = $tmdb->read("*", "4movies_lists", null, $page);
        }
    } else {
        $data = $tmdb->read("*", "4movies_lists", null, null);
    }

    $payload = json_encode($data);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/3/movie/moviescompany', function (Request $request, Response $response, $args) { //function movie 1st
    $queryparams = $request->getQueryParams();

    $tmdb = new database();

    if ($queryparams !== null) {
        $page = $queryparams['page'];
        $id = $queryparams['movie_id'];
        $condition = "movie_id=$id";
        if ($id !== null) {
            $data = $tmdb->read("*", "8movies_company", $condition, $page);
        } else {
            $data = $tmdb->read("*", "8movies_company", null, $page);
        }
    } else {
        $data = $tmdb->read("*", "8movies_company", null, null);
    }

    $payload = json_encode($data);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/3/movie/seriescompany', function (Request $request, Response $response, $args) { //function movie 1st
    $queryparams = $request->getQueryParams();

    $tmdb = new database();

    if ($queryparams !== null) {
        $page = $queryparams['page'];
        $id = $queryparams['series_id'];
        $condition = "series_id=$id";
        if ($id !== null) {
            $data = $tmdb->read("*", "7series_company", $condition, $page);
        } else {
            $data = $tmdb->read("*", "7series_company", null, $page);
        }
    } else {
        $data = $tmdb->read("*", "7series_company", null, null);
    }

    $payload = json_encode($data);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/3/movietv/credits', function (Request $request, Response $response, $args) { //function movie 1st
    $queryparams = $request->getQueryParams();

    $tmdb = new database();

    if ($queryparams !== null) {
        $page = $queryparams['page'];
        $id = $queryparams['actor_id'];
        $condition = "actor_id=$id";
        if ($id !== null) {
            $data = $tmdb->read("*", "actors", $condition, $page);
        } else {
            $data = $tmdb->read("*", "actors", null, $page);
        }
    } else {
        $data = $tmdb->read("*", "actors", null, null);
    }

    $payload = json_encode($data);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/3/movietv/credits/moviesactors', function (Request $request, Response $response, $args) { //function movie 1st
    $queryparams = $request->getQueryParams();

    $tmdb = new database();

    if ($queryparams !== null) {
        $page = $queryparams['page'];
        $id = $queryparams['movie_id'];
        $condition = "movie_id=$id";
        if ($id !== null) {
            $data = $tmdb->read("*", "2movies_actors", $condition, $page);
        } else {
            $data = $tmdb->read("*", "2movies_actors", null, $page);
        }
    } else {
        $data = $tmdb->read("*", "2movies_actors", null, null);
    }

    $payload = json_encode($data);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});
$app->get('/3/movietv/credits/seriesactors', function (Request $request, Response $response, $args) { //function movie 1st
    $queryparams = $request->getQueryParams();

    $tmdb = new database();

    if ($queryparams !== null) {
        $page = $queryparams['page'];
        $id = $queryparams['series_id'];
        $condition = "series_id=$id";
        if ($id !== null) {
            $data = $tmdb->read("*", "1series_actors", $condition, $page);
        } else {
            $data = $tmdb->read("*", "1series_actors", null, $page);
        }
    } else {
        $data = $tmdb->read("*", "1series_actors", null, null);
    }

    $payload = json_encode($data);

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
