<ul class="mt-3 pagination justify-content-center">
    <li class="page-item">
        <a class="page-link" href="<?php echo $page_args . ($current_page - 1) ?>" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
        </a>
    </li>


    <?php
    $page_amount = getPageAmount($video_amount);

    if ($current_page > 6) { // If on over 6 page
        for ($i = $current_page-5; $i <= $current_page-5+10; $i++) {
            if ($i > $page_amount) {
                continue;
            }

            echo '<li class="page-item';

            if ($i == $current_page) {
                echo ' active';
            }

            echo '"><a class="page-link" href="' . $page_args . $i . '">' . $i . '</a></li>';
        }
    } else { // If on under 6 page
        for ($i = 1; $i <= 10; $i++) {
            if ($i > $page_amount) {
                continue;
            }

            echo '<li class="page-item';

            if ($i == $current_page) {
                echo ' active';
            }

            echo '"><a class="page-link" href="' . $page_args . $i . '">' . $i . '</a></li>';
        } 
    }?>

    <li class="page-item">
        <a class="page-link" href="<?php echo $page_args . ($current_page + 1) ?>" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
        </a>
    </li>
</ul>