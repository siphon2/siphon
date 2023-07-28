<?php

$start = microtime(true);
$rsc_files = 0;
$rsc_dirs = 0;
$rsc_links = 0;
$dst_files = 0;
$dst_dirs = 0;
$dst_links = 0;
$debug = [];
$status = false;

$args = (object) [
    'level' => '"^level^"',
    "source" => '"^source^"',
    "destination" => '"^destination^"'
];


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

function cp($source, $destination)
{
    global $start, $rsc_files, $rsc_dirs, $rsc_links, $dst_files, $dst_dirs, $dst_links, $debug, $status;
    $permissions = 0444;
    $perm_err = false;

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
                @cp($source . '/' . $file, $destination . '/' . $file);
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




@cp($args->source, $args->destination);

$response = json_encode(array(
    'status' => (empty($debug)) ? true : false,
    'time' => microtime(true) - $start,
    'debug' => $debug,
    'operation' => [
        'action' => 'cp',
        'arguments' => $args
    ],
    'data' => [
        'source' => [
            'path' => $args->source,
            'hash' => calculateHash($args->source)
        ],
        'destination' => [
            'path' => $args->destination,
            'hash' => calculateHash($args->destination)
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
), JSON_UNESCAPED_UNICODE);

echo str_replace('=', '', base64_encode(gzdeflate($response, $args->level)));
// echo $response;


?>
