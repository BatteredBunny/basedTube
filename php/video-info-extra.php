<div class="divider"></div>

<div class="extra-video-info">
    <?php
    if (isset($entry['description'])) {
        echo "<p class='desc'>" . htmlspecialchars($entry['description']) . "</p>";
    }
    ?>

    <small class="video-date"><?php echo $entry['date'] ?></small>

    <?php
    if (isLoggedIn($client) && $entry['author'] == $_SESSION['user-id']) {
        require('../php/video-forms.php');
    }?>

    <button class="button-icon bg-transparent" onclick="share('<?php echo $MAIN_DOMAIN . '/watch?v=' . $_GET['v']; ?>')">
        <span id="liveToastBtn" class="material-icons can-hover">share</span>
    </button>

    <?php
    if ($entry['visibility'] == 1) {
        echo "<p class='mb-0'>This video is unlisted</p>";
    } else if ($entry['visibility'] == 2) {
        echo "<p class='mb-0'>This video is private</p>";
    }
    ?>
</div>

<div class="divider"></div>