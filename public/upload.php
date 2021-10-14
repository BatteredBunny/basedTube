<?php
require('../php/header.php');
$page_title = 'Upload';
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
    <meta name="description" content="Upload new video">
    <meta name="theme-color" content="#212529">
        
    <!-- facebook meta info -->
    <meta property="og:title" content="<?php echo $page_title?>" />
    <meta property="og:description" content="Upload new video">
    <meta property="og:image" content="/favicon.ico" />
    <meta property="og:url" content="/upload" />

    <!-- twitter meta info -->
    <meta name="twitter:title" content="<?php echo $page_title?>" />
    <meta name="twitter:description" content="Upload new video" />
    <meta name="twitter:image" content="/favicon.ico" />

    <?php require('../php/page-deps.php') ?>

    <script>
        function encodingWarning() {
            document.getElementById('encodingWarning').hidden = false;
        }
    </script>
</head>

<body>
    <?php
    require('../php/navbar.php');
    ?>

    <div class="container mt-4 mb-4">
        <?php
        //Uploads video
        if (isset($_POST['video_name']) && isset($_FILES['video_file'])) {
            if (strlen($_POST['video_name']) > 70) { // If title over 70 char
                echo "<p>Video title can't be over 70 characters</p>";
            } else if (strlen($_POST['video_desc']) > 200) { // If desc over 200 char
                echo "<p>Description can't be over 200 characters</p>";
            } else if (strlen($_POST['video_desc']) > 200) { // If desc over 200 char
                echo "<p>Description can't be over 200 characters</p>";
            } else if (strlen($_POST['video_name']) == 0 || strlen(trim($_POST['video_name'])) == 0) {
                echo "<p>Video title can't be empty!!!</p>";
            } else {
                if ($_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['video_file']['tmp_name'];
                    $fileName = $_FILES['video_file']['name'];
                    $fileSize = $_FILES['video_file']['size'];
                    $fileType = $_FILES['video_file']['type'];

                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    $videoName = str_replace(" ", "_", $_POST['video_name']);
                    $videoName = preg_replace("/[^a-z0-9._]+/i", "", $videoName);
                    $extension = "." . $fileExtension;

                    $ffprobe = FFMpeg\FFProbe::create();
                    if ($ffprobe->isValid($fileTmpPath) && str_starts_with($fileType, 'video/')) {
                        $codex = $ffprobe
                            ->streams($fileTmpPath) // extracts streams informations
                            ->videos()                      // filters video streams
                            ->first()                       // returns the first video stream
                            ->get('codec_name');

                        if ($codex == "h264" || "h265" || "vp8" || "vp9") {
                            if (isset($_POST['loop_video'])) {
                                $loop_video = 't';
                            } else {
                                $loop_video = 'f';
                            }

                            $visibility = $_POST['visibility'];

                            if ($visibility > 2) {
                                $visibility = 0;
                            }

                            // Adds new video entry
                            if (isLoggedIn($client)) {
                                $result = pg_query_params($client,
                                "INSERT INTO stuff.videos (name, file_name, description, author, creation_ip, visibility, loop_video)
                                VALUES ('Video is uploading', $1, $2, $3, $4, 2, $5) RETURNING id, file_id, thumbnail_id, file_name;",
                                array($videoName . $extension, $_POST['video_desc'], $_SESSION['user-id'], $_SERVER['REMOTE_ADDR'], $loop_video))
                                or die('Query failed: ' . pg_last_error());
                            } else {
                                $visibility = 0;

                                $result = pg_query_params($client,
                                "INSERT INTO stuff.videos (name, file_name, description, creation_ip, visibility, loop_video)
                                VALUES ('Video is uploading', $1, $2, $3, 2, $4) RETURNING id, file_id, thumbnail_id, file_name;",
                                array($videoName . $extension, $_POST['video_desc'], $_SERVER['REMOTE_ADDR'], $loop_video))
                                or die('Query failed: ' . pg_last_error());
                            }

                            if ($result) {
                                // Gets newly created video ID
                                $entry = pg_fetch_array($result);
                                $folder = "../cdn/" .  $entry['id'] . "/";

                                // Moves and renames video file
                                mkdir($folder, 0775, true);

                                mkdir($folder . $entry['file_id'] . "/", 0775, true); // Creates video file folder
                                mkdir($folder . $entry['thumbnail_id'] . "/", 0775, true); // Creates thumbnail file folder

                                // Moves the video file
                                rename($fileTmpPath, $folder . $entry['file_id'] . "/" . $videoName . $extension);

                                // Encodes video into av1
                                // shell_exec('ffmpeg -y -i ' . $fileTmpPath . ' -c:v libsvt_av1 -crf 23 -b:v 0 ' . $folder . $videoName . $extension . " >/dev/null 2>&1");

                                createThumbnail($folder, $entry, $videoName);
                                pg_query_params($client, 'UPDATE stuff.videos SET name=$1, visibility=$2 WHERE id=$3', array($_POST['video_name'], $visibility, $entry['id']));
                            }

                            pg_free_result($result);

                            header('Location: /');
                            exit();
                        } else {
                            echo "<p>You either didnt upload video or the video codec is not supported yet! (" . $codex . ")</p>";
                        }
                    } else {
                        echo "<p>Please only upload video files!!</p>";
                    }
                } else {
                    $phpFileUploadErrors = array(
                        0 => 'There is no error, the file uploaded with success',
                        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                        3 => 'The uploaded file was only partially uploaded',
                        4 => 'No file was uploaded',
                        6 => 'Missing a temporary folder',
                        7 => 'Failed to write file to disk.',
                        8 => 'A PHP extension stopped the file upload.',
                    );

                    echo $phpFileUploadErrors[$_FILES['video_file']['error']];
                }
            }
        }
        ?>

        <form action="/upload" method="post" enctype="multipart/form-data">
            <div class="input-group mb-2">
                <span class="input-group-text">Title</span>
                <input autocomplete="off" type="text" class="form-control" id="video_name" name="video_name" placeholder="(max 70 char)" required>
            </div>

            <div class="mb-2 input-group">
                <span class="input-group-text">Description</span>
                <textarea autocomplete="off" placeholder="(max 200 char)" name="video_desc" class="form-control" aria-label="Description"></textarea>
            </div>

            <div class="mb-3">
                <input accept="video/*" autocomplete="off" class="form-control" name="video_file" id="video_file" type="file" aria-describedby="file_disclaimer" required>
                <div id="file_disclaimer" class="form-text">200mb max</div>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" name="loop_video" type="checkbox" value="true" id="loop_video">
                <label class="form-check-label" for="loop_video">
                    Loop video?
                </label>
            </div>
            <?php if (isLoggedIn($client)) {
                require('../php/upload-visibility.php');
            } else {
                require('../php/upload-visibility2.php');
                echo '<p class="text-danger">Warning! you are logged out.</p>';
            }
            ?>

            <button type="submit" onclick="encodingWarning()" class="btn btn-outline-primary">Submit</button>
            <p id="encodingWarning" class="text-warning mt-2" hidden>Just a heads-up that video encoding can take a long time. Please be patient when uploading.</p>
        </form>


    </div>
</body>

</html>

<?php
pg_close($client);
?>