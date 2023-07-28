<?php

$start = microtime(true);

$arguments = [
    'level' => '"^level^"',
    'path' => '"^path^"',
    'link' => '"^link^"'
];

$debug = [];
$data = [];

function sln()
{
    global $arguments, $start, $debug, $data;
    extract($arguments, EXTR_SKIP);
    if (file_exists($path))
    {
      if (@is_link($link))
      {
        @array_push($debug, ['warning' => 'A symlink, named `'.$link.'` already exists']);
        $data = [
            'symlink' =>
            [
                'exist' => true,
                'location' => readlink($link),
                'permissions' => fileperms($link),
                'owner' => posix_getpwuid(fileowner($link))['name'],
                'gruop' => posix_getgrgid(fileowner($link))['name']
            ]
        ];
      }
      else
      {
        @mkdir(dirname($link));
        if (!symlink($path, $link))
        {
            @array_push($debug, ['error' => 'Unable to symlink `'.$path.'` to `'.$link.'`']);
        }
      }
    }
    else
    {
        @array_push($debug, ['warning' => 'Unable to locate `'.$path.'`']);
    }

    $response = json_encode(array(
        'status' => (empty($debug)) ? true : false,
        'time' => microtime(true)-$start,
        'debug' => $debug,
        'operation' => [
            'action' => 'sln',
            'arguments' => $arguments
        ],
        'data' => $data
    ), JSON_UNESCAPED_UNICODE);

    echo str_replace('=', '', base64_encode(gzdeflate($response, $level)));
    // echo $response;
}

@sln();


?>