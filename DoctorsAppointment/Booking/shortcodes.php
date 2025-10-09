<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Register shortcode for booking form
add_shortcode('doctor_appointment_form', 'dab_booking_form_shortcode');

function dab_booking_form_shortcode() {
    ob_start();
    ?>
    <div class="dab-booking-container">
        <div class="dab-booking-form-wrapper">
            <h2 class="dab-form-title">Book Your Appointment</h2>
            <p class="dab-form-description">Fill out the form below to schedule your appointment with our doctors.</p>
            
            <form id="dab-booking-form" class="dab-form">
                <div class="dab-form-row">
                    <div class="dab-form-group">
                        <label for="patient_name">Full Name <span class="required">*</span></label>
                        <input type="text" id="patient_name" name="patient_name" required>
                    </div>
                    
                    <div class="dab-form-group">
                        <label for="patient_email">Email Address <span class="required">*</span></label>
                        <input type="email" id="patient_email" name="patient_email" required>
                    </div>
                </div>
                
                <div class="dab-form-row">
                    <div class="dab-form-group">
                        <label for="patient_phone">Phone Number <span class="required">*</span></label>
                        <input type="tel" id="patient_phone" name="patient_phone" required>
                    </div>
                    
                    <div class="dab-form-group">
                        <label for="doctor_name">Select Doctor <span class="required">*</span></label>
                        <select id="doctor_name" name="doctor_name" required>
                            <option value="">Choose a doctor</option>
                            <option value="Dr. Sarah Johnson">Dr. Sarah Johnson - General Physician</option>
                            <option value="Dr. Michael Chen">Dr. Michael Chen - Cardiologist</option>
                            <option value="Dr. Emily Rodriguez">Dr. Emily Rodriguez - Pediatrician</option>
                            <option value="Dr. James Wilson">Dr. James Wilson - Orthopedic</option>
                            <option value="Dr. Lisa Anderson">Dr. Lisa Anderson - Dermatologist</option>
                        </select>
                    </div>
                </div>
                
                <div class="dab-form-row">
                    <div class="dab-form-group">
                        <label for="appointment_date">Preferred Date <span class="required">*</span></label>
                        <input type="date" id="appointment_date" name="appointment_date" required>
                    </div>
                    
                    <div class="dab-form-group">
                        <label for="appointment_time">Preferred Time <span class="required">*</span></label>
                        <select id="appointment_time" name="appointment_time" required>
                            <option value="">Select time</option>
                            <option value="09:00:00">09:00 AM</option>
                            <option value="09:30:00">09:30 AM</option>
                            <option value="10:00:00">10:00 AM</option>
                            <option value="10:30:00">10:30 AM</option>
                            <option value="11:00:00">11:00 AM</option>
                            <option value="11:30:00">11:30 AM</option>
                            <option value="14:00:00">02:00 PM</option>
                            <option value="14:30:00">02:30 PM</option>
                            <option value="15:00:00">03:00 PM</option>
                            <option value="15:30:00">03:30 PM</option>
                            <option value="16:00:00">04:00 PM</option>
                            <option value="16:30:00">04:30 PM</option>
                        </select>
                    </div>
                </div>
                
                <div class="dab-form-group">
                    <label for="reason">Reason for Visit <span class="required">*</span></label>
                    <textarea id="reason" name="reason" rows="4" required placeholder="Please describe your symptoms or reason for the appointment"></textarea>
                </div>
                
                <div class="dab-form-message" id="dab-form-message"></div>
                
                <button type="submit" class="dab-submit-btn">
                    <span class="btn-text">Book Appointment</span>
                    <span class="btn-loader" style="display: none;">Processing...</span>
                </button>
            </form>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
