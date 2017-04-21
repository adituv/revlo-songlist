<?php

header("Content-Type: application/json");

require("./songlist_curl.php");

print(requestSongList(738164));

?>