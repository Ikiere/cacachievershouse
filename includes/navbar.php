<?php
include 'includes/header.php';
?>
<header class="navbar">
  <div class="nav-container">

    <!-- Logo -->
    <div class="nav-logo">
      <img src="assets/logo/cac-logo.png" alt="Solution House Logo" width="150" height="auto">
    </div>

    <!-- Desktop Menu -->
    <nav class="nav-menu">
      <a href="#">Home</a>
      <a href="#">About Us</a>
      <a href="#">Ministries</a>
      <a href="#">Events</a>
      <a href="#">Contact</a>
    </nav>

    <!-- CTA -->
    <a href="#" class="nav-btn-2">Give Online</a>

    <!-- Hamburger -->
    <div class="hamburger" id="hamburger">
      <span></span>
      <span></span>
      <span></span>
    </div>

  </div>
</header>

<!-- Mobile Offset Menu -->
<div class="mobile-menu" id="mobileMenu">
  <button class="close-menu" id="closeMenu">&times;</button>

  <a href="#">Home</a>
  <a href="#">About Us</a>
  <a href="#">Ministries</a>
  <a href="#">Events</a>
  <a href="#">Contact</a>

  <a href="#" class="mobile-btn">Give Online</a>
</div>

<div class="overlay" id="overlay"></div>


<script>
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');
const overlay = document.getElementById('overlay');
const closeMenu = document.getElementById('closeMenu');

hamburger.onclick = () => {
  mobileMenu.classList.add('active');
  overlay.classList.add('active');
};

closeMenu.onclick = overlay.onclick = () => {
  mobileMenu.classList.remove('active');
  overlay.classList.remove('active');
};
</script>


