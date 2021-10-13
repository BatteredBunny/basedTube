<div class="video-player">
    <video id="my-video" class="video-js" preload="auto" data-setup='{ "controls": true, "autoplay": true, "preload": "auto", "fluid": true }' <?php if ($entry['loop_video'] == 't') { echo 'loop'; }?> autoplay controls>
        <source src='<?php
        echo $CDN_DOMAIN . "/";

        echo $entry['id'] . "/";

        if ($entry['id'] != $entry['file_id']) {
            echo $entry['file_id'] . "/";
        }
        echo $entry['file_name'];
        
        ?>' type='video/mp4'>
    </video> 

    <?php 
    require('../php/video-info.php');
    require('../php/video-info-extra.php');
    ?>
</div>
