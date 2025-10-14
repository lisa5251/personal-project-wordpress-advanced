<?php
/**
 * Sample Data Installation Script
 * Run this file once to populate the database with sample doctors and schedules
 */

// Load WordPress
require_once('../../wp-load.php');

global $wpdb;

echo "<h2>Installing Sample Data for MediBook...</h2>";

// Sample doctors data
$doctors = array(
    array(
        'name' => 'Sarah Johnson',
        'specialty' => 'General Practitioner',
        'email' => 'dr.johnson@medibook.com',
        'phone' => '(555) 123-4567',
        'bio' => 'Dr. Johnson has over 15 years of experience in family medicine. She specializes in preventive care and chronic disease management.',
        'status' => 'active'
    ),
    array(
        'name' => 'Michael Chen',
        'specialty' => 'Cardiologist',
        'email' => 'dr.chen@medibook.com',
        'phone' => '(555) 234-5678',
        'bio' => 'Board-certified cardiologist with expertise in heart disease prevention and treatment. Dr. Chen is passionate about patient education.',
        'status' => 'active'
    ),
    array(
        'name' => 'Emily Rodriguez',
        'specialty' => 'Pediatrician',
        'email' => 'dr.rodriguez@medibook.com',
        'phone' => '(555) 345-6789',
        'bio' => 'Dedicated pediatrician who loves working with children and families. Specializes in developmental pediatrics and preventive care.',
        'status' => 'active'
    ),
    array(
        'name' => 'David Thompson',
        'specialty' => 'Dermatologist',
        'email' => 'dr.thompson@medibook.com',
        'phone' => '(555) 456-7890',
        'bio' => 'Expert in medical and cosmetic dermatology with 10+ years of experience treating various skin conditions.',
        'status' => 'active'
    ),
    array(
        'name' => 'Lisa Martinez',
        'specialty' => 'Orthopedic Surgeon',
        'email' => 'dr.martinez@medibook.com',
        'phone' => '(555) 567-8901',
        'bio' => 'Specialized in sports medicine and joint replacement surgery. Dr. Martinez helps patients regain mobility and live pain-free.',
        'status' => 'active'
    )
);

// Insert doctors
$doctor_ids = array();
foreach ($doctors as $doctor) {
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM {$wpdb->prefix}medibook_doctors WHERE email = %s",
        $doctor['email']
    ));
    
    if (!$existing) {
        $wpdb->insert(
            $wpdb->prefix . 'medibook_doctors',
            $doctor
        );
        $doctor_ids[] = $wpdb->insert_id;
        echo "<p>✓ Added Dr. {$doctor['name']} - {$doctor['specialty']}</p>";
    } else {
        $doctor_ids[] = $existing;
        echo "<p>- Dr. {$doctor['name']} already exists</p>";
    }
}

// Sample schedules (Monday to Friday, 9 AM to 5 PM with lunch break)
$schedules = array(
    // Morning slots: 9:00 AM - 12:00 PM
    array('start_time' => '09:00:00', 'end_time' => '12:00:00', 'slot_duration' => 30),
    // Afternoon slots: 1:00 PM - 5:00 PM
    array('start_time' => '13:00:00', 'end_time' => '17:00:00', 'slot_duration' => 30),
);

echo "<h3>Adding Schedules...</h3>";

foreach ($doctor_ids as $doctor_id) {
    // Add schedule for Monday to Friday (1-5)
    for ($day = 1; $day <= 5; $day++) {
        foreach ($schedules as $schedule) {
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}medibook_schedules 
                WHERE doctor_id = %d AND day_of_week = %d AND start_time = %s",
                $doctor_id, $day, $schedule['start_time']
            ));
            
            if (!$existing) {
                $wpdb->insert(
                    $wpdb->prefix . 'medibook_schedules',
                    array(
                        'doctor_id' => $doctor_id,
                        'day_of_week' => $day,
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                        'slot_duration' => $schedule['slot_duration']
                    )
                );
            }
        }
    }
    echo "<p>✓ Added schedule for Doctor ID: {$doctor_id}</p>";
}

echo "<h3 style='color: green;'>✓ Sample data installation complete!</h3>";
echo "<p><a href='" . home_url() . "'>Go to Homepage</a> | <a href='" . home_url('/doctors') . "'>View Doctors</a></p>";
?>
