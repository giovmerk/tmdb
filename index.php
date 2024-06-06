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
    $queryparams = $request -> getQueryParams();
    
    $tmdb = new database();
    
    if($queryparams!==null)
    {
        if($queryparams['language']!==null)
        {
            $language = $queryparams['language'];
            $language = $tmdb -> real_escape_string($language);
        }
        if($queryparams['page']!==null)
        {
            $page = $queryparams['page'];
        }
        
        if($queryparams['language']!==null && $queryparams['page']!==null)
        {
            $sCondition = "language=$language AND page=$page";
        }
        else if($queryparams['page']===null && $queryparams['page']!==null)
        {
            $sCondition = "page=$page";
        }
        else if($queryparams['page']!==null && $queryparams['page']===null)
        {
            $sCondition = "language=$language";
        } 
        $data = $tmdb->read('*', 'movies', $sCondition); 
        echo $data;
    }
    else
    {
    $data = $tmdb -> read("*", "movies", null);
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
?>