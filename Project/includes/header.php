<?php
/**
 * Header template with navigation
 */

$current_page = basename($_SERVER['PHP_SELF']);
$is_logged_in = isset($_SESSION['user_id']);
$user_is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="<?php echo SITE_URL; ?>/index.php" class="logo">
                    <h1><?php echo SITE_NAME; ?></h1>
                </a>
                <nav class="nav" id="nav">
                    <a href="<?php echo SITE_URL; ?>/index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a>
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact</a>
                    
                    <?php if ($is_logged_in): ?>
                        <?php if ($user_is_admin): ?>
                            <a href="<?php echo SITE_URL; ?>/dashboard/index.php" class="nav-link <?php echo strpos($current_page, 'dashboard') !== false ? 'active' : ''; ?>">Dashboard</a>
                        <?php endif; ?>
                        <a href="<?php echo SITE_URL; ?>/profile.php" class="nav-link">Profile</a>
                        <a href="<?php echo SITE_URL; ?>/logout.php" class="nav-link btn-logout">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/login.php" class="nav-link">Login</a>
                        <a href="<?php echo SITE_URL; ?>/register.php" class="nav-link btn-primary">Register</a>
                    <?php endif; ?>
                </nav>
                <button class="mobile-toggle" id="mobileToggle">☰</button>
            </div>
        </div>
    </header>

