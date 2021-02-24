<?php
// get the id parameter from the request
$id = intval($_GET['id']);

// set the Content-Type header to JSON, so that the client knows that we are returning a JSON data
header('Content-Type: application/json');

$mng = new MongoDB\Driver\Manager("mongodb://localhost:27017");

$filter = [ "id" => strval($id)];
$options = ["projection" => ['_id' => 0]];
$query = new MongoDB\Driver\Query($filter, $options);

$rows = $mng->executeQuery("testdb.testcol", $query);
$laureate = current($rows->toArray());
// unset($laureate->_id);

if (!empty($laureate)) {
    echo json_encode($laureate);
} else {
    echo "No match found\n";
}

?>