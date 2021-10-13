<img class='avatar user-page-avatar rounded-circle' src='<?php echo getUserAvatar($id, 80, $client) ?>'>
<h1 class='text-center'><?php echo htmlspecialchars($_GET['name']) ?></h1>

<ul class="list-group list-group-flush">
    <li class='list-group-item text-center bg-transparent text-white'>
        This user has <b><?php echo $video_amount?> video<?php if($video_amount!=1){echo "s";}?></b> which is <b><?php echo round($video_amount / $video_amount_total * 100, 2)?>%</b> of the videos amount!
    </li>
    <li class='list-group-item text-center bg-transparent text-white'>
        This user has <b><?php echo $views ?> views</b> which is <b><?php echo round($views / $views_total * 100, 2) ?>%</b> of the views!
    </li>
</ul>