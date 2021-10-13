<?php
require('../php/header.php');
$page_title = 'Avatar settings';
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
    <meta name="description" content="Change your avatar here">
    <meta name="theme-color" content="#212529">
        
    <!-- facebook meta info -->
    <meta property="og:title" content="<?php echo $page_title?>" />
    <meta property="og:description" content="Change your avatar here">
    <meta property="og:image" content="/favicon.ico" />
    <meta property="og:url" content="/avatar" />

    <!-- twitter meta info -->
    <meta name="twitter:title" content="<?php echo $page_title?>" />
    <meta name="twitter:description" content="Change your avatar here" />
    <meta name="twitter:image" content="/favicon.ico" />

    <?php require('../php/page-deps.php') ?>
</head>

<body>
    <?php
    require('../php/navbar.php');
    ?>

    <div class="container mt-4 mb-4">
        <?php
        // If user is changing avatar
        if (isset($_FILES['avatar'])) {
            if (!changeAvatar($client, $_SESSION['user-id'])) {
                header('Location: /');
                exit();
            } else {
                header('Location: /avatar');
                exit();
            }
        }
        ?>

        <form action="/avatar" method="post" enctype="multipart/form-data">
            <div class="mb-2">
                <label class="form-label" for="avatar">Change avatar (must be under 10mb)</label>
                <input accept="image/*" onchange="previewFile()" class="form-control" arial-describedby="notice" id="avatar" type="file" name="avatar" required>
                <div id="notice" class="form-text">
                    Note that animated webps don't work right now! (gif and apng work.)
                </div>
            </div>

            <button id="save-button" type="submit" class="btn btn-success"><span class="material-icons">save</span> Save changes</button>
            <div id="preview-cancel" onclick="cancelPreview()" class="btn btn-secondary" hidden>Reset</div>
        </form>

        <img id="profile-pic" alt="User's avatar" class='avatar-big rounded-circle mt-4' src='<?php echo getUserAvatar($_SESSION['user-id'], 0, $client) ?>'>

        <a href="/settings" class="btn btn-primary mt-3"><span class="material-icons">arrow_back</span> Back</a>
    </div>

    <footer>
        <script>
            const fileReader = new FileReader();
            const current_pic = document.getElementById('profile-pic').src;
            const input = document.getElementById('avatar');
            const profile_pic = document.getElementById('profile-pic');
            const preview_cancel_button = document.getElementById('preview-cancel');
            const save_button = document.getElementById('save-button');

            save_button.hidden = true;

            function previewFile() {
                fileReader.addEventListener("load", function() {
                    profile_pic.src = fileReader.result;
                }, false);

                fileReader.readAsDataURL(input.files[0]);

                preview_cancel_button.hidden = false;
                save_button.hidden = false;
            }

            function cancelPreview() {
                profile_pic.src = current_pic; // Changes to current avatar

                input.value = ""; // Clears file input
                preview_cancel_button.hidden = true;
                save_button.hidden = true;
            }
        </script>
    </footer>
</body>

</html>

<?php
pg_close($client);
?>