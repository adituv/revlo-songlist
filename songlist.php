<?php
require("./songlist_curl.php");
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
    <div class="container-fluid">
      <h1>Song Request Queue</h1>

      <?php
      $response = requestSongList(738164);
      $requests = json_decode($response, true);
      $songReqs = $requests["redemptions"];
      $numReqs = count($songReqs);
      ?>

      <?php if ($numReqs <= 0) { ?>
          <p>No requests in queue!</p>
      <?php } else { ?>
        <h3>Next <?=$numReqs?> song requests</h2>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Request</th>
              <th>User</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach($songReqs as $i => $request) {
                $n = $i + 1;
                $date = strtotime($request["created_at"]);
                $date = date("H:i:s, j M Y", $date);
            ?>
            <tr>
                <th scope="row"><?=$n?></th>
                <td><?=$request["user_input"]["song"]?></td>
                <td><?=$request["username"]?></td>
                <td><?=$date?></td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      <?php } ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  </body>
</html>
