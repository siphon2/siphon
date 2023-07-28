<?php

$start = microtime(true);
$arguments = [
    'level' => '"^level^"',
    "path" => '"^path^"'
];


function upload()
{
    global $arguments, $start;
    extract($arguments, EXTR_SKIP);
    $debug = []; $data = [];

    if (file_exists($path))
    {
        @array_push($debug, ['warning' => 'File `'.$path.'` already exists']);
    }
    else
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if (isset($_FILES['file']))
            {
                $file = $_FILES['file'];
                if ($file['error'] === UPLOAD_ERR_OK)
                {
                @mkdir(dirname($path));
                $destination = dirname($path) . PATH_SEPARATOR . $file['name'];
                $destination = $path;
                if (!move_uploaded_file($file['tmp_name'], $destination))
                {
                    @array_push($debug, ['error' => 'Error uploading file']);
                    @array_push($debug, ['debug' => 'Error code: ' . $file['error']]);
                }
                }
            }
            else
            {
                @array_push($debug, ['warning' => 'Nothing was uploaded']);
            }
        }
        else
        {
            @array_push($debug, ['warning' => 'Uploading FORM DATA requires POST request']);
        }
    }

    $response = json_encode([
        'status' => (empty($debug)) ? true : false,
        'time' => microtime(true) - $start,
        'debug' => $debug,
        'operation' => [
            'action' => 'form upload',
            'arguments' => $arguments
        ],
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);

    echo str_replace('=', '', base64_encode(gzdeflate($response, $level)));
    // echo $response;
}

@upload();

?>