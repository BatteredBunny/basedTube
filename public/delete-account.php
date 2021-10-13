<?php
require('../php/header.php');
$page_title = 'Account deletion';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include('../php/meta/icons.php')?>
    <meta name="theme-color" content="#212529">
    <title>Settings</title>

    <?php require('../php/page-deps.php') ?>
    
</head>

<body>
    <?php
    require('../php/navbar.php');
    ?>

    <div class="container mt-4 mb-4">
        <?php
        if (isLoggedIn($client)) {
            if (isset($_POST['password'])) {
                $result = pg_query_params($client, "SELECT hash_password FROM stuff.users WHERE id=$1 and hash_password=$2", array($_SESSION['user-id'], md5($_POST['password'])))
                or die('Query failed: ' . pg_last_error());
    
                if ($result) {
                    $entry = pg_fetch_array($result);
    
                    if ($entry['hash_password'] == md5($_POST['password'])) {
                        deleteUser($_SESSION['user-id'], $client);
                        header('Location: /');
                        exit();
                    } else {
                        echo "<p>Wrong password!!!</p>";
                    }
                }
    
                pg_free_result($result);
            }
        } else {
            header('Location: /');
            exit();
        }
        ?>

        <form onsubmit="return confirm('Are you sure?');" action="/delete-account" method="post">
            <div class="input-group mb-3">
                <button type="submit" class="btn btn-danger" type="button" id="button-addon4"><span class="material-icons">delete_forever</span> Delete account</button>
                <input autocomplete="off" type="password" class="form-control" placeholder="Confirm password" name="password" aria-describedby="button-addon4" required>
            </div>
        </form>

        <a href="/settings" class="btn btn-primary mt-2"><span class="material-icons">arrow_back</span> Back</a>
    </div>
</body>

</html>

<?php
pg_close($client);
?>