<?php

error_reporting(E_ALL);

$data = json_decode(file_get_contents('php://input'), true);

// Create or open (if exists) the database
$db = new SQLite3('../tracks.sqlite');

// Create tables
$db->exec("CREATE TABLE IF NOT EXISTS locations (date TEXT, id TEXT, latitude TEXT, longitude TEXT, altitude TEXT, battery TEXT)");

if ( isset($data["uplink_message"]["decoded_payload"]["latitude"]) ) {
    $id = $data["end_device_ids"]["device_id"];
    $date = $data["received_at"];
    $lat = $data["uplink_message"]["decoded_payload"]["latitude"];
    $lon = $data["uplink_message"]["decoded_payload"]["longitude"];
    $battery = $data["uplink_message"]["decoded_payload"]["battery"];
    $altitude = $data["uplink_message"]["decoded_payload"]["altitude"];

    // Insert values into table
    $db->exec("INSERT INTO locations VALUES ('$date', '$id', '$lat', '$lon', '$altitude', '$battery')");

    //$db->exec("DELETE FROM locations WHERE date <= DateTime('now','-30 days')");
}

$db->close();

?>
