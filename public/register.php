<?php
require('/var/www/php/header.php');
$page_title = 'Register';
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
    <meta name="description" content="Register">
    <meta name="theme-color" content="#212529">
        
    <!-- facebook meta info -->
    <meta property="og:title" content="<?php echo $page_title?>" />
    <meta property="og:description" content="Register">
    <meta property="og:image" content="/favicon.ico" />
    <meta property="og:url" content="/register" />

    <!-- twitter meta info -->
    <meta name="twitter:title" content="<?php echo $page_title?>" />
    <meta name="twitter:description" content="Register" />
    <meta name="twitter:image" content="/favicon.ico" />

    <?php require('/var/www/php/page-deps.php') ?>
</head>

<body>
    <?php
    require('/var/www/php/navbar.php');
    ?>

    <div class="container mt-4 mb-4">
        <?php
        if (isset($_POST['username']) && isset($_POST['password'])) {
            if (strlen($_POST['username']) == 0) {
                echo "<p>Username can't be empty";
            } else if (strlen($_POST['username']) > 20) {
                echo "<p>Sorry, username can't be over 20 characters long";
            } else if (!preg_match('([a-zA-Z0-9-_])', $_POST['username'])) {
                echo "<p>Sorry, username can't contain special characters</p>";
            } else if (preg_match('/\s/', $_POST['username'])) {
                echo "<p>No spaces in username!!!</p>";
            } else if (strlen($_POST['password']) < 3) {
                echo "<p>Sorry, your password is too short";
            } else if (getIdByUser($client, $_POST['username']) != false) {
                echo "<p>Sorry, this username is taken!</p>";
            } else {
                $result = pg_query_params($client, "INSERT INTO stuff.users (username, hash_password, creation_ip) VALUES ($1, $2, $3) RETURNING id", array($_POST['username'], md5($_POST['password']), $_SERVER['REMOTE_ADDR']));

                if ($result) {
                    $entry = pg_fetch_array($result);

                    $_SESSION["wants_intro"] = true;
                    $_SESSION["user-id"] = $entry['id'];

                    header('Location: /');
                    exit();

                    pg_free_result($result);
                } else {
                    $error = pg_last_error($client);

                    if (preg_match('/duplicate/i', $error)) {
                        echo "<p>Sorry, this username is taken!</p>";
                    } else {
                        echo "<p>Unknown error</p>";
                    }
                }
            }
        }

        ?>

        <form action="/register" method="post">
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

            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</body>

</html>

<?php
pg_close($client);
?>