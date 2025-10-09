<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add admin menu
add_action('admin_menu', 'dab_add_admin_menu');

function dab_add_admin_menu() {
    add_menu_page(
        'Doctor Appointments',
        'Appointments',
        'manage_options',
        'doctor-appointments',
        'dab_admin_page',
        'dashicons-calendar-alt',
        30
    );
}

function dab_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'doctor_appointments';
    
    // Get filter parameters
    $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
    $date_filter = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';
    
    // Build query
    $query = "SELECT * FROM $table_name WHERE 1=1";
    
    if ($status_filter) {
        $query .= $wpdb->prepare(" AND status = %s", $status_filter);
    }
    
    if ($date_filter) {
        $query .= $wpdb->prepare(" AND appointment_date = %s", $date_filter);
    }
    
    $query .= " ORDER BY appointment_date DESC, appointment_time DESC";
    
    $appointments = $wpdb->get_results($query);
    
    // Get statistics
    $total_appointments = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $pending_appointments = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'pending'");
    $confirmed_appointments = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'confirmed'");
    $completed_appointments = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'completed'");
    
    ?>
    <div class="wrap dab-admin-wrap">
        <h1 class="dab-admin-title">
            <span class="dashicons dashicons-calendar-alt"></span>
            Doctor Appointments
        </h1>
        
        <div class="dab-stats-container">
            <div class="dab-stat-card">
                <div class="stat-icon total">
                    <span class="dashicons dashicons-calendar"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo $total_appointments; ?></h3>
                    <p>Total Appointments</p>
                </div>
            </div>
            
            <div class="dab-stat-card">
                <div class="stat-icon pending">
                    <span class="dashicons dashicons-clock"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo $pending_appointments; ?></h3>
                    <p>Pending</p>
                </div>
            </div>
            
            <div class="dab-stat-card">
                <div class="stat-icon confirmed">
                    <span class="dashicons dashicons-yes-alt"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo $confirmed_appointments; ?></h3>
                    <p>Confirmed</p>
                </div>
            </div>
            
            <div class="dab-stat-card">
                <div class="stat-icon completed">
                    <span class="dashicons dashicons-saved"></span>
                </div>
                <div class="stat-content">
                    <h3><?php echo $completed_appointments; ?></h3>
                    <p>Completed</p>
                </div>
            </div>
        </div>
        
        <div class="dab-filters">
            <form method="get" action="">
                <input type="hidden" name="page" value="doctor-appointments">
                
                <select name="status" id="status-filter">
                    <option value="">All Statuses</option>
                    <option value="pending" <?php selected($status_filter, 'pending'); ?>>Pending</option>
                    <option value="confirmed" <?php selected($status_filter, 'confirmed'); ?>>Confirmed</option>
                    <option value="completed" <?php selected($status_filter, 'completed'); ?>>Completed</option>
                    <option value="cancelled" <?php selected($status_filter, 'cancelled'); ?>>Cancelled</option>
                </select>
                
                <input type="date" name="date" value="<?php echo esc_attr($date_filter); ?>" placeholder="Filter by date">
                
                <button type="submit" class="button">Filter</button>
                <a href="?page=doctor-appointments" class="button">Reset</a>
            </form>
        </div>
        
        <div class="dab-appointments-table-wrapper">
            <table class="wp-list-table widefat fixed striped dab-appointments-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($appointments): ?>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr data-id="<?php echo $appointment->id; ?>">
                                <td><?php echo $appointment->id; ?></td>
                                <td><strong><?php echo esc_html($appointment->patient_name); ?></strong></td>
                                <td><?php echo esc_html($appointment->patient_email); ?></td>
                                <td><?php echo esc_html($appointment->patient_phone); ?></td>
                                <td><?php echo esc_html($appointment->doctor_name); ?></td>
                                <td><?php echo date('M d, Y', strtotime($appointment->appointment_date)); ?></td>
                                <td><?php echo date('h:i A', strtotime($appointment->appointment_time)); ?></td>
                                <td><span class="reason-text"><?php echo esc_html(substr($appointment->reason, 0, 50)) . '...'; ?></span></td>
                                <td>
                                    <select class="status-select" data-id="<?php echo $appointment->id; ?>">
                                        <option value="pending" <?php selected($appointment->status, 'pending'); ?>>Pending</option>
                                        <option value="confirmed" <?php selected($appointment->status, 'confirmed'); ?>>Confirmed</option>
                                        <option value="completed" <?php selected($appointment->status, 'completed'); ?>>Completed</option>
                                        <option value="cancelled" <?php selected($appointment->status, 'cancelled'); ?>>Cancelled</option>
                                    </select>
                                </td>
                                <td>
                                    <button class="button button-small dab-delete-btn" data-id="<?php echo $appointment->id; ?>">
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 40px;">
                                <span class="dashicons dashicons-calendar-alt" style="font-size: 48px; opacity: 0.3;"></span>
                                <p style="margin-top: 10px; color: #666;">No appointments found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="dab-shortcode-info">
            <h3>How to Use</h3>
            <p>Add the booking form to any page or post using this shortcode:</p>
            <code>[doctor_appointment_form]</code>
        </div>
    </div>
    <?php
}
