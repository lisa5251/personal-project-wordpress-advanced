<?php
/*
Template Name: Doctors Page
*/
get_header(); ?>

<main class="doctors-page" style="padding:2rem;">
    <h1>Our Doctors</h1>
    <p>Meet our dedicated medical team.</p>

    <div class="doctors-grid" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:20px; margin-top:2rem;">
        <?php
        // Example static list of doctors
        $doctors = [
            [
                'name' => 'Dr. Sarah Thompson',
                'specialty' => 'Cardiologist',
                'image' => 'https://via.placeholder.com/300x200?text=Dr+Sarah+Thompson'
            ],
            [
                'name' => 'Dr. Michael Lee',
                'specialty' => 'Pediatrician',
                'image' => 'https://via.placeholder.com/300x200?text=Dr+Michael+Lee'
            ],
            [
                'name' => 'Dr. Emma Rodriguez',
                'specialty' => 'Dermatologist',
                'image' => 'https://via.placeholder.com/300x200?text=Dr+Emma+Rodriguez'
            ],
        ];

        // Output doctors
        foreach ($doctors as $doctor) {
            echo '<div class="doctor-card" style="border:1px solid #ddd; border-radius:10px; padding:1rem; text-align:center; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,0.1);">';
            echo '<img src="' . esc_url($doctor['image']) . '" alt="' . esc_attr($doctor['name']) . '" style="width:100%; border-radius:10px;">';
            echo '<h3 style="margin-top:1rem;">' . esc_html($doctor['name']) . '</h3>';
            echo '<p style="color:#555;">' . esc_html($doctor['specialty']) . '</p>';
            echo '</div>';
        }
        ?>
    </div>
</main>

<?php get_footer(); ?>
