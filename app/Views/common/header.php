<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Homeverse - Find your dream house</title>

  <!-- 
    - favicon
  -->
  <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">

  <!-- 
    - custom css link
  -->
  <link rel="stylesheet" href="<?= base_url('public/assets/css/style.css');?>">
  <link href="<?= base_url('public/assets/css/bootstrap.min.css');?>" rel="stylesheet" type="text/css" />
  <!-- 
    - google font link
  -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&family=Poppins:wght@400;500;600;700&display=swap"
    rel="stylesheet">

    <script src="<?= base_url('public/assets/js/jquery.min.js');?>"></script>
    <script src="<?= base_url('public/assets/js/bootstrap.min.js');?>"></script>
</head>

<body>
  <!-- 
    - #HEADER
  -->

  <header class="header" data-header>

    <div class="overlay" data-overlay></div>

    <div class="header-top mb-1">
      <div class="container p-1">

        <ul class="header-top-list">

          <li>
            <a href="mailto:info@homeverse.com" class="header-top-link">
              <ion-icon name="mail-outline"></ion-icon>

              <span>info@homeverse.com</span>
            </a>
          </li>

          <li>
            <a href="#" class="header-top-link">
              <ion-icon name="location-outline"></ion-icon>
              <p class="m-0">15/A, Nest Tower, NYC</p>
              <!-- <address>15/A, Nest Tower, NYC</address> -->
            </a>
          </li>

        </ul>

        <div class="wrapper">
          <ul class="header-top-social-list">

            <li>
              <a href="https://www.facebook.com/" class="header-top-social-link">
                <ion-icon name="logo-facebook"></ion-icon>
              </a>
            </li>

            <li>
              <a href="https://www.twitter.com" class="header-top-social-link">
                <ion-icon name="logo-twitter"></ion-icon>
              </a>
            </li>

            <li>
              <a href="https://www.instagram.com" class="header-top-social-link">
                <ion-icon name="logo-instagram"></ion-icon>
              </a>
            </li>

            <li>
              <a href="https://www.pinterest.com" class="header-top-social-link">
                <ion-icon name="logo-pinterest"></ion-icon>
              </a>
            </li>

          </ul>

          <button class="header-top-btn">Add Listing</button>
        </div>

      </div>
    </div>

    <div class="header-bottom p-2">
      <div class="container">

        <a href="#" class="logo">
          <img src="<?= base_url().'public/assets/images/logo.png' ?>" alt="Homeverse logo">
        </a>

        <nav class="navbar" data-navbar>

          <div class="navbar-top">

            <a href="#" class="logo">
              <img src="<?= base_url().'public/assets/images/logo.png' ?>" alt="Homeverse logo">
            </a>

            <button class="nav-close-btn" data-nav-close-btn aria-label="Close Menu">
              <ion-icon name="close-outline"></ion-icon>
            </button>

          </div>

          <div class="navbar-bottom">
            <ul class="navbar-list">

              <li>
                <a href="<?= base_url('public/') ?>" class="navbar-link" data-nav-link>Home</a>
              </li>

              <li class="nav-item dropdown">
                <a href="<?= base_url('public/projects') ?>" class="navbar-link dropdown-toggle" data-nav-link id="projectsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Projects</a>
                <ul class="dropdown-menu" aria-labelledby="projectsDropdown">
                  <li><a class="dropdown-item" href="<?= base_url('public/project/1') ?>">Neems Boro Phase 1</a></li>
                  <li><a class="dropdown-item" href="<?= base_url('public/project/2') ?>">Neems Boro Phase 2</a></li>
                  <li><a class="dropdown-item" href="<?= base_url('public/project/3') ?>">Neems Boro Phase 1 Neems Boro Phase </a></li>
                </ul>
              </li>

              <li>
                <a href="#service" class="navbar-link" data-nav-link>Service</a>
              </li>

              <li>
                <a href="#about" class="navbar-link" data-nav-link>About</a>
              </li>

              <li>
                <a href="#blog" class="navbar-link" data-nav-link>Blog</a>
              </li>

              <li>
                <a href="<?= base_url('public/contact') ?>" class="navbar-link" data-nav-link>Contact</a>
              </li>

            </ul>
          </div>

        </nav>

        <div class="header-bottom-actions">

          <!-- <button class="header-bottom-actions-btn" aria-label="Search">
            <ion-icon name="search-outline"></ion-icon>

            <span>Search</span>
          </button>

          <button class="header-bottom-actions-btn" aria-label="Profile">
            <ion-icon name="person-outline"></ion-icon>

            <span>Profile</span>
          </button>

          <button class="header-bottom-actions-btn" aria-label="Cart">
            <ion-icon name="cart-outline"></ion-icon>

            <span>Cart</span>
          </button> -->

          <button class="header-bottom-actions-btn" data-nav-open-btn aria-label="Open Menu">
            <ion-icon name="menu-outline"></ion-icon>

            <span>Menu</span>
          </button>

        </div>

      </div>
    </div>

  </header>
  <!-- <script>
  document.addEventListener('contextmenu', event => event.preventDefault());
</script> -->

