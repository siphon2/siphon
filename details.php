<?php

$start = microtime(true);

$arguments = [
    'level' => '"^level^"',
    'path' => '"^path^"'
];

function stats()
{
    global $arguments, $start;
    extract($arguments, EXTR_SKIP);
    $result = []; $debug = []; $info = []; $result['status'] = false;
    if (!file_exists($path)) {array_push($debug, ["error" => "path"]);}
    else
    {
        $result['status'] = true;
        $stat = (object)stat($path);
        $info['type'] = is_link($path) ? 'symlink' : (is_file($path) ? 'file' : (is_dir($path) ? 'dir' : 'unknown'));
        if ($info['type'] == 'symlink') {$info['dest'] = readlink($path);}
        $info['mime'] = mime_content_type($path);
        if ($info['mime'] != 'directory') {$info['exec'] = is_executable($path);}
        $info['size' ] = $stat->size;
        $info['perms'] = fileperms($path);
        $info['owner'] = posix_getpwuid($stat->uid)['name'];
        $info['group'] = posix_getgrgid($stat->gid)['name'];
        $info['atime'] = $stat->atime;
        $info['ctime'] = $stat->ctime;
        $info['mtime'] = $stat->mtime;
    }
    $result['time'] = microtime(true)-$start;
    $result['debug'] = $debug;
    $result['operation'] = [
        'action' => 'll',
        'arguments' => $arguments
    ];
    $result['data'] = $info;
    $response = json_encode($result, JSON_UNESCAPED_UNICODE);
    echo str_replace('=', '', base64_encode(gzdeflate($response, $level)));
    // echo $response;
}

@stats();

?>