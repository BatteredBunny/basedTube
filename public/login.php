<?php
require('/var/www/php/header.php');
$page_title = 'Login';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="/manifest.json">
    <?php include('/var/www/php/meta/icons.php')?>

    <!-- normal meta info -->
    <title><?php echo $page_title?></title>
    <meta name="description" content="Login">
    <meta name="theme-color" content="#212529">
            
    <!-- facebook meta info -->
    <meta property="og:title" content="<?php echo $page_title?>" />
    <meta property="og:description" content="Login">
    <meta property="og:image" content="/favicon.ico" />
    <meta property="og:url" content="/login" />

    <!-- twitter meta info -->
    <meta name="twitter:title" content="<?php echo $page_title?>" />
    <meta name="twitter:description" content="Login" />
    <meta name="twitter:image" content="/favicon.ico" />
    
    <?php require('/var/www/php/page-deps.php') ?>
</head>

<body>
    <?php require('/var/www/php/navbar.php') ?>

    <div class="container mt-4 mb-4">
        <?php
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $result = pg_query_params($client, "SELECT username, id FROM stuff.users WHERE username=$1 and hash_password=$2", array($_POST['username'], md5($_POST['password'])))
                or die('Query failed: ' . pg_last_error());

            if ($result) {
                $entry = pg_fetch_array($result);

                if (isset($entry['username'])) {
                    $_SESSION["user-id"] = $entry['id'];
                    header('Location: /user?name=' . $entry['username']);
                    exit();
                } else {
                    echo "<p>Wrong password!!!</p>";
                }
            }

            pg_free_result($result);
        }
        ?>

        <form action="/login" method="post">
            <div class="mb-3 row">
                <label for="username" class="col-sm-1 col-form-label">Username</label>
                <div class="col-sm-6">
                    <input type="text" name="username" class="form-control" aria-describedby="passwordHelpBlock" id="username">
                    <div id="usernameHelp" class="form-text">
                        Your username must be 1-20 characters long, must not contain spaces, special characters, or emoji.
                    </div>
                </div>
            </div>

            <div class="mb-3 row">
                <label for="inputPassword" class="col-sm-1 col-form-label">Password</label>
                <div class="col-sm-6">
                    <input type="password" name="password" class="form-control" aria-describedby="passwordHelpBlock" id="inputPassword">
                    <div id="passwordHelp" class="form-text">
                        Your password must be at least 3 characters long.
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>

    </div>
</body>

</html>

<?php
pg_close($client);
?>