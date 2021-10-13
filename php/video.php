<div class="video">
    <a href="/watch?v=<?php echo htmlspecialchars($entry['id'])?>">
        <img width=210 height=117 alt="thumbnail for the video" class="thumbnail" src="<?php echo getVideoThumbnail($entry) ?>">
        <?php require('../php/video-info.php')?>
    </a>
</div>
