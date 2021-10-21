<?php
function getLoggedInUser($client)
{
    $result = pg_query_params($client, "SELECT username, id FROM stuff.users WHERE id=$1", array($_SESSION["user-id"]))
        or die('Query failed: ' . pg_last_error());

    if ($result) {
        $entry = pg_fetch_array($result);

        if (isset($entry['username'])) {
            pg_free_result($result);

            return $entry['username'];
        }
    }
}

function isLoggedIn($client)
{
    if (isset($_SESSION["user-id"])) {
        $result = pg_query_params($client, "SELECT username, id FROM stuff.users WHERE id=$1", array($_SESSION["user-id"]));

        if ($result) {
            $entry = pg_fetch_array($result);

            if (isset($entry['username'])) {
                pg_free_result($result);
                return true;
            } else {
                pg_free_result($result);
                return false;
            }
        }
    } else {
        return false;
    }
}

function getUserById($client, $id)
{
    $result = pg_query_params($client, "SELECT username, id FROM stuff.users WHERE id=$1", array($id));

    if ($result) {
        $entry = pg_fetch_array($result);

        if (isset($entry['username'])) {
            pg_free_result($result);
            return $entry['username'];
        } else {
            pg_free_result($result);
            return "Unknown";
        }
    }
}

function getIdByUser($client, $username)
{
    $result = pg_query_params($client, "SELECT username, id FROM stuff.users WHERE username=$1", array($username));

    if ($result) {
        $entry = pg_fetch_array($result);

        if (isset($entry['id'])) {
            pg_free_result($result);
            return $entry['id'];
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function isAdmin($client, $id)
{
    $result = pg_query_params($client, "SELECT id FROM stuff.admins WHERE id=$1", array($id));

    if ($result) {
        $entry = pg_fetch_array($result);

        if (isset($entry['id'])) {
            pg_free_result($result);

            if ($entry['id'] == $id) {
                return true;
            } else {
                return false;
            }
        } else {
            pg_free_result($result);
            return false;
        }
    }
}

function getUserAvatar($id, $res, $client)
{
    global $MAIN_DOMAIN;
    global $CDN_DOMAIN;

    $DEFAULT = $MAIN_DOMAIN . "/assets/avatar/unknown.webp";
    if (is_null($id)) {
        return $DEFAULT;
    }

    if (file_exists("/var/www/cdn/avatar/" . $id . "/avatar.webp")) {
        return $CDN_DOMAIN . "/avatar" . $id . "/avatar.webp";
    } else {
        $result = pg_query_params($client, "SELECT avatar_id FROM stuff.users WHERE id=$1", array($id));

        if ($result) {
            $entry = pg_fetch_array($result);

            if (file_exists("/var/www/cdn/avatar/" . $id . "/" . $entry['avatar_id'] . "/avatar.webp")) {
                if ($res == 0) {
                    return $CDN_DOMAIN . '/avatar/' . $id . "/" . $entry['avatar_id'] . '/avatar.webp';
                } else {
                    return $CDN_DOMAIN . '/avatar/' . $id . "/" . $entry['avatar_id'] . '/avatar' . $res . 'x' . $res . '.webp';
                } 
            }
        }

        pg_free_result($result);
    }

    return $DEFAULT;
}

function deleteDir($dirPath)
{
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

function deleteVideo($video_id, $client)
{
    // Makes sure user is logged in
    if (isLoggedIn($client) && strlen($video_id) != 0) {

        // If admin can delete any video
        if (isAdmin($client, $_SESSION['user-id'])) {
            deleteDir('/var/www/cdn/' . $video_id . '/'); // Removes all video files

            pg_query_params($client, 'DELETE FROM stuff.comments WHERE video=$1', array($video_id)); // Deletes comments
            pg_query_params($client, 'DELETE FROM stuff.videos WHERE id=$1', array($video_id)); // Deletes video

            return true;
        } else { // If not admin
            $result = pg_query_params($client, 'SELECT file_name, author FROM stuff.videos WHERE id=$1', array($video_id));

            if ($result) {
                $entry = pg_fetch_array($result);

                if ($entry['author'] == $_SESSION['user-id']) { // Makes sure requester made the video
                    deleteDir('/var/www/cdn/' . $video_id . '/'); // Removes all video files

                    pg_query_params($client, 'DELETE FROM stuff.comments WHERE video=$1', array($video_id)); // Deletes comments
                    pg_query_params($client, 'DELETE FROM stuff.videos WHERE id=$1', array($video_id)); // Deletes video
                }
            } else {
                return false;
            }

            pg_free_result($result);
            return true;
        }
    } else {
        return false;
    }
}

function deleteComment($comment_id, $client)
{
    // Makes sure user is logged in
    if (isLoggedIn($client) && strlen($comment_id) != 0) {
        if (isAdmin($client, $_SESSION['user-id'])) {
            pg_query_params($client, 'DELETE FROM stuff.comments WHERE id=$1', array($comment_id)); // Deletes comment
            return true;
        } else {
            $result = pg_query_params($client, 'SELECT "author" FROM stuff.comments WHERE id=$1', array($comment_id)); // Finds comment author

            if ($result) {
                $entry = pg_fetch_array($result);

                if ($_SESSION['user-id'] != "" && $entry['author'] == $_SESSION['user-id']) { // Makes sure user made the comment
                    pg_query_params($client, 'DELETE FROM stuff.comments WHERE id=$1', array($comment_id)); // Deletes comment
                    return true;
                }
            } else {
                return false;
            }

            pg_free_result($result);
        }
    } else {
        return false;
    }
}

function deleteUser($user_id, $client)
{
    // Makes sure user is logged in
    if (isLoggedIn($client) && strlen($user_id) != 0) {
        if (isAdmin($client, $_SESSION['user-id'])) {
            pg_query_params($client, 'DELETE FROM stuff.comments WHERE author=$1', array($user_id)); // Finds all comments from user
            $result = pg_query_params($client, 'SELECT id FROM stuff.videos WHERE author=$1', array($user_id)); // Finds all videos from user

            while ($entry = pg_fetch_array($result)) { // Loops through all videos
                deleteVideo($entry['id'], $client); // This function also deletes all comments on videos
            }

            unlink('/var/www/cdn/avatar/' . $user_id . '/avatar.webp');
            unlink('/var/www/cdn/avatar/' . $user_id . '/avatar80x80.webp');
            unlink('/var/www/cdn/avatar/' . $user_id . '/avatar40x40.webp');

            rmdir('/var/www/cdn/avatar/' . $user_id . '/');

            pg_free_result($result);

            pg_query_params($client, 'DELETE FROM stuff.users WHERE id=$1', array($user_id)); // Deletes user

            return true;
        } else if ($_SESSION['user-id'] != "" && $user_id == $_SESSION['user-id']) {
            pg_query_params($client, 'DELETE FROM stuff.comments WHERE author=$1', array($user_id)); // Finds all comments from user
            $result = pg_query_params($client, 'SELECT id FROM stuff.videos WHERE author=$1', array($user_id)); // Finds all videos from user

            while ($entry = pg_fetch_array($result)) { // Loops through all videos
                deleteVideo($entry['id'], $client); // This function also deletes all comments on videos
            }

            pg_free_result($result);

            pg_query_params($client, 'DELETE FROM stuff.users WHERE id=$1', array($user_id)); // Deletes user

            return true;
        }
    } else {
        return false;
    }
}

function changeVisibility($client, $video_id)
{
    $result = pg_query_params($client, 'SELECT visibility, author FROM stuff.videos WHERE id=$1', array($video_id));

    if ($result) {
        $entry = pg_fetch_array($result);

        if ($entry['author'] == $_SESSION['user-id']) {
            $visibility = $entry['visibility'] + 1;
            $visibility = $visibility % 3;

            pg_query_params($client, 'UPDATE stuff.videos SET visibility=$1 WHERE id=$2', array($visibility, $video_id));
        }
    }

    pg_free_result($result);
}

function getPageAmount(int $video_amount)
{
    return ceil($video_amount / 24);
}

function getUserVideoAmount($client, $id)
{
    if (isLoggedIn($client) && $_SESSION['user-id'] == $id) {
        $result = pg_query_params($client, 'SELECT * FROM stuff.videos WHERE author=$1', array($id)) or die('Query failed: ' . pg_last_error());
        return pg_num_rows($result);

        pg_free_result($result);
    } else {
        $result = pg_query_params($client, 'SELECT * FROM stuff.videos WHERE visibility=0 and author=$1', array($id)) or die('Query failed: ' . pg_last_error());
        return pg_num_rows($result);

        pg_free_result($result);
    }
}

function getVideoAmount($client)
{
    $results_amount = 0;

    $result = pg_query('SELECT * FROM stuff.videos WHERE visibility=0') or die('Query failed: ' . pg_last_error());
    $results_amount += pg_num_rows($result);

    pg_free_result($result);

    if (isLoggedIn($client)) {
        $result = pg_query_params($client, 'SELECT * FROM stuff.videos WHERE visibility != 0 and author=$1', array($_SESSION['user-id'])) or die('Query failed: ' . pg_last_error());
        $results_amount += pg_num_rows($result);

        pg_free_result($result);
    }

    return $results_amount;
}

function loopVideo($client, $video_id)
{
    $result = pg_query_params($client, 'SELECT loop_video, author FROM stuff.videos WHERE id=$1', array($video_id));

    if ($result) {
        $entry = pg_fetch_array($result);

        if ($entry['author'] == $_SESSION['user-id']) {
            if ($entry['loop_video'] == 't') {
                pg_query_params($client, 'UPDATE stuff.videos SET loop_video=false WHERE id=$1', array($video_id));
            } else {
                pg_query_params($client, 'UPDATE stuff.videos SET loop_video=true WHERE id=$1', array($video_id));
            }
        }
    }

    pg_free_result($result);
}

function changeAvatar($client, $user_id)
{
    if (isLoggedIn($client) && strlen($user_id) != 0) {
        if (isAdmin($client, $_SESSION['user-id']) || ($_SESSION['user-id'] != "" && $user_id == $_SESSION['user-id'])) {
            if (isset($_FILES['avatar'])) {
                if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['avatar']['tmp_name'];
                    $fileSize = $_FILES['avatar']['size'];
                    $fileType = $_FILES['avatar']['type'];

                    // Checks that file is under 10mb
                    if ($fileSize <= 10000000) {
                        // Checks that picture is valid and basic type is image
                        if (str_starts_with($fileType, 'image/')) {
                            $ffprobe = FFMpeg\FFProbe::create();
                            $codec = $ffprobe
                                ->streams($fileTmpPath) // extracts streams informations
                                ->videos()                      // filters video streams
                                ->first()                       // returns the first video stream
                                ->get('codec_name');

                            // Checks if it has right codec to detected renamed files
                            if ($codec == "png" || "mjpeg" || "webp" || "gif" || "apng") {
                                $base_folder = "/var/www/cdn/avatar/" .  $user_id . "/";

                                if (!is_dir($base_folder)) { // If avatar folder doesnt exist yet, creats it
                                    mkdir($base_folder, 0775, true);
                                }

                                // Fetches current avatar id and deletes current files
                                $result = pg_query_params($client, "SELECT avatar_id FROM stuff.users WHERE id=$1;", array($user_id));
                                if ($result) {
                                    $entry = pg_fetch_array($result);

                                    deleteDir($base_folder . $entry['avatar_id'] . "/");
                                }
                                pg_free_result($result);

                                // Updates avatar id and moves new files
                                $result = pg_query_params($client, "UPDATE stuff.users SET avatar_id=gen_random_uuid() WHERE id=$1 RETURNING avatar_id;", array($user_id));
                                if ($result) {
                                    $entry = pg_fetch_array($result);

                                    mkdir($base_folder . $entry['avatar_id'] . "/", 0775, true);

                                    // Makes the new avatar
                                    $no_hang = ">/dev/null 2>&1";
                                    shell_exec('ffmpeg -y -i ' . $fileTmpPath . ' ' . $base_folder . $entry['avatar_id'] . '/avatar.webp ' . $no_hang);
                                    shell_exec('ffmpeg -y -i ' . $fileTmpPath . ' -s 80x80 ' . $base_folder . $entry['avatar_id'] . '/avatar80x80.webp ' . $no_hang);
                                    shell_exec('ffmpeg -y -i ' . $fileTmpPath . ' -s 40x40 ' . $base_folder . $entry['avatar_id'] . '/avatar40x40.webp ' . $no_hang);
                                }

                                pg_free_result($result);
                                return true;
                            } else {
                                echo "<p>Sorry " . $codec . " is not support</p>";
                                return false;
                            }
                        } else {
                            echo "<p>Sorry, you have to upload an image</p>";
                            return false;
                        }
                    } else {
                        echo "<p>Sorry, the uploaded image is over 10mb</p>";
                        return false;
                    }
                }
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function getViewsAmount($client, $user_id)
{
    $views = 0;
    $result = pg_query_params($client, 'SELECT SUM(views) FROM stuff.videos WHERE author=$1', array($user_id));

    if ($result) {
        $entry = pg_fetch_array($result);
        $views += $entry['sum'];
    }

    pg_free_result($result);

    return $views;
}

function getViewsTotal()
{
    $views = 0;
    $result = pg_query('SELECT SUM(views) FROM stuff.videos');

    if ($result) {
        $entry = pg_fetch_array($result);
        $views += $entry['sum'];
    }

    pg_free_result($result);

    return $views;
}

function get_pagination(int $current_page, string $page_args, int $video_amount)
{
    if (ceil($video_amount / 25) > 1) {
        require('pagination.php');
    }
}

function getVideoThumbnail($entry)
{
    global $CDN_DOMAIN;
    $folder = "/var/www/cdn/" . $entry['id'] . "/";

    if ($entry['id'] == $entry['file_id'] || $entry['file_id'] == $entry['thumbnail_id']) {
        return $CDN_DOMAIN . "/" . $entry['id'] . "/thumbnail.webp";
    }
    
    $thumbnail = $folder . $entry['thumbnail_id'] . "/thumbnail.webp";

    // Checks if it has to regenerate thumbnail, just in case.
    if (!file_exists($thumbnail)) {
        createThumbnail($folder, $entry, $entry['file_name']);
    }

    return $CDN_DOMAIN . "/" . $entry['id'] . "/" . $entry['thumbnail_id'] . "/thumbnail.webp";
}

function createThumbnail($folder, $entry, $file_name) {
    shell_exec('ffmpeg -y -i ' . $folder . $entry['file_id'] . "/" . $file_name . ' -s 210x117 -vframes 1 ' . $folder . $entry['thumbnail_id'] . "/thumbnail.webp >/dev/null 2>&1");
}