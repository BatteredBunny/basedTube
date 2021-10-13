<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">
      <div id="swag-ver-num"></div><?php echo $BRANDING ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item" data-title="Upload" data-intro='Upload new videos here'>
          <a class="nav-link" href="/upload"><span class="material-icons">file_upload</span></a>
        </li>
        <li class="nav-item" data-title="Random video" data-intro='Gives you random video'>
          <a class="nav-link" href="/random"><span class="material-icons">shuffle</span></a>
        </li>
        <li class="nav-item" data-title="Forum" data-intro='Discuss something here'>
          <a class="nav-link" href="/forum"><span class="material-icons">forum</span></a>
        </li>
        <li class="nav-item" data-title="Rules" data-intro='Please follow them'>
          <a class="nav-link" href="/rules">Rules</a>
        </li>
      </ul>

      <?php
      if ($page_title != "") {
        require("page_title.php");
      }

      if (isLoggedIn($client)) {
        $username = htmlspecialchars(getLoggedInUser($client));
        require('../php/navbar/logged-in.php');
      } else {
        require('../php/navbar/logged-out.php');
      }
      ?>
    </div>
  </div>
</nav>