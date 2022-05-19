<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$prefix="";
$db = new SQLite3('../tracks.sqlite');
$TrackerID = $db->query('SELECT id FROM locations WHERE date >= DateTime(\'now\',\'-10 minutes\') AND latitude != "" AND id == "gps-tracker-2" GROUP BY id');

while ( $ID = $TrackerID->fetchArray() ) {
$stmt = $db->querySingle("SELECT date, id, altitude, latitude, longitude, battery FROM locations WHERE latitude != \"\" AND id = \"$ID[0]\" ORDER BY date DESC LIMIT 1", true); ?>
{
    "tracker-id": "<?php echo $stmt['id']; ?>",
    "date": "<?php echo $stmt['date']; ?>",
    "battery": "<?php echo $stmt['battery']; ?>",
    "location": { 
        "altitude": "<?php echo $stmt['altitude']; ?>",
        "latitude": "<?php echo $stmt['latitude']; ?>",
        "longitude": "<?php echo $stmt['longitude']; ?>"
    }
}<?php } ?>
