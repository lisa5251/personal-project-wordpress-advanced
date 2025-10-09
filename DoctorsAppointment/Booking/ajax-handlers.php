<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Handle appointment booking submission
add_action('wp_ajax_dab_book_appointment', 'dab_handle_booking');
add_action('wp_ajax_nopriv_dab_book_appointment', 'dab_handle_booking');

function dab_handle_booking() {
    // Verify nonce
    check_ajax_referer('dab_nonce', 'nonce');
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'doctor_appointments';
    
    // Sanitize input data
    $patient_name = sanitize_text_field($_POST['patient_name']);
    $patient_email = sanitize_email($_POST['patient_email']);
    $patient_phone = sanitize_text_field($_POST['patient_phone']);
    $appointment_date = sanitize_text_field($_POST['appointment_date']);
    $appointment_time = sanitize_text_field($_POST['appointment_time']);
    $doctor_name = sanitize_text_field($_POST['doctor_name']);
    $reason = sanitize_textarea_field($_POST['reason']);
    
    // Validate required fields
    if (empty($patient_name) || empty($patient_email) || empty($patient_phone) || 
        empty($appointment_date) || empty($appointment_time) || empty($doctor_name) || empty($reason)) {
        wp_send_json_error(array('message' => 'All fields are required.'));
    }
    
    // Validate email
    if (!is_email($patient_email)) {
        wp_send_json_error(array('message' => 'Please enter a valid email address.'));
    }
    
    // Check if the time slot is already booked
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name 
        WHERE appointment_date = %s 
        AND appointment_time = %s 
        AND doctor_name = %s 
        AND status != 'cancelled'",
        $appointment_date,
        $appointment_time,
        $doctor_name
    ));
    
    if ($existing > 0) {
        wp_send_json_error(array('message' => 'This time slot is already booked. Please choose another time.'));
    }
    
    // Insert appointment
    $result = $wpdb->insert(
        $table_name,
        array(
            'patient_name' => $patient_name,
            'patient_email' => $patient_email,
            'patient_phone' => $patient_phone,
            'appointment_date' => $appointment_date,
            'appointment_time' => $appointment_time,
            'doctor_name' => $doctor_name,
            'reason' => $reason,
            'status' => 'pending'
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );
    
    if ($result) {
        // Send confirmation email (optional)
        $to = $patient_email;
        $subject = 'Appointment Confirmation';
        $message = "Dear $patient_name,\n\n";
        $message .= "Your appointment has been successfully booked.\n\n";
        $message .= "Details:\n";
        $message .= "Doctor: $doctor_name\n";
        $message .= "Date: $appointment_date\n";
        $message .= "Time: $appointment_time\n\n";
        $message .= "We look forward to seeing you!\n\n";
        $message .= "Best regards,\nThe Medical Team";
        
        wp_mail($to, $subject, $message);
        
        wp_send_json_success(array('message' => 'Appointment booked successfully! You will receive a confirmation email shortly.'));
    } else {
        wp_send_json_error(array('message' => 'Failed to book appointment. Please try again.'));
    }
}

// Handle appointment status update
add_action('wp_ajax_dab_update_status', 'dab_update_appointment_status');

function dab_update_appointment_status() {
    check_ajax_referer('dab_admin_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access.'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'doctor_appointments';
    
    $appointment_id = intval($_POST['appointment_id']);
    $new_status = sanitize_text_field($_POST['status']);
    
    $result = $wpdb->update(
        $table_name,
        array('status' => $new_status),
        array('id' => $appointment_id),
        array('%s'),
        array('%d')
    );
    
    if ($result !== false) {
        wp_send_json_success(array('message' => 'Status updated successfully.'));
    } else {
        wp_send_json_error(array('message' => 'Failed to update status.'));
    }
}

// Handle appointment deletion
add_action('wp_ajax_dab_delete_appointment', 'dab_delete_appointment');

function dab_delete_appointment() {
    check_ajax_referer('dab_admin_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Unauthorized access.'));
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'doctor_appointments';
    
    $appointment_id = intval($_POST['appointment_id']);
    
    $result = $wpdb->delete(
        $table_name,
        array('id' => $appointment_id),
        array('%d')
    );
    
    if ($result) {
        wp_send_json_success(array('message' => 'Appointment deleted successfully.'));
    } else {
        wp_send_json_error(array('message' => 'Failed to delete appointment.'));
    }
}
