<?php get_header(); ?>

<main>
<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <div class="entry-content">
                <?php the_excerpt(); ?>
            </div>
        </article>
    <?php endwhile; ?>
    <nav class="pagination">
        <?php the_posts_pagination(); ?>
    </nav>
<?php else : ?>
    <p>No posts found.</p>
<?php endif; ?>
</main>

<?php get_footer(); ?>