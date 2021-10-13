<div class="video-info">
    <p class="title"><?php echo htmlspecialchars($entry['name'])?></p>
    <img alt="User's avatar" class='avatar rounded-circle' src='<?php echo getUserAvatar($entry['author'], 40, $client) ?>'>
    <?php 
        if ($entry['author']) {
            echo '<a class="username" href="/user?name=' . getUserById($client, $entry['author']) . '">' . htmlspecialchars(getUserById($client, $entry['author'])) . '</a>';
        } else {
            echo '<p class="username">Unknown</p>';
        }
    ?>
    <p class="extra"><?php echo $entry['views']?> views • <?php echo time_elapsed_string($entry['date']) ?></p>  
</div>

