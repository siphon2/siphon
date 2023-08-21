<?php

$start = microtime(true);

$arguments = [
  'level' => '"^level^"',
  'path' => '"^path^"',
  'permissions' => "^permissions^"
];

$debug = [];
$status = false;

function mkdir_()
{
    global $arguments, $start, $status, $debug;
    extract($arguments, EXTR_SKIP);
    if (!file_exists($path))
    {
      if (@mkdir($path, $permissions, true))
      {
        $status = true;
      }
      else
      {
        @array_push($debug, ['error' => 'Unable to create `'.$path.'`']);
      }
    }
    else
    {
        @array_push($debug, ['warning' => 'Path `'.$path.'` already exists']);
    }
}


@mkdir_();

$response = json_encode([
    'status' => (empty($debug)) ? true : false,
    'time' => microtime(true) - $start,
    'debug' => $debug,
    'operation' => [
        'action' => 'mkdir',
        'arguments' => $arguments
    ],
    'data' => []
], JSON_UNESCAPED_UNICODE);

echo str_replace('=', '', base64_encode(gzdeflate($response, $level)));
// echo $response;


?>
