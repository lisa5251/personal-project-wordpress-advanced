<?php
/**
 * Admin page for managing doctors
 * Access: wp-admin/admin.php?page=manage-doctors
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'medibook_doctor_action')) {
        wp_die('Security check failed');
    }
    
    if ($_POST['action'] === 'add_doctor') {
        $wpdb->insert(
            $wpdb->prefix . 'medibook_doctors',
            array(
                'name' => sanitize_text_field($_POST['name']),
                'specialty' => sanitize_text_field($_POST['specialty']),
                'email' => sanitize_email($_POST['email']),
                'phone' => sanitize_text_field($_POST['phone']),
                'bio' => sanitize_textarea_field($_POST['bio']),
                'status' => 'active'
            )
        );
        echo '<div class="notice notice-success"><p>Doctor added successfully!</p></div>';
    }
    
    if ($_POST['action'] === 'delete_doctor' && isset($_POST['doctor_id'])) {
        $wpdb->delete(
            $wpdb->prefix . 'medibook_doctors',
            array('id' => intval($_POST['doctor_id']))
        );
        // Also delete related schedules
        $wpdb->delete(
            $wpdb->prefix . 'medibook_schedules',
            array('doctor_id' => intval($_POST['doctor_id']))
        );
        echo '<div class="notice notice-success"><p>Doctor deleted successfully!</p></div>';
    }
}

// Get all doctors
$doctors = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}medibook_doctors ORDER BY name");
?>

<div class="wrap">
    <h1>Manage Doctors</h1>
    
    <div style="background: white; padding: 20px; margin: 20px 0; border: 1px solid #ccc; border-radius: 4px;">
        <h2>Add New Doctor</h2>
        <form method="post" action="">
            <?php wp_nonce_field('medibook_doctor_action'); ?>
            <input type="hidden" name="action" value="add_doctor">
            
            <table class="form-table">
                <tr>
                    <th><label for="name">Doctor Name *</label></th>
                    <td><input type="text" id="name" name="name" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="specialty">Specialty *</label></th>
                    <td><input type="text" id="specialty" name="specialty" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="email">Email *</label></th>
                    <td><input type="email" id="email" name="email" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="phone">Phone *</label></th>
                    <td><input type="tel" id="phone" name="phone" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="bio">Bio</label></th>
                    <td><textarea id="bio" name="bio" rows="4" class="large-text"></textarea></td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" class="button button-primary" value="Add Doctor">
            </p>
        </form>
    </div>
    
    <h2>Current Doctors</h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Specialty</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($doctors)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No doctors found. Add your first doctor above.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($doctors as $doctor): ?>
                    <tr>
                        <td><?php echo $doctor->id; ?></td>
                        <td><strong>Dr. <?php echo esc_html($doctor->name); ?></strong></td>
                        <td><?php echo esc_html($doctor->specialty); ?></td>
                        <td><?php echo esc_html($doctor->email); ?></td>
                        <td><?php echo esc_html($doctor->phone); ?></td>
                        <td>
                            <span class="dashicons dashicons-yes" style="color: green;"></span>
                            <?php echo ucfirst($doctor->status); ?>
                        </td>
                        <td>
                            <a href="admin.php?page=manage-schedules&doctor=<?php echo $doctor->id; ?>" class="button button-small">
                                Manage Schedule
                            </a>
                            <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this doctor?');">
                                <?php wp_nonce_field('medibook_doctor_action'); ?>
                                <input type="hidden" name="action" value="delete_doctor">
                                <input type="hidden" name="doctor_id" value="<?php echo $doctor->id; ?>">
                                <button type="submit" class="button button-small button-link-delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
