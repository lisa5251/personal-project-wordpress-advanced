<?php
/**
 * Admin page for managing doctor schedules
 * Access: wp-admin/admin.php?page=manage-schedules
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

$doctor_id = isset($_GET['doctor']) ? intval($_GET['doctor']) : 0;
$doctors = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}medibook_doctors ORDER BY name");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'medibook_schedule_action')) {
        wp_die('Security check failed');
    }
    
    if ($_POST['action'] === 'add_schedule') {
        $wpdb->insert(
            $wpdb->prefix . 'medibook_schedules',
            array(
                'doctor_id' => intval($_POST['doctor_id']),
                'day_of_week' => intval($_POST['day_of_week']),
                'start_time' => sanitize_text_field($_POST['start_time']),
                'end_time' => sanitize_text_field($_POST['end_time']),
                'slot_duration' => intval($_POST['slot_duration'])
            )
        );
        echo '<div class="notice notice-success"><p>Schedule added successfully!</p></div>';
        $doctor_id = intval($_POST['doctor_id']);
    }
    
    if ($_POST['action'] === 'delete_schedule' && isset($_POST['schedule_id'])) {
        $wpdb->delete(
            $wpdb->prefix . 'medibook_schedules',
            array('id' => intval($_POST['schedule_id']))
        );
        echo '<div class="notice notice-success"><p>Schedule deleted successfully!</p></div>';
    }
}

// Get schedules for selected doctor
$schedules = array();
if ($doctor_id) {
    $schedules = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}medibook_schedules WHERE doctor_id = %d ORDER BY day_of_week, start_time",
        $doctor_id
    ));
}

$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
?>

<div class="wrap">
    <h1>Manage Doctor Schedules</h1>
    
    <div style="background: white; padding: 20px; margin: 20px 0; border: 1px solid #ccc; border-radius: 4px;">
        <h2>Add Schedule</h2>
        <form method="post" action="">
            <?php wp_nonce_field('medibook_schedule_action'); ?>
            <input type="hidden" name="action" value="add_schedule">
            
            <table class="form-table">
                <tr>
                    <th><label for="doctor_id">Select Doctor *</label></th>
                    <td>
                        <select id="doctor_id" name="doctor_id" required onchange="window.location.href='admin.php?page=manage-schedules&doctor=' + this.value">
                            <option value="">Choose a doctor...</option>
                            <?php foreach ($doctors as $doc): ?>
                                <option value="<?php echo $doc->id; ?>" <?php selected($doctor_id, $doc->id); ?>>
                                    Dr. <?php echo esc_html($doc->name); ?> - <?php echo esc_html($doc->specialty); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <?php if ($doctor_id): ?>
                <tr>
                    <th><label for="day_of_week">Day of Week *</label></th>
                    <td>
                        <select id="day_of_week" name="day_of_week" required>
                            <?php foreach ($days as $index => $day): ?>
                                <option value="<?php echo $index; ?>"><?php echo $day; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="start_time">Start Time *</label></th>
                    <td><input type="time" id="start_time" name="start_time" required></td>
                </tr>
                <tr>
                    <th><label for="end_time">End Time *</label></th>
                    <td><input type="time" id="end_time" name="end_time" required></td>
                </tr>
                <tr>
                    <th><label for="slot_duration">Slot Duration (minutes) *</label></th>
                    <td>
                        <select id="slot_duration" name="slot_duration" required>
                            <option value="15">15 minutes</option>
                            <option value="30" selected>30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60">60 minutes</option>
                        </select>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
            
            <?php if ($doctor_id): ?>
            <p class="submit">
                <input type="submit" class="button button-primary" value="Add Schedule">
            </p>
            <?php endif; ?>
        </form>
    </div>
    
    <?php if ($doctor_id): ?>
        <h2>Current Schedules for Selected Doctor</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Slot Duration</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No schedules found. Add a schedule above.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><strong><?php echo $days[$schedule->day_of_week]; ?></strong></td>
                            <td><?php echo date('g:i A', strtotime($schedule->start_time)); ?></td>
                            <td><?php echo date('g:i A', strtotime($schedule->end_time)); ?></td>
                            <td><?php echo $schedule->slot_duration; ?> minutes</td>
                            <td>
                                <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                                    <?php wp_nonce_field('medibook_schedule_action'); ?>
                                    <input type="hidden" name="action" value="delete_schedule">
                                    <input type="hidden" name="schedule_id" value="<?php echo $schedule->id; ?>">
                                    <button type="submit" class="button button-small button-link-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
