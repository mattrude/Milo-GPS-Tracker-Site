<?php

$data = json_decode(file_get_contents('php://input'), true);

$id = $data["end_device_ids"]["device_id"];
$date = $data["received_at"];
$lat = $data["uplink_message"]["decoded_payload"]["latitude"];
$lon = $data["uplink_message"]["decoded_payload"]["longitude"];
$battery = $data["uplink_message"]["decoded_payload"]["battery"];
$altitude = $data["uplink_message"]["decoded_payload"]["altitude"];

// Create or open (if exists) the database
$db = new SQLite3('tracks.sqlite');

// Create tables
//$db->exec("CREATE TABLE locations (date TEXT, id TEXT, latitude TEXT, longitude TEXT, altitude TEXT, battery TEXT)");

// Insert values into table
$db->exec("INSERT INTO locations VALUES ('$date', '$id', '$lat', '$lon', '$altitude', '$battery')");

$db->close();

?>
