<?php
/**
 * MediBook Theme Functions
 */

// Create database tables on theme activation
function medibook_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    // Doctors table
    $table_doctors = $wpdb->prefix . 'medibook_doctors';
    $sql_doctors = "CREATE TABLE IF NOT EXISTS $table_doctors (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        specialty varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        phone varchar(50) NOT NULL,
        bio text,
        photo varchar(255),
        status varchar(20) DEFAULT 'active',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    // Doctor schedules table
    $table_schedules = $wpdb->prefix . 'medibook_schedules';
    $sql_schedules = "CREATE TABLE IF NOT EXISTS $table_schedules (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        doctor_id mediumint(9) NOT NULL,
        day_of_week tinyint(1) NOT NULL,
        start_time time NOT NULL,
        end_time time NOT NULL,
        slot_duration int DEFAULT 30,
        PRIMARY KEY  (id),
        KEY doctor_id (doctor_id)
    ) $charset_collate;";
    
    // Appointments table
    $table_appointments = $wpdb->prefix . 'medibook_appointments';
    $sql_appointments = "CREATE TABLE IF NOT EXISTS $table_appointments (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        doctor_id mediumint(9) NOT NULL,
        patient_name varchar(255) NOT NULL,
        patient_email varchar(255) NOT NULL,
        patient_phone varchar(50) NOT NULL,
        appointment_date date NOT NULL,
        appointment_time time NOT NULL,
        reason text,
        status varchar(20) DEFAULT 'pending',
        notes text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY doctor_id (doctor_id),
        KEY appointment_date (appointment_date)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_doctors);
    dbDelta($sql_schedules);
    dbDelta($sql_appointments);
}
add_action('after_switch_theme', 'medibook_create_tables');

function medibook_admin_menu() {
    add_menu_page(
        'MediBook',
        'MediBook',
        'manage_options',
        'medibook',
        'medibook_dashboard_page',
        'dashicons-calendar-alt',
        30
    );
    
    add_submenu_page(
        'medibook',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'medibook',
        'medibook_dashboard_page'
    );
    
    add_submenu_page(
        'medibook',
        'Manage Doctors',
        'Doctors',
        'manage_options',
        'manage-doctors',
        'medibook_doctors_page'
    );
    
    add_submenu_page(
        'medibook',
        'Manage Schedules',
        'Schedules',
        'manage_options',
        'manage-schedules',
        'medibook_schedules_page'
    );
    
    add_submenu_page(
        'medibook',
        'Appointments',
        'Appointments',
        'manage_options',
        'manage-appointments',
        'medibook_appointments_page'
    );
}
add_action('admin_menu', 'medibook_admin_menu');

// Admin page callbacks
function medibook_dashboard_page() {
    include get_template_directory() . '/admin/dashboard.php';
}

function medibook_doctors_page() {
    include get_template_directory() . '/admin/manage-doctors.php';
}

function medibook_schedules_page() {
    include get_template_directory() . '/admin/manage-schedules.php';
}

function medibook_appointments_page() {
    include get_template_directory() . '/admin/manage-appointments.php';
}

// Enqueue scripts and styles
function medibook_enqueue_scripts() {
    wp_enqueue_style('medibook-style', get_stylesheet_uri());
    wp_enqueue_script('medibook-booking', get_template_directory_uri() . '/js/booking.js', array(), '1.0', true);
    
    // Pass AJAX URL to JavaScript
    wp_localize_script('medibook-booking', 'medibook_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('medibook_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'medibook_enqueue_scripts');

// Register menus
function medibook_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'medibook'),
    ));
}
add_action('init', 'medibook_register_menus');

// AJAX handler for getting available time slots
function medibook_get_time_slots() {
    check_ajax_referer('medibook_nonce', 'nonce');
    
    global $wpdb;
    $doctor_id = intval($_POST['doctor_id']);
    $date = sanitize_text_field($_POST['date']);
    
    $day_of_week = date('w', strtotime($date));
    
    // Get doctor's schedule for this day
    $schedule = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}medibook_schedules 
        WHERE doctor_id = %d AND day_of_week = %d",
        $doctor_id, $day_of_week
    ));
    
    if (!$schedule) {
        wp_send_json_error(array('message' => 'No schedule available for this day'));
        return;
    }
    
    // Get booked appointments for this date
    $booked_times = $wpdb->get_col($wpdb->prepare(
        "SELECT appointment_time FROM {$wpdb->prefix}medibook_appointments 
        WHERE doctor_id = %d AND appointment_date = %s AND status != 'cancelled'",
        $doctor_id, $date
    ));
    
    // Generate time slots
    $slots = array();
    $start = strtotime($schedule->start_time);
    $end = strtotime($schedule->end_time);
    $duration = $schedule->slot_duration * 60; // Convert to seconds
    
    for ($time = $start; $time < $end; $time += $duration) {
        $slot_time = date('H:i:s', $time);
        $slots[] = array(
            'time' => $slot_time,
            'display' => date('g:i A', $time),
            'available' => !in_array($slot_time, $booked_times)
        );
    }
    
    wp_send_json_success($slots);
}
add_action('wp_ajax_get_time_slots', 'medibook_get_time_slots');
add_action('wp_ajax_nopriv_get_time_slots', 'medibook_get_time_slots');

// AJAX handler for booking appointment
function medibook_book_appointment() {
    check_ajax_referer('medibook_nonce', 'nonce');
    
    global $wpdb;
    
    $data = array(
        'doctor_id' => intval($_POST['doctor_id']),
        'patient_name' => sanitize_text_field($_POST['patient_name']),
        'patient_email' => sanitize_email($_POST['patient_email']),
        'patient_phone' => sanitize_text_field($_POST['patient_phone']),
        'appointment_date' => sanitize_text_field($_POST['appointment_date']),
        'appointment_time' => sanitize_text_field($_POST['appointment_time']),
        'reason' => sanitize_textarea_field($_POST['reason']),
        'status' => 'pending'
    );
    
    // Check if slot is still available
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}medibook_appointments 
        WHERE doctor_id = %d AND appointment_date = %s AND appointment_time = %s AND status != 'cancelled'",
        $data['doctor_id'], $data['appointment_date'], $data['appointment_time']
    ));
    
    if ($existing > 0) {
        wp_send_json_error(array('message' => 'This time slot is no longer available'));
        return;
    }
    
    $result = $wpdb->insert(
        $wpdb->prefix . 'medibook_appointments',
        $data
    );
    
    if ($result) {
        wp_send_json_success(array(
            'message' => 'Appointment booked successfully!',
            'appointment_id' => $wpdb->insert_id
        ));
    } else {
        wp_send_json_error(array('message' => 'Failed to book appointment'));
    }
}
add_action('wp_ajax_book_appointment', 'medibook_book_appointment');
add_action('wp_ajax_nopriv_book_appointment', 'medibook_book_appointment');

// Helper function to get all doctors
function medibook_get_doctors() {
    global $wpdb;
    return $wpdb->get_results(
        "SELECT * FROM {$wpdb->prefix}medibook_doctors WHERE status = 'active' ORDER BY name"
    );
}

// Helper function to get doctor by ID
function medibook_get_doctor($doctor_id) {
    global $wpdb;
    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}medibook_doctors WHERE id = %d",
        $doctor_id
    ));
}

// Helper function to get doctor's schedule
function medibook_get_doctor_schedule($doctor_id) {
    global $wpdb;
    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}medibook_schedules WHERE doctor_id = %d ORDER BY day_of_week, start_time",
        $doctor_id
    ));
}
?>
