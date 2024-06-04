<?Php
$movie_id = 1001;
$array = [10,20,30,40,50];

foreach ($array as $value)
{
    echo "INSERT INTO nome_tabella VALUES ($movie_id, $value)";
}
for($i=1; $i<=5; ++$i) #for per prendere gradualmente diversi elementi attraverso le API
{
    $response = $client->request('GET', "https://api.themoviedb.org/3/discover/movie?language=en-US&page=" . $i, [
        'headers' => [
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI4NDU5MjQ1YzU3MTkyNTM2OTYxMjgzOWI3MmYxY2E0MyIsInN1YiI6IjY2NTU5ZTYxMjcyZWQ0NmYzYjIxMjYwOCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.bbQz0qqFEXOaTcHlkFHdILUYbxI8CBwLeYYeZ5Xke-g',
            'accept' => 'application/json',
        ],
    ]);
    
    
    $jsonobj = $response->getBody();
    
    $obj = json_decode($jsonobj);
    
    $obj = $obj->results;
    
    for($i=0; $i<20; ++$i)
    {
        foreach($obj as $key => $value) #adatto i dati alla query
        {
            $adult = $value -> adult;
            var_export($adult);
        }
        
    }
}

?>