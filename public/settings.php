<?php
require('../php/header.php');
$page_title = 'Settings';
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="/manifest.json">
    <?php include('../php/meta/icons.php')?>

    <!-- normal meta info -->
    <title><?php echo $page_title?></title>
    <meta name="description" content="Change settings">
    <meta name="theme-color" content="#212529">
        
    <!-- facebook meta info -->
    <meta property="og:title" content="<?php echo $page_title?>" />
    <meta property="og:description" content="Change settings">
    <meta property="og:image" content="/favicon.ico" />
    <meta property="og:url" content="/settings" />

    <!-- twitter meta info -->
    <meta name="twitter:title" content="<?php echo $page_title?>" />
    <meta name="twitter:description" content="Change settings" />
    <meta name="twitter:image" content="/favicon.ico" />

    <?php require('../php/page-deps.php') ?>
</head>

<body>
    <?php
    require('../php/navbar.php');
    ?>

    <div class="container mt-4 mb-4">
        <?php
        if (isLoggedIn($client)) {
            // If user want's to delete account
            if (isset($_POST['i-want-to-delete-account']) && $_POST['i-want-to-delete-account'] == "on") {
                if (deleteUser($_SESSION['user-id'], $client)) {
                    header('Location: /');
                    exit();
                } else {
                    echo "<p>Failed to delete your account</p>";
                }
            }

            // If user is changing password
            if (isset($_POST['new-password']) && isset($_POST['old-password'])) {
                if (strlen($_POST['new-password']) > 2) {
                    $result = pg_query_params($client, "SELECT hash_password FROM stuff.users WHERE id=$1", array($_SESSION['user-id']));

                    if ($result) {
                        $entry = pg_fetch_array($result);

                        if ($entry['hash_password'] == md5($_POST['old-password'])) {
                            pg_query_params($client, "UPDATE stuff.users SET hash_password=$1 WHERE id=$2", array(md5($_POST['new-password']), $_SESSION['user-id']));
                            echo "<p>Changed password</p>";
                        }
                    } else {
                        echo "<p>Wrong password</p>";
                    }
                } else {
                    echo "<p>Too short password</p>";
                }
            }

            $isAdmin = isAdmin($client, $_SESSION['user-id']);

            if ($isAdmin) {

                // Delete video
                if (isset($_POST['video-id'])) {
                    if (deleteVideo($_POST['video-id'], $client)) {
                        echo "<p>Succesfully deleted video</p>";
                    } else {
                        echo "<p>Failed to delete video</p>";
                    }
                }

                // Delete comment
                if (isset($_POST['comment-id'])) {
                    if (deleteComment($_POST['comment-id'], $client)) {
                        echo "<p>Succesfully deleted comment</p>";
                    } else {
                        echo "<p>Failed to delete comment</p>";
                    }
                }

                // Delete user
                if (isset($_POST['delete-user'])) {
                    if (deleteUser($_POST['delete-user'], $client)) {
                        echo "<p>Succesfully deleted user</p>";
                    } else {
                        echo "<p>Failed to delete user</p>";
                    }
                }

                // Change avatar
                if (isset($_FILES['avatar'])) {
                    if (changeAvatar($client, $_POST['user-id'])) {
                        echo "<p>Succesfully changed avatar</p>";
                    } else {
                        echo "<p>Failed to change avatar</p>";
                    }
                }
                
            }

            require('../php/settings/normal-panel.php');

            if ($isAdmin) {
                require('../php/settings/admin-panel.php');
            }
        } else {
            header('Location: /login');
            exit();
        }

        ?>


    </div>
</body>

</html>

<?php
pg_close($client);
?>