<ul class="navbar-nav">
    <li class="nav-item" data-title="Your user page" data-intro='View your videos and statistics here'>
        <a class="nav-link" href="/user?name=<?php echo $username?>"><?php echo $username ?></a>
    </li>
    <li class="nav-item" data-title="Settings" data-intro='Change avatar, password and more.'>
        <a class="nav-link" href="/settings"><span class="material-icons">settings</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="/logout"><span class="material-icons">logout</span></a>
    </li>
</ul>