<?php

$start = microtime(true);

$arguments = [
    'level' => '"^level^"',
    'path' => '"^path^"',
    'permissions' => "^permissions^"
];

$debug = [];
$data = [];


function chmod_()
{
    global $arguments, $start, $debug, $data;
    extract($arguments, EXTR_SKIP);
    if (file_exists($path))
    {
        if (!chmod($path, $permissions))
        {
            @array_push($debug, ['error' => 'Unable to change permissions of `'.$path.'`']);
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

@chmod_();

?>