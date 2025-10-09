# Doctor Appointment Booking Plugin for WordPress

A complete appointment booking system for medical practices built with PHP, JavaScript, CSS, and HTML.

## Features

- **Patient Booking Form**: Easy-to-use form for patients to book appointments
- **Admin Dashboard**: Comprehensive dashboard to manage all appointments
- **Status Management**: Track appointments with statuses (Pending, Confirmed, Completed, Cancelled)
- **Email Notifications**: Automatic confirmation emails sent to patients
- **Time Slot Validation**: Prevents double-booking of time slots
- **Responsive Design**: Works perfectly on all devices
- **Statistics Dashboard**: View appointment statistics at a glance
- **Filter & Search**: Filter appointments by status and date

## Installation

1. Download the plugin ZIP file
2. Go to WordPress Admin → Plugins → Add New
3. Click "Upload Plugin" and select the ZIP file
4. Click "Install Now" and then "Activate"
5. The plugin will automatically create the necessary database table

## Usage

### Adding the Booking Form

Add the booking form to any page or post using this shortcode:

\`\`\`
[doctor_appointment_form]
\`\`\`

### Managing Appointments

1. Go to WordPress Admin → Appointments
2. View all appointments in the dashboard
3. Change appointment status using the dropdown
4. Delete appointments if needed
5. Filter appointments by status or date

## Customization

### Adding More Doctors

Edit `includes/shortcodes.php` and add more options to the doctor select field.

### Changing Time Slots

Edit `includes/shortcodes.php` and modify the time options in the appointment_time select field.

### Styling

- Frontend styles: `assets/css/style.css`
- Admin styles: `assets/css/admin-style.css`

## Database Structure

The plugin creates a table `wp_doctor_appointments` with the following fields:

- id
- patient_name
- patient_email
- patient_phone
- appointment_date
- appointment_time
- doctor_name
- reason
- status
- created_at

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Support

For issues or questions, please contact your WordPress administrator.

## License

GPL v2 or later
