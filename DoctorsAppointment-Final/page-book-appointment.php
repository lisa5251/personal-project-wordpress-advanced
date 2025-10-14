<?php
/*
Template Name: Book Appointment
*/
get_header();

$doctors = medibook_get_doctors();
$selected_doctor = isset($_GET['doctor']) ? intval($_GET['doctor']) : 0;
?>

<div class="container" style="padding: 3rem 20px;">
    <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 2rem; text-align: center;">
        Book an Appointment
    </h1>
    
    <div style="max-width: 800px; margin: 0 auto;">
        <div id="booking-messages"></div>
        
        <form id="booking-form" class="card">
            <div class="form-group">
                <label class="form-label">Select Doctor *</label>
                <select id="doctor-select" name="doctor_id" class="form-select" required>
                    <option value="">Choose a doctor...</option>
                    <?php foreach ($doctors as $doctor): ?>
                        <option value="<?php echo $doctor->id; ?>" <?php selected($selected_doctor, $doctor->id); ?>>
                            Dr. <?php echo esc_html($doctor->name); ?> - <?php echo esc_html($doctor->specialty); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div id="doctor-info" style="display: none; background: var(--color-surface); padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem;">
                <!-- Doctor info will be loaded here -->
            </div>
            
            <div class="form-group">
                <label class="form-label">Select Date *</label>
                <input type="date" id="appointment-date" name="appointment_date" class="form-input" required min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div id="time-slots-container" style="display: none;">
                <label class="form-label">Select Time *</label>
                <div id="time-slots" class="time-slots">
                    <!-- Time slots will be loaded here -->
                </div>
                <input type="hidden" id="selected-time" name="appointment_time" required>
            </div>
            
            <div style="border-top: 1px solid var(--color-border); margin: 2rem 0; padding-top: 2rem;">
                <h3 style="font-size: 1.25rem; margin-bottom: 1rem;">Your Information</h3>
                
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="patient_name" class="form-input" required placeholder="John Doe">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="patient_email" class="form-input" required placeholder="john@example.com">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phone Number *</label>
                    <input type="tel" name="patient_phone" class="form-input" required placeholder="(555) 123-4567">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Reason for Visit</label>
                    <textarea name="reason" class="form-textarea" rows="4" placeholder="Please describe your symptoms or reason for the appointment..."></textarea>
                </div>
            </div>
            
            <button type="submit" class="btn btn-accent" style="width: 100%; font-size: 1.125rem; padding: 1rem;">
                Confirm Appointment
            </button>
        </form>
    </div>
</div>

<?php get_footer(); ?>
