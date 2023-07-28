<?php

$start = microtime(true);
$arguments = [
    'level' => '"^level^"',
    "source" => '"^source^"',
    "destination" => '"^destination^"'
];


function mv()
{
    global $arguments, $start;
    extract($arguments, EXTR_SKIP);
    $status = false; $debug = [];

    if (file_exists($source))
    {
        if (rename($source, $destination))
        {
            $status = true;
        }
        else
        {
            @array_push($debug, ['error' => 'Unable to move `'.$source.'` to `'.$destination.'`']);
        }
    }
    else
    {
        @array_push($debug, ['error' => 'Unable to locate `'.$source.'`']);
    }
    $response = json_encode([
        'status' => (empty($debug)) ? true : false,
        'time' => microtime(true) - $start,
        'debug' => $debug,
        'operation' => [
            'action' => 'mv',
            'arguments' => $arguments
        ],
        'data' => []
    ], JSON_UNESCAPED_UNICODE);

    echo str_replace('=', '', base64_encode(gzdeflate($response, $level)));
    // echo $response;
}

@mv();


?>