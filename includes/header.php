<?php
// =============================
// INITIAL SETUP
// =============================
// Include database config and authentication functions
require_once 'config.php';
require_once 'auth.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Basic meta tags -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <!-- Page title -->
  <title>Recipe Network</title>

  <!-- Local stylesheet with cache-busting version query -->
  <link rel="stylesheet" href="/recipe_network/assets/css/style.css?v=1">

  <!-- Font Awesome icon library -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>

<!-- ============================= -->
<!-- NAVIGATION BAR -->
<!-- ============================= -->
<nav class="navbar">
  <!-- Brand logo + link -->
  <a href="/recipe_network/index.php" class="brand">🍴 RecipeNetwork</a>

  <!-- Navigation links -->
  <div class="nav-links">
    <!-- Always show Recipes link -->
    <a href="/recipe_network/index.php"><i class="fas fa-book-open"></i> Recipes</a>

    <!-- Show logged-in menu -->
    <?php if (is_logged_in()): ?>
      <a href="/recipe_network/profile.php"><i class="fas fa-user"></i> Profile</a>
      <a href="/recipe_network/add_recipe.php"><i class="fas fa-plus"></i> New</a>
      <a href="/recipe_network/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>

    <!-- Show guest menu -->
    <?php else: ?>
      <a href="/recipe_network/register.php"><i class="fas fa-user-plus"></i> Register</a>
      <a href="/recipe_network/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
    <?php endif; ?>
  </div>
</nav>

<!-- ============================= -->
<!-- PAGE CONTENT CONTAINER -->
<!-- ============================= -->
<div class="content">
