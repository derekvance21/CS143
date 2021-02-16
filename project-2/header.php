<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  <style>
    table {
      margin: 0 auto;
    }

    .review-tr td {
      border-bottom: 1px solid black;
    }

    h3 {
      padding-top: 2rem;
    }

    .review-td {
      padding-left: 10px; 
      text-align: left; 
      max-width: 600px;
    }

    #movie-score {
      margin-left: 1rem;
      font-size: 2rem;
      margin-right: -0.4rem;
    }

    #search-bar-container {
      margin-bottom: 2rem;
    }

    .center {
      text-align: center;
    }


    .page-title {
      margin: 2rem 3rem;
    }

  </style>
</head>

<body>
  <a style="display: block;" class="center" href="index.php">Home</a>
  <h1 class="center page-title">Internet Movie (MySQL) Database</h1>
  <?php 
    $db = new mysqli('localhost', 'cs143', '', 'cs143');
    if ($db->connect_errno > 0) { 
      die('Unable to connect to database [' . $db->connect_error . ']'); 
    }

    function createLinkCell($display, $action, $id) {
      echo "<tr><td><a href=$action?id=$id>$display</a></td></tr>";
    }
  ?>
  <?php  include "search.php"; ?>