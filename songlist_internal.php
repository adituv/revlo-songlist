<?php
header("Cached-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: application/json");
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

require("./songlist_curl.php");

$requests = readCache();
$numReqs = count($requests);
$limit = 25;
$page = (!empty($_GET["page"]) ? intval($_GET["page"]) : 1);
$numpages = bcdiv($numReqs, $limit, 0) + 1;
$shownStart = (($page - 1) * $limit);
$numberShown = ($shownStart + $limit > $numReqs ? $numReqs % $limit : $limit);

$requests = array_slice($requests, $shownStart, $numberShown);

$data = array(
    "requests" => $requests,
    "total" => $numReqs,
    "numpages" => $numpages,
    "limit" => $limit
);

print(json_encode($data));

?>