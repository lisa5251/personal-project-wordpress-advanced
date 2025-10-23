<?php
/*
Template Name: Book Appointment Page
*/
get_header(); ?>

<main class="book-appointment-page" style="padding:2rem;">
    <h1>Book an Appointment</h1>
    <p>Use the form below to schedule your appointment.</p>

    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
    else :
        echo '<p>Appointment booking form will appear here.</p>';
    endif;
    ?>
</main>

<?php get_footer(); ?>
