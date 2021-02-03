<?php include "header.php"; ?>

<?php
$id = $_GET["id"];
$actor_statement = $db->prepare(
  "
  SELECT id, first, last
  FROM Actor
  WHERE id=?;
  "
);
if (!$actor_statement) {
  die("actor_statement prepare() failed: $db->error <br>");
}
$actor_statement->bind_param('i', $id);

$movies_statement = $db->prepare(
  "
  SELECT id, title, year
  FROM Movie
  WHERE id IN (
    SELECT mid 
    FROM MovieActor
    WHERE aid=?
  )
  ORDER BY year DESC;
  "
);
if (!$movies_statement) {
  die("movies_statement prepare() failed: $db->error <br>");
}
$movies_statement->bind_param('i', $id);

?>

<h2 class="center">
  <?php 
  if (!$actor_statement->execute()) {
    die("actor_statement execute() failed: $db->error <br>");
  }
  $actor_statement->bind_result($actor_id, $actor_first, $actor_last);
  $actor_statement->fetch();
  echo "$actor_first $actor_last";
  $actor_statement->free_result();
  ?>
</h2>

<div class="center">
  <table style="margin: 0 auto">
    <thead>
      <tr>
        <th>Movies</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if (!$movies_statement->execute()) {
        die("movies_statement execute() failed: $db->error <br>");
      }
      $movies_statement->bind_result($movie_id, $movie_title, $movie_year);

      while ($movies_statement->fetch()) {
        createLinkCell("$movie_title ($movie_year)", "movie.php", $movie_id);
      }

      $movies_statement->free_result();
      ?>
    </tbody>
  </table>
</div>

<?php include "footer.php"; ?>