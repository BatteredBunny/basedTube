<?php
require('../php/header.php');
require('../php/time_elapsed.php');
$page_title = $BRANDING;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/manifest.json">
    <?php include('../php/meta/icons.php')?>

    <!-- normal meta info -->
    <title><?php echo $page_title ?></title>
    <meta name="description" content="Search page">
    <meta name="theme-color" content="#212529">
    
    <!-- facebook meta info -->
    <meta property="og:title" content="<?php echo $page_title ?>" />
    <meta property="og:description" content="Search page">
    <meta property="og:image" content="/favicon.ico" />
    <meta property="og:url" content="/search" />

    <!-- twitter meta info -->
    <meta name="twitter:title" content="<?php echo $page_title ?>" />
    <meta name="twitter:description" content="Search page" />
    <meta name="twitter:image" content="/favicon.ico" />

    <?php require('../php/page-deps.php') ?>
</head>

<body>
    <?php require('../php/navbar.php') ?>

    <div class="container-fluid mt-4 mb-4">
        <form class="container input-group mb-4" action="search" method="get">
            <div class="input-group-prepend">
                <button class="btn btn-primary" type="submit">search</button>
            </div>
            <input value="<?php echo $_GET['q'];?>" type="text" name="q" id="q" class="form-control" placeholder="search" aria-label="search" aria-describedby="basic-addon1">
        </form>

        <div class="videos">
            <?php

            if (isset($_GET['q'])) {
                $result = pg_query_params($client, 'SELECT * FROM (SELECT *, ts_rank_cd(to_tsvector(\'english\', name), to_tsquery($1)) AS score FROM stuff.videos) s WHERE score > 0 ORDER BY score DESC', array($_GET['q'])) or die('Query failed: ' . pg_last_error());
            }

            while ($entry = pg_fetch_array($result)) {
                if ($entry['visibility'] != 0) {
                    if (!isset($_SESSION['user-id'])) {
                        continue;
                    }

                    if (!isLoggedIn($client)) {
                        continue;
                    }

                    if ($entry['author'] != $_SESSION['user-id']) {
                        continue;
                    }
                }

                require('../php/video.php');
            }

            pg_free_result($result);
            ?>
        </div>
    </div>
</body>

</html>

<?php
pg_close($client);
?>