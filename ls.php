<?php

$start = microtime(true);

$arguments = [
    'level' => isset($_COOKIE['level']) ? $_COOKIE['level'] : '9',
    "path" => isset($_COOKIE['path']) ? $_COOKIE['path'] : '/sdcard',
    "page" => isset($_COOKIE['page']) ? $_COOKIE['page'] : 1,
    "limit" => isset($_COOKIE['limit']) ? $_COOKIE['limit'] : 10,
    "details" => isset($_COOKIE['details']) ? $_COOKIE['details'] : false
];


function ls()
{
    global $arguments, $start;
    extract($arguments, EXTR_SKIP);
    $status = false; $debug = []; $output = array();

    if (!file_exists($path)) {array_push($debug, ["error" => "path"]);}
    else
    {
        $status = true;
        $handle = opendir($path);
        $start_offset = ($page - 1) * $limit;
        for ($i = 0; $i < $start_offset; $i++) {readdir($handle);}
        $total_count = 0;
        while (($file = readdir($handle)) !== false && $total_count < $limit) {
            if ($file != '.' && $file != '..')
            {
                $path = $path . '/' . $file;
                if ($details)
                {
                    $stat = (object)stat($path);
                    $info['name'] = $file;
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
                    $output[] = $info;
                } else {
                    $output[] = $file;
                }
                $total_count++;
            }
        }
        closedir($handle);
    }

    $response = json_encode(array(
        'status' => $status,
        'time' => microtime(true)-$start,
        'debug' => $debug,
        'operation' => [
            'action' => 'ls',
            'arguments' => $arguments
        ],
        'data' => [
            'path' => $arguments['dir'],
            'details' => $arguments['details'],
            'page' => $page,
            'results' => [
                'limit' => $limit,
                'fetched' => count($output),
                'items' => $output
            ]
        ]
    ), JSON_UNESCAPED_UNICODE);

    // echo str_replace('=', '', base64_encode(gzdeflate($response, $level)));
    echo $response;
}

@ls();

?>
