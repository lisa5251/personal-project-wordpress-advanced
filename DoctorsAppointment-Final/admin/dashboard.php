<?php
/**
 * Admin Dashboard
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Get statistics
$total_doctors = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}medibook_doctors WHERE status = 'active'");
$total_appointments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}medibook_appointments");
$pending_appointments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}medibook_appointments WHERE status = 'pending'");
$today_appointments = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}medibook_appointments WHERE appointment_date = %s",
    date('Y-m-d')
));

// Get recent appointments
$recent_appointments = $wpdb->get_results(
    "SELECT a.*, d.name as doctor_name, d.specialty 
    FROM {$wpdb->prefix}medibook_appointments a
    LEFT JOIN {$wpdb->prefix}medibook_doctors d ON a.doctor_id = d.id
    ORDER BY a.created_at DESC
    LIMIT 10"
);
?>

<div class="wrap">
    <h1>MediBook Dashboard</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
        <div style="background: white; padding: 20px; border-left: 4px solid #0891b2; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 10px 0; color: #666;">Total Doctors</h3>
            <p style="font-size: 2.5rem; font-weight: bold; margin: 0; color: #0891b2;"><?php echo $total_doctors; ?></p>
        </div>
        
        <div style="background: white; padding: 20px; border-left: 4px solid #10b981; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 10px 0; color: #666;">Total Appointments</h3>
            <p style="font-size: 2.5rem; font-weight: bold; margin: 0; color: #10b981;"><?php echo $total_appointments; ?></p>
        </div>
        
        <div style="background: white; padding: 20px; border-left: 4px solid #f59e0b; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 10px 0; color: #666;">Pending Appointments</h3>
            <p style="font-size: 2.5rem; font-weight: bold; margin: 0; color: #f59e0b;"><?php echo $pending_appointments; ?></p>
        </div>
        
        <div style="background: white; padding: 20px; border-left: 4px solid #f43f5e; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 10px 0; color: #666;">Today's Appointments</h3>
            <p style="font-size: 2.5rem; font-weight: bold; margin: 0; color: #f43f5e;"><?php echo $today_appointments; ?></p>
        </div>
    </div>
    
    <div style="background: white; padding: 20px; margin: 20px 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2>Recent Appointments</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent_appointments)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No appointments yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recent_appointments as $apt): ?>
                        <tr>
                            <td><?php echo $apt->id; ?></td>
                            <td>
                                <strong><?php echo esc_html($apt->patient_name); ?></strong><br>
                                <small><?php echo esc_html($apt->patient_email); ?></small>
                            </td>
                            <td>
                                Dr. <?php echo esc_html($apt->doctor_name); ?><br>
                                <small><?php echo esc_html($apt->specialty); ?></small>
                            </td>
                            <td>
                                <?php echo date('M j, Y', strtotime($apt->appointment_date)); ?><br>
                                <small><?php echo date('g:i A', strtotime($apt->appointment_time)); ?></small>
                            </td>
                            <td>
                                <?php
                                $status_colors = array(
                                    'pending' => '#f59e0b',
                                    'confirmed' => '#10b981',
                                    'cancelled' => '#ef4444',
                                    'completed' => '#0891b2'
                                );
                                $color = isset($status_colors[$apt->status]) ? $status_colors[$apt->status] : '#666';
                                ?>
                                <span style="background: <?php echo $color; ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                    <?php echo ucfirst($apt->status); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($apt->created_at)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div style="background: #e0f2fe; border-left: 4px solid #0891b2; padding: 15px; margin: 20px 0;">
        <h3 style="margin-top: 0;">Quick Links</h3>
        <p>
            <a href="admin.php?page=manage-doctors" class="button button-primary">Manage Doctors</a>
            <a href="admin.php?page=manage-schedules" class="button">Manage Schedules</a>
            <a href="admin.php?page=manage-appointments" class="button">View All Appointments</a>
            <a href="<?php echo home_url(); ?>" class="button" target="_blank">View Website</a>
        </p>
    </div>
</div>
