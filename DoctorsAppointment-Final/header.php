<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header class="site-header">
        <div class="container">
            <a href="<?php echo home_url(); ?>" class="site-logo">
                MediBook
            </a>
            <nav class="site-nav">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'fallback_cb' => function() {
                        echo '<ul>';
                        echo '<li><a href="' . home_url() . '">Home</a></li>';
                        echo '<li><a href="' . home_url('/doctors') . '">Doctors</a></li>';
                        echo '<li><a href="' . home_url('/book-appointment') . '">Book Appointment</a></li>';
                        echo '<li><a href="' . admin_url() . '">Admin</a></li>';
                        echo '</ul>';
                    }
                ));
                ?>
            </nav>
        </div>
    </header>
    <main class="site-content">
