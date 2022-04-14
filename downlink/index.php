<?php

$prefix="";
$db = new SQLite3('../tracks.sqlite');
$TrackerID = $db->query('SELECT id FROM locations WHERE date >= DateTime(\'now\',\'-10 minutes\') AND latitude != "" GROUP BY id');

?>{
    "site": "console.theodin.network",
    "trackers": [ <?php
        while ( $ID = $TrackerID->fetchArray() ) {
            $stmt = $db->querySingle("SELECT date, id, altitude, latitude, longitude, battery FROM locations WHERE latitude != \"\" AND id = \"$ID[0]\" ORDER BY date DESC LIMIT 1", true);
            echo $prefix; ?>

        {
            "tracker-id": "<?php echo $stmt['id']; ?>",
            "date": "<?php echo $stmt['date']; ?>",
            "battery": "<?php echo $stmt['battery']; ?>",
            "location": { 
                "altitude": "<?php echo $stmt['altitude']; ?>",
                "latitude": "<?php echo $stmt['latitude']; ?>",
                "longitude": "<?php echo $stmt['longitude']; ?>"
            }
        }<?php $prefix=","; } ?>

    ]
}
