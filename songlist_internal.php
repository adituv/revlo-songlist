<?php

header("Content-Type: application/json");

require("./songlist_curl.php");

print_r(readCache());

?>