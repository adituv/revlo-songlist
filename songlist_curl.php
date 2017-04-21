<?php
header("HTTP/1.0 404 Not Found");

function requestSongList($reward_id, $page = 0) {
    require("./revlo_api_key.php");
    $baseurl = "https://api.revlo.co/1/";
    $endpoint = "redemptions";

    $query = "?completed=false&refunded=false&reward_id=$reward_id";

    if($page > 0) {
        $query = $query . "&page=$page";
    }

    $url = "$baseurl$endpoint$query";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [ "x-api-key: $apikey" ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}
?>