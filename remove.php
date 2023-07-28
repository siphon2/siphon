<?php

$start = microtime(true);

$args = (object)[
  'level' => '"^level^"',
  'path' => '"^path^"'
];

$debug = [];
$status = false;

function rm($path)
{
    global $args, $start, $status, $debug;
    if (file_exists($path))
    {
        if (is_dir($path))
        {
          $objects = scandir($path);
          foreach ($objects as $object)
          {
            if ($object != "." && $object != "..")
            {
              if (is_dir($path. DIRECTORY_SEPARATOR .$object) && !is_link($path."/".$object))
                rm($path. DIRECTORY_SEPARATOR .$object);
              else
                unlink($path. DIRECTORY_SEPARATOR .$object); 
            } 
          }
          rmdir($path); 
        } 
    }
    else
    {
        @array_push($debug, ['error' => 'Unable to locate `'.$path.'`']);
    }
    if ($debug != [])
    {
        @array_push($debug, ['error' => 'Unable to remove `'.$path.'`']);   
    }
  }


@rm($args->path);

$response = json_encode([
    'status' => (empty($debug)) ? true : false,
    'time' => microtime(true) - $start,
    'debug' => $debug,
    'operation' => [
        'action' => 'rm',
        'arguments' => $args
    ],
    'data' => []
], JSON_UNESCAPED_UNICODE);

echo str_replace('=', '', base64_encode(gzdeflate($response, $args->level)));
// echo $response;

?>