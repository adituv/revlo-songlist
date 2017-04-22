<?php
header("Cached-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
date_default_timezone_set("EST");
error_reporting(E_ALL);
include("./songlist_curl.php");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Aditu's Songlist</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <div class="container">
      <h1>Song Request Queue</h1>

      <?php
      $requests = readCache();
      $numReqs = count($requests);
      $limit = 25;

      $page = (!empty($_GET["page"]) ? intval($_GET["page"]) : 1);
      $numpages = bcdiv($numReqs, $limit, 0) + 1;
      $shownStart = (($page - 1) * $limit) + 1;
      $shownEnd = $shownStart + ($shownStart + $limit > $numReqs ? $numReqs % $limit : $limit) - 1;
      ?>

      <h2>Requests <strong><?=$shownStart?></strong> to <strong><?=$shownEnd?></strong> of <strong><?=$numReqs?></strong></h2>

      <div class="btn-group" role="group" aria-label="Page navigation buttons">
        <?php for($i = 1; $i <= $numpages; $i++) {
            $class = "btn";
            if($page === $i) {
                $class = "$class btn-primary";
            }
        ?>
        <a class="<?=$class?>" role="button" href="?page=<?=$i?>"><?=$i?></a>
        <?php } ?>
      </div>

      <?php if ($numReqs <= 0) { ?>
        <h3>The request queue is empty!</h3>
      <?php }?>
      <table class="table table-striped">
        <thead>
        <tr>
          <th>#</th>
          <th>Sub?</th>
          <th>Request</th>
          <th>User</th>
          <th>Time</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for($i = $shownStart; $i <= $shownEnd; $i++) {
          $request = $requests[$i-1];
          $date = strtotime($request["created_at"]);
          $date = date("H:i:s T, j M Y", $date);
          $subreq = ($request["sub"] == 1 ? "Y" : "N");
        ?>
        <tr>
          <th scope="row"><?=$i?></th>
          <td><?=$subreq?></td>
          <td><?=$request["user_input"]["song"]?></td>
          <td><?=$request["username"]?></td>
          <td><?=$date?></td>
        </tr>
        <?php } ?>
        </tbody>
      </table>
      <div class="btn-group" role="group" aria-label="Page navigation buttons">
        <?php for($i = 1; $i <= $numpages; $i++) {
            $class = "btn";
            if($page === $i) {
                $class = "$class btn-primary";
            }
        ?>
        <a class="<?=$class?>" role="button" href="?page=<?=$i?>"><?=$i?></a>
        <?php } ?>
      </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  </body>
</html>
