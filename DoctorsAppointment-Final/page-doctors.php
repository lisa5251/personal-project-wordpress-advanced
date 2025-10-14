<?php
/*
Template Name: Doctors List
*/
get_header();

$doctors = medibook_get_doctors();
$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
?>

<div class="container" style="padding: 3rem 20px;">
    <h1 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 2rem; text-align: center;">
        Our Doctors
    </h1>
    
    <?php if (empty($doctors)): ?>
        <div class="alert alert-info">
            <p>No doctors available at the moment. Please check back later.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-2">
            <?php foreach ($doctors as $doctor): ?>
                <?php $schedule = medibook_get_doctor_schedule($doctor->id); ?>
                <div class="card">
                    <div style="display: flex; gap: 1.5rem; margin-bottom: 1rem;">
                        <div style="width: 100px; height: 100px; background: var(--color-surface); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--color-primary); flex-shrink: 0;">
                            <?php echo strtoupper(substr($doctor->name, 0, 1)); ?>
                        </div>
                        <div style="flex: 1;">
                            <h2 style="font-size: 1.5rem; margin-bottom: 0.25rem;">
                                Dr. <?php echo esc_html($doctor->name); ?>
                            </h2>
                            <p style="color: var(--color-primary); font-weight: 600; margin-bottom: 0.5rem;">
                                <?php echo esc_html($doctor->specialty); ?>
                            </p>
                            <p style="color: var(--color-text-light); font-size: 0.875rem;">
                                <?php echo esc_html($doctor->email); ?> | <?php echo esc_html($doctor->phone); ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if ($doctor->bio): ?>
                        <p style="color: var(--color-text-light); margin-bottom: 1rem; line-height: 1.6;">
                            <?php echo esc_html($doctor->bio); ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($schedule)): ?>
                        <div style="background: var(--color-surface); padding: 1rem; border-radius: var(--radius); margin-bottom: 1rem;">
                            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.75rem;">
                                Available Schedule:
                            </h3>
                            <?php foreach ($schedule as $slot): ?>
                                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--color-border);">
                                    <span style="font-weight: 500;"><?php echo $days[$slot->day_of_week]; ?></span>
                                    <span style="color: var(--color-text-light);">
                                        <?php echo date('g:i A', strtotime($slot->start_time)); ?> - 
                                        <?php echo date('g:i A', strtotime($slot->end_time)); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <a href="<?php echo home_url('/book-appointment?doctor=' . $doctor->id); ?>" class="btn btn-primary" style="width: 100%;">
                        Book Appointment
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
