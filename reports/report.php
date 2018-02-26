<?php
$connection = require 'connection.php';
if (empty($_REQUEST['id'])) {
    die('missing argument');
}
$id = $_REQUEST['id'];

$query = file_get_contents(__DIR__ . '/queries/' . $id . '.sql');
$stmt = $connection->prepare($query);
$res = $stmt->execute([]);

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=$id.csv");
header("Pragma: no-cache");
header("Expires: 0");

$encloseAll = false;
$enclosure = '"';
$delimiter = ',';
$delimiter_esc = preg_quote($delimiter, '/');
$enclosure_esc = preg_quote($enclosure, '/');
$first = true;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $output = [];
    if ($first) {
        $header = array_keys($row);
        echo implode($delimiter, $header), "\n";
        $first = false;
    }
    foreach ($row as $field) {
        if ($encloseAll || preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field)) {
            $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
        } else {
            $output[] = $field;
        }
    }
    echo implode($delimiter, $output), "\n";
}
