<?php
$client = pg_connect('host=db dbname=' . getenv("POSTGRES_DB") . ' user=' . getenv("POSTGRES_USER") . ' password=' . getenv("POSTGRES_PASSWORD"));
?>
