<?php
require('/var/www/php/header.php');
require('/var/www/php/time_elapsed.php');
$page_title = 'Forum';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('/var/www/php/meta/icons.php')?>

    <!-- normal meta info -->
    <title>Forum</title>
    <meta name="description" content="Forum">
    <meta name="theme-color" content="#212529">
    
    <!-- facebook meta info -->
    <meta property="og:title" content="Forum" />
    <meta property="og:description" content="Forum">
    <meta property="og:image" content="/favicon.ico" />
    <meta property="og:url" content="/" />

    <!-- twitter meta info -->
    <meta name="twitter:title" content="Forum" />
    <meta name="twitter:description" content="Forum" />
    <meta name="twitter:image" content="/favicon.ico" />

    <?php require('/var/www/php/page-deps.php') ?>
</head>

<body>
    <?php require('/var/www/php/navbar.php') ?>

    <div class="container mt-4 mb-4">
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">User</th>
                    <th scope="col">Date</th>
                    <th scope="col">last activity</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = pg_query('SELECT * FROM forum.threads ORDER BY last_reply DESC') or die('Query failed: ' . pg_last_error());

                while ($entry = pg_fetch_array($result)) {
                    echo "<tr>";

                    echo "<td><a href='/thread?id=" . $entry['id'] . "'>". $entry['thread_name'] . "</a></td>";
                    echo "<td>" . getUserById($client, $entry['author']) . "</td>";
                    echo "<td>" . time_elapsed_string($entry['date']) . "</td>";
                    echo "<td>" . time_elapsed_string($entry['last_reply']) . "</td>";

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>