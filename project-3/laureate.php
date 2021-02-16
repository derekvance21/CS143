<?php
// get the id parameter from the request
$id = intval($_GET['id']);

// set the Content-Type header to JSON, so that the client knows that we are returning a JSON data
header('Content-Type: application/json');

$db = new mysqli('localhost', 'cs143', '', 'cs143');
if ($db->connect_errno > 0) { 
    die('Unable to connect to database [' . $db->connect_error . ']'); 
}

$laureate_statement = $db->prepare(
    "
    SELECT id, givenName, familyName, orgName, gender, originDate, originCity, originCountry
    FROM Laureate
    WHERE id=?;
    "
  );
if (!$laureate_statement) {
  die("laureate_statement prepare() failed: $db->error <br>");
}
$laureate_statement->bind_param('i', $id);

if (!$laureate_statement->execute()) {
  die("laureate_statement execute() failed: $db->error");
}
$laureate_statement->bind_result($id, $givenName, $familyName, $orgName, $gender, $originDate, $originCity, $originCountry);
$laureate_statement->fetch();
$laureate_statement->free_result();

$affiliations_statement = $db->prepare(
  "
  SELECT year, category, name, city, country
  FROM Affiliations
  WHERE lid=?;
  "
);
if (!$affiliations_statement) {
  die("affiliations_statement prepare() failed: $db->error <br>");
}
$affiliations_statement->bind_param('i', $id);

if (!$affiliations_statement->execute()) {
  die("affiliations_statement execute() failed: $db->error <br>");
}
$affiliations_statement->bind_result($year, $category, $name, $city, $country);
$affiliations = array();

while ($affiliations_statement->fetch()) {

  $attrs = [
    "key" => (object) [
      "year" => $year,
      "category" => $category
    ],
    "value" => [
      "name" => ["en" => $name],
      "city" => ["en" => $city],
      "country" => ["en" => $country]
    ]
  ];
  if (!$city) {
    unset($attrs["value"]["city"]);
  }
  if (!$country) {
    unset($attrs["value"]["country"]);
  }
  array_push($affiliations, (object) $attrs);
}
// print_r($affiliations[0]->key);

$affiliations_statement->free_result();

$wins_statement = $db->prepare(
    "
    SELECT W.year, W.category, portion, sortOrder, motivation, prizeStatus, prizeAmount, dateAwarded
    FROM Wins W LEFT OUTER JOIN Prize P ON W.year=P.year AND W.category=P.category
    WHERE lid=?;
    "
  );
  if (!$wins_statement) {
    die("wins_statement prepare() failed: $db->error <br>");
  }
  $wins_statement->bind_param('i', $id);

if (!$wins_statement->execute()) {
    die("wins_statement execute() failed: $db->error <br>");
  }
$wins_statement->bind_result($year, $category, $portion, $sortOrder, $motivation, $prizeStatus, $prizeAmount, $dateAwarded);
$nobelPrizes = array();
while ($wins_statement->fetch()) {
  $attrs = [
    "awardYear" => strval($year),
    "category" => (object) [ 
      "en" => $category 
    ],
    "sortOrder" => strval($sortOrder),
    "portion" => $portion,
    "dateAwarded" => $dateAwarded,
    "prizeStatus" => $prizeStatus,
    "motivation" => (object) [ 
      "en" => $motivation 
    ],
    "prizeAmount" => $prizeAmount
  ];
  if (!$dateAwarded) {
    unset($attrs["dateAwarded"]);
  }

  array_push($nobelPrizes, (object) $attrs);
}
$wins_statement->free_result();

if ($orgName) {
  $laureateAttrs = [
    "id" => strval($id),
    "orgName" => [
      "en" => $orgName
    ]
  ];
} else {
  $laureateAttrs = [
    "id" => strval($id),
    "givenName" => [
      "en" => $givenName
    ]
  ];
  if ($familyName) {
    $laureateAttrs["familyName"] = ["en" => $familyName];
  }
  $laureateAttrs["gender"] = $gender;
}
$originKey = $orgName ? "founded" : "birth";
if ($originDate) {
  $laureateAttrs[$originKey] = [ "date" => $originDate ];
  if ($originCountry) {
    $laureateAttrs[$originKey]["place"] = ["country" => ["en" => $originCountry]];
    if ($originCity) {
      $laureateAttrs[$originKey]["place"]["city"] = ["en" => $originCity];
    }
  }
}

$output = (object) array_merge($laureateAttrs, [
    "nobelPrizes" => array_map(function($nobelPrize) {
      global $affiliations;
      $affs = array_filter($affiliations, function($affiliation) use ($nobelPrize) {
        return strval($affiliation->key->year) == $nobelPrize->awardYear && $affiliation->key->category == $nobelPrize->category->en;
      });
      $affs = array_map(function ($aff) {return $aff->value;}, $affs);
      $nobelPrize = (array)$nobelPrize;
      if ($affs) {
        $nobelPrize["affiliations"] = $affs;
      }
      return (object) $nobelPrize;
    }, $nobelPrizes)
]);

echo json_encode($output);

?>
