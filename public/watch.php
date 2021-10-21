<?php
require('/var/www/php/header.php');
require('/var/www/php/time_elapsed.php');
$page_title = "";

// Makes sure its a valid video
if (isset($_GET['v'])) {
    $result = pg_query_params($client, 'SELECT "name", "views", "date", "id", "file_name", "description", "author", "visibility", "loop_video", "file_id", "thumbnail_id" FROM stuff.videos WHERE id=$1', array($_GET['v']));

    if ($result) {
        $entry = pg_fetch_array($result);

        if (!isset($entry['id'])) {
            header('Location: /');
            exit();
        }

        if ($entry['visibility'] == 2 && $entry['author'] != $_SESSION['user-id']) {
            header('Location: /');
            exit();
        }

        $author = $entry['author'];
        $visibility = $entry['visibility'];

        // Ups the view counter
        pg_query_params($client, 'UPDATE stuff.videos SET views = $1 WHERE id=$2', array($entry['views'] + 1, $_GET['v']));
    } else {
        header('Location: /');
        exit();
    }

    $authorName = htmlspecialchars(getUserById($client, $entry['author']));
    $thumbnail = getVideoThumbnail($entry);

    pg_free_result($result);
} ?>

<title><?php echo htmlspecialchars($entry['name']) ?> by <?php echo $authorName ?></title>
<meta name="theme-color" content="#212529">

<!-- facebook meta info -->
<meta property="og:title" content="<?php echo htmlspecialchars($entry['name']) ?> by <?php echo $authorName ?>" />
<meta property="og:description" content="<?php echo htmlspecialchars($entry['description']) ?>">
<meta property="og:type" content="video" />
<meta property="og:video" content="<?php
                                    echo $CDN_DOMAIN . "/" . $entry['id'] . "/";

                                    # Legacy video file location code
                                    if ($entry['id'] != $entry['file_id']) {
                                        echo $entry['file_id'] . "/";
                                    };

                                    echo $entry['file_name'] ?>" />
<meta property="og:url" content="/watch?v=<?php echo htmlspecialchars($_GET['v']) ?>" />

<!-- twitter meta info -->
<meta name="twitter:title" content="<?php echo htmlspecialchars($entry['name']) ?> by <?php echo $authorName ?>" />
<meta name="twitter:description" content="<?php echo htmlspecialchars($entry['description']) ?>" />

<link type="application/json+oembed" href="/api/video_oembed?id=<?php echo $_GET['v']?>" />
<?php require('/var/www/php/page-deps.php') ?>

<script src="https://cdn.jsdelivr.net/npm/video.js@7/dist/video.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/video.js@7/dist/video-js.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@5/dark.min.css">
<script src="/js/share.js"></script>
</head>

<body>
    <?php require('/var/www/php/navbar.php') ?>

    <div class="container-fluid mt-4 mb-5">
        <?php

        use League\CommonMark\CommonMarkConverter;

        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        // Posts comment
        if (isset($_POST['comment'])) {
            if (strlen($_POST['comment']) > 150) { // If comment is over 150 char
                echo '<noscript>Sorry your comment is too long, it has to be under 150 characters</noscript>';
                echo '<script>alert("Sorry your comment is too long, it has to be under 150 characters")</script>';
            } else if (strlen($converter->convertToHtml($_POST['comment'])) == 0 || strlen($_POST['comment']) == 0 || strlen(trim($_POST['comment'])) == 0) {
                echo '<noscript>You can\'t post empty comments</noscript>';
                echo '<script>alert("You can\'t post empty comments")</script>';
            } else {
                pg_query_params($client, 'INSERT INTO stuff.comments (author, content, video, creation_ip) VALUES($1, $2, $3, $4);', array($_SESSION['user-id'], $_POST['comment'], $_GET['v'], $_SERVER['REMOTE_ADDR']));
                header('Location: /watch?v=' . $_GET['v']);
                exit();
            }
        }

        if (isLoggedIn($client)) {
            // Change loop propetriy
            if (isset($_POST['loop-video'])) {
                loopVideo($client, $_GET['v']);
                header('Location: /watch?v=' . $_GET['v']);
                exit();
            }

            // Changes visibility
            if (isset($_POST['change-visibility'])) {
                changeVisibility($client, $_GET['v']);
                header('Location: /watch?v=' . $_GET['v']);
                exit();
            }

            // Deletes video
            if (isset($_POST['delete-video'])) {
                deleteVideo($_POST['delete-video'], $client);
                header('Location: /');
                exit();
            }

            // Deletes comment
            if (isset($_POST['delete-comment'])) {
                deleteComment($_POST['delete-comment'], $client);
            }
        }

        // Video player
        require('/var/www/php/video_player.php');
        ?>

        <!-- Comment making form -->
        <div class="sticky-top comment-section">
            <form action="/watch?v=<?php echo htmlspecialchars($_GET['v']) ?>" method="post">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <button class="btn btn-dark border-secondary" type="submit" id="button-addon1">Submit</button>
                    </div>

                    <input name="comment" id="comment" placeholder="Comment (max 150 char)" autocomplete="off" type="text" class="form-control bg-dark border-secondary text-white" aria-label="comment" aria-describedby="basic-addon1">
                </div>
            </form>
        </div>

        <?php
        // Gets comments from db
        if (isset($_GET['v'])) {
            $result = pg_query_params($client, 'SELECT "author", "content", "date", "id" FROM stuff.comments WHERE video=$1 ORDER BY date DESC', array($_GET['v']));

            if ($result) {
                echo "<div class='comments'>";
                while ($entry = pg_fetch_array($result)) {
                    echo "<div class='comment'>
                    <div class='comment-about'>";

                    // If comment from user with account
                    if (isset($entry['author'])) {
                        $avatar = getUserAvatar($entry['author'], 40, $client);

                        echo "
                        <a href='/user?name=" . getUserById($client, $entry['author']) . "'>
                            <img alt='User's avatar' class='avatar rounded-circle' src='" . $avatar . "' >
                            <small class='comment-author'>" . htmlspecialchars(getUserById($client, $entry['author'])) . "</small>
                        </a>";
                    } else { // If anonymous comment
                        echo "<img alt='User's avatar' class='avatar rounded-circle' src='/assets/avatar/unknown.webp'>";
                        echo "<small class='comment-author'>Unknown </small>";
                    }

                    echo "<small class='comment-date'>" . time_elapsed_string($entry['date']) . "</small>";

                    // Form to delete comment
                    if (isLoggedIn($client) && $entry['author'] == $_SESSION['user-id']) {
                        require('/var/www/php/comment-deletion-form.php');
                    }

                    echo "</div>
                        <p class='comment-content'>" . $converter->convertToHtml($entry['content']) . "</p>
                    </div>";
                }
                echo "</div>";
            }

            pg_free_result($result);
        }
        ?>
    </div>
</body>

</html>

<?php
pg_close($client);
?>