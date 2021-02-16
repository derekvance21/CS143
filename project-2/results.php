<?php include "header.php"; ?>

<?php
$terms = explode(" ", $_POST["search"]);
function actor_query_func($term)
{
  $sanitized_term = str_replace("'", "''", $term);
  return "(first LIKE '%{$sanitized_term}%' OR last LIKE '%{$sanitized_term}%')";
}
function movie_query_func($term)
{
  $sanitized_term = str_replace("'", "''", $term);
  return "title LIKE '%{$sanitized_term}%'";
}

$movie_statement = $db->prepare(
  "
  SELECT id, title, year
  FROM Movie
  WHERE "
    . implode(" AND ", array_map("movie_query_func", $terms)) .
    "ORDER BY title;"
);
if (!$movie_statement) {
  die("movie_q prepare() failed: $db->error <br>");
}

$actor_statement = $db->prepare(
  "
  SELECT id, first, last
  FROM Actor
  WHERE "
    . implode(" AND ", array_map("actor_query_func", $terms)) .
    "ORDER BY last;"
);
if (!$actor_statement) {
  die("actor_statement prepare() failed: $db->error <br>");
}

?>
<div class="center">
  <table style="margin: 0 auto">
    <thead>
      <tr>
        <th>Movies</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if (!$movie_statement->execute()) {
        die("movie_q execute() failed: $db->error <br>");
      }
      $movie_statement->bind_result($movie_id, $movie_title, $movie_year);

      while ($movie_statement->fetch()) {
        createLinkCell("$movie_title ($movie_year)", "movie.php", $movie_id);
      }

      $movie_statement->free_result();
      ?>
    </tbody>
  </table>
  <table style="margin: 0 auto">
    <thead>
      <tr>
        <th>Actors</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if (!$actor_statement->execute()) {
        die("actor_statement execute() failed: $db->error <br>");
      }
      $actor_statement->bind_result($actor_id, $actor_first, $actor_last);

      while ($actor_statement->fetch()) {
        createLinkCell("$actor_first $actor_last", "actor.php", $actor_id);
      }

      $actor_statement->free_result();
      ?>
    </tbody>
  </table>
</div>
<?php include "footer.php"; ?>