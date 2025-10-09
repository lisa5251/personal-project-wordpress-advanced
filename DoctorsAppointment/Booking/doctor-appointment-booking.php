<?php
/**
 * Plugin Name: Doctor Appointment Booking
 * Plugin URI: https://example.com
 * Description: A simple doctor appointment booking system for WordPress
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * Text Domain: doctor-appointment-booking
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('DAB_VERSION', '1.0.0');
define('DAB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DAB_PLUGIN_URL', plugin_dir_url(__FILE__));

// Activation hook
register_activation_hook(__FILE__, 'dab_activate_plugin');

function dab_activate_plugin() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'doctor_appointments';
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        patient_name varchar(100) NOT NULL,
        patient_email varchar(100) NOT NULL,
        patient_phone varchar(20) NOT NULL,
        appointment_date date NOT NULL,
        appointment_time time NOT NULL,
        doctor_name varchar(100) NOT NULL,
        reason text NOT NULL,
        status varchar(20) DEFAULT 'pending',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Add plugin version
    add_option('dab_version', DAB_VERSION);
}

// Enqueue styles and scripts
add_action('wp_enqueue_scripts', 'dab_enqueue_scripts');

function dab_enqueue_scripts() {
    wp_enqueue_style('dab-styles', DAB_PLUGIN_URL . 'assets/css/style.css', array(), DAB_VERSION);
    wp_enqueue_script('dab-scripts', DAB_PLUGIN_URL . 'assets/js/script.js', array('jquery'), DAB_VERSION, true);
    
    // Localize script for AJAX
    wp_localize_script('dab-scripts', 'dabAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('dab_nonce')
    ));
}

// Admin styles and scripts
add_action('admin_enqueue_scripts', 'dab_admin_enqueue_scripts');

function dab_admin_enqueue_scripts($hook) {
    if ($hook !== 'toplevel_page_doctor-appointments') {
        return;
    }
    
    wp_enqueue_style('dab-admin-styles', DAB_PLUGIN_URL . 'assets/css/admin-style.css', array(), DAB_VERSION);
    wp_enqueue_script('dab-admin-scripts', DAB_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), DAB_VERSION, true);
    
    wp_localize_script('dab-admin-scripts', 'dabAdminAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('dab_admin_nonce')
    ));
}

// Include required files
require_once DAB_PLUGIN_DIR . 'includes/shortcodes.php';
require_once DAB_PLUGIN_DIR . 'includes/ajax-handlers.php';
require_once DAB_PLUGIN_DIR . 'includes/admin-menu.php';
