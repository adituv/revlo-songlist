<?php
$cache_file = "./request_cache.dat";
$cache_time = 30;
$rewardid_non = 738164;
$rewardid_sub = 740524;
$url = "https://api.revlo.co/1/redemptions";

function readCache() {
    global $cache_file;

    $cache_data = unserialize(file_get_contents($cache_file));
    $exp = $cache_data["expiration"] - time();
    if ($exp < 0) {
        $data = refreshCache();
        echo "<p>Cache expired: data fetched from REST API</p>";
    } else {
        $data = $cache_data["data"];
        echo "<p>Cache expires in $exp seconds: data read from cache</p>";
    }
    return $data;
}

function refreshCache() {
    // Provides $apikey
    require("./revlo_api_key.php");

    global $url;
    global $rewardid_sub;
    global $rewardid_non;
    global $cache_file;
    global $cache_time;

    $data = array(
        "reward_id" => $rewardid_sub,
        "completed" => "false",
        "refunded" => "false"
    );
    $chsub = curlRequest($url, $data, $apikey);

    $data["reward_id"] = $rewardid_non;
    $chnon = curlRequest($url, $data, $apikey);

    $subreqs = json_decode($chsub, true);
    $nonreqs = json_decode($chnon, true);

    $subPageCount = bcdiv($subreqs["total"], $subreqs["page_size"], 0) + 1;
    $nonPageCount = bcdiv($nonreqs["total"], $nonreqs["page_size"], 0) + 1;

    foreach($subreqs["redemptions"] as $r => $v) {
        $subreqs["redemptions"][$r]["sub"] = 1;
    }
    foreach($nonreqs["redemptions"] as $r => $v) {
        $nonreqs["redemptions"][$r]["sub"] = 0;
    }
    $songreqs = array_merge($subreqs["redemptions"], $nonreqs["redemptions"]);

    $data["reward_id"] = $rewardid_sub;
    for($i = 2; $i < $subPageCount; $i++) {
        $data["page"] = $i;
        $pageData = curlRequest($url, $data, $apikey);
        foreach($pageData["redemptions"] as $r => $v) {
            $pageData["redemptions"][$r]["sub"] = 1;
        }

        $songreqs = array_merge($songreqs, $pageData["redemptions"]);
    }

    $data["reward_id"] = $rewardid_non;
    for($i = 2; $i < $nonPageCount; $i++) {
        $data["page"] = $i;
        $pageData = curlRequest($url, $data, $apikey);
        foreach($pageData["redemptions"] as $r => $v) {
            $pageData["redemptions"][$r]["sub"] = 0;
        }

        $songreqs = array_merge($songreqs, $pageData["redemptions"]);
    }

    usort($songreqs, "sortById");
    $reqsDeduped = removeDuplicates($songreqs);

    $cacheData = array(
        "expiration" => time() + $cache_time,
        "data" => $reqsDeduped
    );

    file_put_contents($cache_file, serialize($cacheData));

    return $cacheData["data"];
}

function removeDuplicates($songreqs) {
    $tmp = array();
    foreach($songreqs as $k => $song) {
        $tmp[$k] = $song["redemption_id"];
    }

    array_unique($tmp);

    $result = array();
    foreach($tmp as $k => $id) {
        array_push($result, $songreqs[$k]);
    }

    return $result;
}

function sortById($song1, $song2) {
    return $song1["redemption_id"] - $song2["redemption_id"];
}

function curlRequest($url, $data, $apikey = NULL) {
    $query = http_build_query($data);
    $curl = curl_init("$url?$query");
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    if ($apikey !== NULL) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, [ "x-api-key: $apikey" ]);
    }

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}