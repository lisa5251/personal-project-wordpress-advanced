<?php
/**
 * Admin page for managing appointments
 * Access: wp-admin/admin.php?page=manage-appointments
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'medibook_appointment_action')) {
        wp_die('Security check failed');
    }
    
    if ($_POST['action'] === 'update_status' && isset($_POST['appointment_id']) && isset($_POST['status'])) {
        $wpdb->update(
            $wpdb->prefix . 'medibook_appointments',
            array('status' => sanitize_text_field($_POST['status'])),
            array('id' => intval($_POST['appointment_id']))
        );
        echo '<div class="notice notice-success"><p>Appointment status updated successfully!</p></div>';
    }
    
    if ($_POST['action'] === 'add_notes' && isset($_POST['appointment_id']) && isset($_POST['notes'])) {
        $wpdb->update(
            $wpdb->prefix . 'medibook_appointments',
            array('notes' => sanitize_textarea_field($_POST['notes'])),
            array('id' => intval($_POST['appointment_id']))
        );
        echo '<div class="notice notice-success"><p>Notes added successfully!</p></div>';
    }
    
    if ($_POST['action'] === 'delete_appointment' && isset($_POST['appointment_id'])) {
        $wpdb->delete(
            $wpdb->prefix . 'medibook_appointments',
            array('id' => intval($_POST['appointment_id']))
        );
        echo '<div class="notice notice-success"><p>Appointment deleted successfully!</p></div>';
    }
}

// Get filter parameters
$filter_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$filter_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';
$filter_doctor = isset($_GET['doctor']) ? intval($_GET['doctor']) : 0;

// Build query
$where = array('1=1');
if ($filter_status) {
    $where[] = $wpdb->prepare("a.status = %s", $filter_status);
}
if ($filter_date) {
    $where[] = $wpdb->prepare("a.appointment_date = %s", $filter_date);
}
if ($filter_doctor) {
    $where[] = $wpdb->prepare("a.doctor_id = %d", $filter_doctor);
}

$where_clause = implode(' AND ', $where);

// Get appointments
$appointments = $wpdb->get_results(
    "SELECT a.*, d.name as doctor_name, d.specialty 
    FROM {$wpdb->prefix}medibook_appointments a
    LEFT JOIN {$wpdb->prefix}medibook_doctors d ON a.doctor_id = d.id
    WHERE $where_clause
    ORDER BY a.appointment_date DESC, a.appointment_time DESC"
);

// Get all doctors for filter
$doctors = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}medibook_doctors ORDER BY name");
?>

<div class="wrap">
    <h1>Manage Appointments</h1>
    
    <!-- Filters -->
    <div style="background: white; padding: 15px; margin: 20px 0; border: 1px solid #ccc; border-radius: 4px;">
        <form method="get" action="">
            <input type="hidden" name="page" value="manage-appointments">
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
                <div>
                    <label for="status" style="display: block; margin-bottom: 5px; font-weight: 600;">Status</label>
                    <select id="status" name="status" style="min-width: 150px;">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php selected($filter_status, 'pending'); ?>>Pending</option>
                        <option value="confirmed" <?php selected($filter_status, 'confirmed'); ?>>Confirmed</option>
                        <option value="completed" <?php selected($filter_status, 'completed'); ?>>Completed</option>
                        <option value="cancelled" <?php selected($filter_status, 'cancelled'); ?>>Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label for="date" style="display: block; margin-bottom: 5px; font-weight: 600;">Date</label>
                    <input type="date" id="date" name="date" value="<?php echo esc_attr($filter_date); ?>" style="min-width: 150px;">
                </div>
                
                <div>
                    <label for="doctor" style="display: block; margin-bottom: 5px; font-weight: 600;">Doctor</label>
                    <select id="doctor" name="doctor" style="min-width: 200px;">
                        <option value="">All Doctors</option>
                        <?php foreach ($doctors as $doc): ?>
                            <option value="<?php echo $doc->id; ?>" <?php selected($filter_doctor, $doc->id); ?>>
                                Dr. <?php echo esc_html($doc->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <button type="submit" class="button button-primary">Filter</button>
                    <a href="admin.php?page=manage-appointments" class="button">Clear</a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Appointments Table -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>Patient Information</th>
                <th>Doctor</th>
                <th>Date & Time</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($appointments)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">
                        No appointments found. <?php if ($filter_status || $filter_date || $filter_doctor): ?>Try adjusting your filters.<?php endif; ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($appointments as $apt): ?>
                    <tr>
                        <td><?php echo $apt->id; ?></td>
                        <td>
                            <strong><?php echo esc_html($apt->patient_name); ?></strong><br>
                            <small>
                                <?php echo esc_html($apt->patient_email); ?><br>
                                <?php echo esc_html($apt->patient_phone); ?>
                            </small>
                        </td>
                        <td>
                            <strong>Dr. <?php echo esc_html($apt->doctor_name); ?></strong><br>
                            <small><?php echo esc_html($apt->specialty); ?></small>
                        </td>
                        <td>
                            <strong><?php echo date('M j, Y', strtotime($apt->appointment_date)); ?></strong><br>
                            <small><?php echo date('g:i A', strtotime($apt->appointment_time)); ?></small>
                        </td>
                        <td>
                            <?php if ($apt->reason): ?>
                                <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo esc_html(substr($apt->reason, 0, 100)); ?>
                                    <?php if (strlen($apt->reason) > 100): ?>...<?php endif; ?>
                                </div>
                            <?php else: ?>
                                <em style="color: #999;">No reason provided</em>
                            <?php endif; ?>
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
                            <span style="background: <?php echo $color; ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; display: inline-block;">
                                <?php echo ucfirst($apt->status); ?>
                            </span>
                        </td>
                        <td>
                            <button type="button" class="button button-small" onclick="toggleDetails(<?php echo $apt->id; ?>)">
                                Details
                            </button>
                        </td>
                    </tr>
                    <tr id="details-<?php echo $apt->id; ?>" style="display: none;">
                        <td colspan="7" style="background: #f9fafb; padding: 20px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div>
                                    <h3 style="margin-top: 0;">Appointment Details</h3>
                                    <p><strong>Appointment ID:</strong> <?php echo $apt->id; ?></p>
                                    <p><strong>Created:</strong> <?php echo date('M j, Y g:i A', strtotime($apt->created_at)); ?></p>
                                    <p><strong>Full Reason:</strong><br><?php echo esc_html($apt->reason ?: 'No reason provided'); ?></p>
                                    
                                    <?php if ($apt->notes): ?>
                                        <p><strong>Admin Notes:</strong><br><?php echo nl2br(esc_html($apt->notes)); ?></p>
                                    <?php endif; ?>
                                    
                                    <h4>Update Status</h4>
                                    <form method="post" style="margin-bottom: 15px;">
                                        <?php wp_nonce_field('medibook_appointment_action'); ?>
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="appointment_id" value="<?php echo $apt->id; ?>">
                                        <select name="status" required>
                                            <option value="pending" <?php selected($apt->status, 'pending'); ?>>Pending</option>
                                            <option value="confirmed" <?php selected($apt->status, 'confirmed'); ?>>Confirmed</option>
                                            <option value="completed" <?php selected($apt->status, 'completed'); ?>>Completed</option>
                                            <option value="cancelled" <?php selected($apt->status, 'cancelled'); ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" class="button button-primary">Update Status</button>
                                    </form>
                                </div>
                                
                                <div>
                                    <h3 style="margin-top: 0;">Add/Update Notes</h3>
                                    <form method="post">
                                        <?php wp_nonce_field('medibook_appointment_action'); ?>
                                        <input type="hidden" name="action" value="add_notes">
                                        <input type="hidden" name="appointment_id" value="<?php echo $apt->id; ?>">
                                        <textarea name="notes" rows="6" style="width: 100%; padding: 8px;" placeholder="Add internal notes about this appointment..."><?php echo esc_textarea($apt->notes); ?></textarea>
                                        <button type="submit" class="button button-primary" style="margin-top: 10px;">Save Notes</button>
                                    </form>
                                    
                                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
                                        <h4 style="color: #ef4444;">Danger Zone</h4>
                                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this appointment? This action cannot be undone.');">
                                            <?php wp_nonce_field('medibook_appointment_action'); ?>
                                            <input type="hidden" name="action" value="delete_appointment">
                                            <input type="hidden" name="appointment_id" value="<?php echo $apt->id; ?>">
                                            <button type="submit" class="button button-link-delete">Delete Appointment</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 20px; padding: 15px; background: #e0f2fe; border-left: 4px solid #0891b2;">
        <p style="margin: 0;"><strong>Total Appointments:</strong> <?php echo count($appointments); ?></p>
    </div>
</div>

<script>
function toggleDetails(id) {
    var row = document.getElementById('details-' + id);
    if (row.style.display === 'none') {
        row.style.display = 'table-row';
    } else {
        row.style.display = 'none';
    }
}
</script>

<style>
.wp-list-table th,
.wp-list-table td {
    vertical-align: top;
}
</style>
