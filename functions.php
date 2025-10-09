<?php
/**
 * Minimal functions for Final Wordpress Project theme
 */

function ds_enqueue_assets() {
  
  wp_enqueue_style( 'bootstrap-cdn', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' );


  wp_enqueue_style( 'style', get_stylesheet_uri(), array(), '1.2', 'all' );


  wp_enqueue_script( 'bootstrap-cdn', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), null, true );

  // Booking form assets
  wp_enqueue_style( 'ds-booking', get_template_directory_uri() . '/css/booking.css', array('style'), '1.0' );
  wp_enqueue_script( 'ds-booking', get_template_directory_uri() . '/js/booking.js', array('jquery'), '1.0', true );


 
  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }
}
add_action( 'wp_enqueue_scripts', 'ds_enqueue_assets' );
