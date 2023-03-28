<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
    <a class="sidebar-brand brand-logo" href="/admin/"><img src="/admin/assets/images/logo.svg" alt="logo" /></a>
    <a class="sidebar-brand brand-logo-mini" href="/admin/"><img src="/admin/assets/images/logo-mini.svg" alt="logo" /></a>
  </div>
  <ul class="nav">
    <li class="nav-item profile">
      <div class="profile-desc">
        <div class="profile-pic">
          <div class="count-indicator">
            <img class="img-xs rounded-circle" src="/assets/images/icons/admin.png">
            <span class="count bg-success"></span>
          </div>
          <div class="profile-name">
            <h5 class="mb-0 text-capitalize font-weight-normal"><?= $dataUser['surnameUser'] .' '. $dataUser['nameUser']; ?></h5>
            <span class="text-capitalize">Admin</span>
          </div>
        </div>
      </div>
    </li>
    <li class="nav-item nav-category">
      <span class="nav-link">Navigation</span>
    </li>
    
    <li class="nav-item menu-items">
      <a class="nav-link" href="/admin/pages/user/utilisateur.php">
        <span class="menu-icon">
          <i class="mdi mdi-account-circle"></i>
        </span>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <li class="nav-item menu-items">
      <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
        <span class="menu-icon">
          <i class="mdi mdi-shopping"></i>
        </span>
        <span class="menu-title">Boutique</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="/admin/pages/boutique/article.php">Articles</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item menu-items">
      <a class="nav-link" href="/admin/pages/photo/photo.php">
        <span class="menu-icon">
          <i class="mdi mdi-image-multiple"></i>
        </span>
        <span class="menu-title">Photos</span>
      </a>
    </li>
    
    <li class="nav-item menu-items">
      <a class="nav-link" href="/admin/pages/tag/tag.php">
        <span class="menu-icon">
          <i class="mdi mdi-label"></i>
        </span>
        <span class="menu-title">Tag</span>
      </a>
    </li>

    <li class="nav-item menu-items">
      <a class="nav-link" href="/admin/pages/config/config.php">
        <span class="menu-icon">
          <i class="mdi mdi-settings"></i>
        </span>
        <span class="menu-title">Configuration</span>
      </a>
    </li>

    <li class="nav-item menu-items">
      <a class="nav-link" href="/admin/pages/icons/mdi.html">
        <span class="menu-icon">
          <i class="mdi mdi-file-document"></i>
        </span>
        <span class="menu-title">Demo</span>
      </a>
    </li>

  </ul>
</nav>