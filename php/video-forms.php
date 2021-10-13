<form class="inline" action="/watch?v=<?php echo $entry['id']?>" method="post">
    <input type="text" name="change-visibility" value="<?php echo $entry['id']?>" hidden>
    <button class="button-icon bg-transparent" type="submit">
        <span class="can-hover material-icons"><?php if ($entry['visibility'] == 0) { echo 'visibility';} else { echo 'visibility_off';}?></span>
    </button>
</form>

<form class="inline video-deletion-form" action="/watch?v=<?php echo $entry['id']?>" method="post">
    <input type="text" name="loop-video" value="<?php echo $entry['id']?>" hidden>
    <button class="button-icon bg-transparent" type="submit">
        <span class="<?php if ($entry['loop_video'] == 't') { echo "text-success"; }?> can-hover material-icons">loop</span>
    </button>
</form>

<form class="inline video-deletion-form" onsubmit="return confirm('Do you really want to delete this video?');" action="/watch?v=<?php echo $entry['id']?>" method="post">
    <input type="text" name="delete-video" value="<?php echo $entry['id']?>" hidden>
    <button class="button-icon bg-transparent" type="submit">
        <span class="can-hover material-icons">delete_forever</span>
    </button>
</form>