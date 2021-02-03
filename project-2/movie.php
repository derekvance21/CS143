<?php include "header.php"; ?>

<?php
$id = $_GET["id"];
$movie_statement = $db->prepare("
  SELECT id, title, year
  FROM Movie
  WHERE id=?;
");
if (!$movie_statement) {
  die("movie_statement prepare() failed: $db->error <br>");
}
$movie_statement->bind_param('i', $id);

$actors_statement = $db->prepare("
  SELECT id, first, last
  FROM Actor
  WHERE id IN (
    SELECT aid 
    FROM MovieActor
    WHERE mid=?
  )
  ORDER BY last;
");
if (!$actors_statement) {
  die("actors_statement prepare() failed: $db->error <br>");
}
$actors_statement->bind_param('i', $id);

$reviews_statement = $db->prepare("
  SELECT name, DATE(time) AS date, rating, comment
  FROM Review
  WHERE mid=?
  ORDER BY date DESC;
");
if (!$reviews_statement) {
  die("reviews_statement prepare() failed: $db->error <br>");
}
$reviews_statement->bind_param('i', $id);

$rating_statement = $db->prepare("
  SELECT AVG(rating)
  FROM Review
  WHERE mid=?;
");
if (!$rating_statement) {
  die("rating_statement prepare() failed: $db->error <br>");
}
$rating_statement->bind_param('i', $id);

if (isset($_POST['name'], $_POST['rating'], $_POST['comment'])) {
  $post_name = $_POST['name'];
  $post_rating = $_POST['rating'];
  $post_comment = $_POST['comment'];
  $post_statement = $db->prepare("
    INSERT INTO Review
    VALUES (?, NOW(), ?, ?, ?);
  ");
  if (!$post_statement) {
    die("post_statement prepare() failed: $db->error <br>");
  }
  $post_statement->bind_param('siis', $post_name, $id, $post_rating, $post_comment);
  if (!$post_statement->execute()) {
    die("post_statement execute() failed: $post_statement->error <br>");
  }
  $post_statement->free_result();
}

?>
<div class="center">
  <h2 class="center" style="display: inline-block;">
    <?php
    if (!$movie_statement->execute()) {
      die("movie_statement execute() failed: $db->error <br>");
    }
    $movie_statement->bind_result($movie_id, $movie_title, $movie_year);
    $movie_statement->fetch();
    echo $movie_title;
    $movie_statement->free_result();
    ?>
  </h2>
  <span>
    <span id="movie-score">
      <?php
      if (!$rating_statement->execute()) {
        die("rating_statement execute() failed: $db->error <br>");
      }
      $rating_statement->bind_result($movie_rating);
      $rating_statement->fetch();
      echo $movie_rating > 0 ? round($movie_rating, 1) : "?";
      $rating_statement->free_result();
      ?>
    </span>/5
  </span>
</div>
<div class="center">
  <table style="margin: 0 auto">
    <thead>
      <tr>
        <th>Actors</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if (!$actors_statement->execute()) {
        die("actors_statement execute() failed: $db->error <br>");
      }
      $actors_statement->bind_result($actor_id, $actor_first, $actor_last);

      while ($actors_statement->fetch()) {
        createLinkCell("$actor_first $actor_last", "actor.php", $actor_id);
      }

      $actors_statement->free_result();
      ?>
    </tbody>
  </table>
  <h3>Reviews</h3>
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Time</th>
        <th>Rating</th>
        <th>Comment</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      if (!$reviews_statement->execute()) {
        die("reviews_statement execute() failed: $db->error <br>");
      }
      $reviews_statement->bind_result($review_name, $review_time, $review_rating, $review_comment);
      while ($reviews_statement->fetch()) {
        echo "<tr class='review-tr'><td>$review_name</td><td>$review_time</td><td>$review_rating</td><td class='review-td'>$review_comment</td></tr>";
      }
      ?>
    </tbody>
  </table>
  <h3>Write Review</h3>
  <form method="POST" action="movie.php?id=<?php echo $id?>">
    <label for="name">Name: </label>
    <input type="text" name="name" id=name">

    <div>
      <span>Rating:</span>
      <input type="radio" id="rating-1" name="rating" value="1">
      <label for="score-1">1</label>
      <input type="radio" id="rating-2" name="rating" value="2">
      <label for="score-2">2</label>
      <input type="radio" id="rating-3" name="rating" value="3">
      <label for="score-3">3</label>
      <input type="radio" id="rating-4" name="rating" value="4">
      <label for="score-4">4</label>
      <input type="radio" id="rating-5" name="rating" value="5">
      <label for="score-5">5</label>
    </div>
    
    <label for="comment">Comment: </label>
    <textarea name="comment" id="comment" cols="60" rows="10" style="margin: 0 auto; display: block;"></textarea>
    
    <input style="margin-top: 10px;" type="submit" value="Submit Review">
  </form> 
</div>
<script>
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
</script>
<?php include "footer.php"; ?>