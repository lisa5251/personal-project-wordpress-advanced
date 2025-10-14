<?php get_header(); ?>

<div class="container" style="padding: 4rem 20px;">
    <div style="text-align: center; max-width: 800px; margin: 0 auto 4rem;">
        <h1 style="font-size: 3rem; font-weight: 700; color: var(--color-text); margin-bottom: 1rem;">
            Book Your Doctor Appointment
        </h1>
        <p style="font-size: 1.25rem; color: var(--color-text-light); margin-bottom: 2rem;">
            Schedule appointments with our experienced medical professionals. Easy, fast, and convenient.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="<?php echo home_url('/book-appointment'); ?>" class="btn btn-primary" style="font-size: 1.125rem; padding: 1rem 2rem;">
                Book Appointment Now
            </a>
            <a href="<?php echo home_url('/doctors'); ?>" class="btn btn-outline" style="font-size: 1.125rem; padding: 1rem 2rem;">
                View Our Doctors
            </a>
        </div>
    </div>

    <div style="background: var(--color-surface); padding: 3rem; border-radius: var(--radius); margin-bottom: 4rem;">
        <h2 style="text-align: center; font-size: 2rem; margin-bottom: 3rem; color: var(--color-text);">
            How It Works
        </h2>
        <div class="grid grid-3">
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: var(--color-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem; font-weight: 700;">
                    1
                </div>
                <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Choose a Doctor</h3>
                <p style="color: var(--color-text-light);">Browse our list of qualified doctors and select the specialist you need.</p>
            </div>
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: var(--color-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem; font-weight: 700;">
                    2
                </div>
                <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Select Date & Time</h3>
                <p style="color: var(--color-text-light);">Pick a convenient date and time slot from the doctor's available schedule.</p>
            </div>
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; background: var(--color-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: white; font-size: 2rem; font-weight: 700;">
                    3
                </div>
                <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Confirm Booking</h3>
                <p style="color: var(--color-text-light);">Fill in your details and confirm your appointment. You'll receive a confirmation.</p>
            </div>
        </div>
    </div>

    <div style="text-align: center; padding: 3rem 0;">
        <h2 style="font-size: 2rem; margin-bottom: 1rem;">Why Choose MediBook?</h2>
        <div class="grid grid-2" style="margin-top: 2rem; text-align: left;">
            <div class="card">
                <h3 style="color: var(--color-primary); margin-bottom: 0.5rem;">Easy Scheduling</h3>
                <p style="color: var(--color-text-light);">Book appointments 24/7 from anywhere. No phone calls needed.</p>
            </div>
            <div class="card">
                <h3 style="color: var(--color-primary); margin-bottom: 0.5rem;">Qualified Doctors</h3>
                <p style="color: var(--color-text-light);">All our doctors are certified professionals with years of experience.</p>
            </div>
            <div class="card">
                <h3 style="color: var(--color-primary); margin-bottom: 0.5rem;">Flexible Times</h3>
                <p style="color: var(--color-text-light);">Multiple time slots available throughout the day to fit your schedule.</p>
            </div>
            <div class="card">
                <h3 style="color: var(--color-primary); margin-bottom: 0.5rem;">Instant Confirmation</h3>
                <p style="color: var(--color-text-light);">Get immediate confirmation of your appointment via email.</p>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
