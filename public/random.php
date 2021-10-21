<?php
require('/var/www/php/db.php');

$result = pg_query('SELECT id FROM stuff.videos WHERE visibility=0 ORDER BY random() LIMIT 1');

if ($result) {
    $entry = pg_fetch_array($result);

    $id = $entry['id'];
    header('Location: /watch?v=' . $id);
    exit();
}
?>