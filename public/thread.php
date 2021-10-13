<?php
require('../php/header.php');
require('../php/time_elapsed.php');
$page_title = 'Thread';

use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter([
    'html_input' => 'strip',
    'allow_unsafe_links' => false,
]);

// Posts reply to thread
if (isset($_POST['comment']) && isset($_GET['id'])) {
    if (!isLoggedIn($client)) {
        echo '<noscript>You need to be logged in to reply</noscript>';
        echo '<script>alert("You need to be logged in to reply")</script>';
    } else if (strlen($_POST['comment']) > 150) {
        echo '<noscript>Sorry your comment is too long, it has to be under 150 characters</noscript>';
        echo '<script>alert("Sorry your comment is too long, it has to be under 150 characters")</script>';
    } else if (strlen($converter->convertToHtml($_POST['comment'])) == 0 || strlen($_POST['comment']) == 0 || strlen(trim($_POST['comment'])) == 0) {
        echo '<noscript>You can\'t post empty comments</noscript>';
        echo '<script>alert("You can\'t post empty comments")</script>';
    } else {
        pg_query_params(
            $client,
            'INSERT INTO forum.replies 
                (author, content, thread, creation_ip) VALUES($1, $2, $3, $4);',
            array($_SESSION['user-id'], $_POST['comment'], $_GET['id'], $_SERVER['REMOTE_ADDR'])
        );

        pg_query_params($client, 'UPDATE forum.threads SET last_reply=now() WHERE id=$1', array($_GET['id']));

        header('Location: /thread?id=' . $_GET['id']);
        exit();
    }
}?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('../php/meta/icons.php')?>

    <!-- normal meta info -->
    <title><?php echo $page_title?></title>
    <meta name="description" content="<?php echo $page_title?>">
    <meta name="theme-color" content="#212529">
    
    <!-- facebook meta info -->
    <meta property="og:title" content="<?php echo $page_title?>" />
    <meta property="og:description" content="Thread">
    <meta property="og:image" content="/favicon.ico" />

    <!-- twitter meta info -->
    <meta name="twitter:title" content="<?php echo $page_title?>" />
    <meta name="twitter:description" content="Thread" />
    <meta name="twitter:image" content="/favicon.ico" />

    <?php require('../php/page-deps.php') ?>
</head>

<body>
    <?php require('../php/navbar.php') ?>

    <div class="container-fluid mt-4 mb-4">

        <!-- Reply to thread form -->
        <div class="sticky-top comment-section">
            <form action="/thread?id=<?php echo htmlspecialchars($_GET['id']) ?>" method="post">
                <div class="input-group mb-3">
                    <button class="input-group-prepend btn btn-dark border-secondary" type="submit" id="button-addon1">Submit</button>

                    <div class="form-floating">  
                        <textarea cols="30" rows="5" aria-describedby="button-addon1" autocomplete="off" class="form-control bg-dark border-secondary text-white" placeholder="Comment (max 150 char)" name="comment" id="comment"></textarea>
                        <label for="comment">Comment (max 150 char)</label>
                    </div>
                </div>
            </form>
        </div>

        <?php
        //Fetches thread title and author
        $result = pg_query_params($client, "SELECT id, thread_name, author FROM forum.threads WHERE id=$1", array($_GET['id']));
        if ($result) {
            $entry = pg_fetch_array($result);

            if (!isset($entry['id'])) {
                header('Location: /forum');
                exit();
            }

            echo "<h1 class='text-center'>" . $entry['thread_name'] . "</h1>";
            echo "<h3 class='text-center'>" . getUserById($client, $entry['author']) . "</h3>";
        } else {
            header('Location: /forum');
            exit();
        }

        pg_free_result($result);

        //Fetches thread replies
        $result = pg_query_params('SELECT author, date, content FROM forum.replies WHERE thread=$1 ORDER BY date', array($_GET['id']));
        echo "<div class='comments'>";
        while ($entry = pg_fetch_array($result)) {
            $username = getUserById($client, $entry['author']);
            $avatar = getUserAvatar($entry['author'], 40, $client);

            echo "<div class='comment'><div class='comment-about'>";

            echo "
            <a href='/user?name=" . $username . "'>
                <img alt='User's avatar' class='avatar rounded-circle' src='" . $avatar . "' >
                <small class='comment-author'>" . htmlspecialchars($username) . "</small>
            </a>";

            echo "<small class='comment-date'>" . time_elapsed_string($entry['date']) . "</small>";

            echo "</div>
                <p class='comment-content'>" . $converter->convertToHtml($entry['content']) . "</p>
            </div>";
        }
        echo "</div>";

        pg_free_result($result);
        ?>
    </div>
</body>

</html>