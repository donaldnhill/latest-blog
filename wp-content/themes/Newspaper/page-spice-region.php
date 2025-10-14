<?php
/**
 * Template Name: Spice Region Page
 * 
 * Custom page template that automatically displays spice region content
 */

get_header(); ?>

<div class="spice-region-page">
    <div class="container">
        
        <!-- Latest Post Banner -->
        <div class="latest-post-section">
            <?php echo do_shortcode('[latest_post_banner]'); ?>
        </div>
        
        <!-- Current Spice Region Posts -->
        <div class="current-posts-section">
            <h2>Current Spice Region Posts</h2>
            <?php echo do_shortcode('[spice_region_current_posts]'); ?>
        </div>
        
        <!-- All Spice Region Posts -->
        <div class="all-posts-section">
            <h2>All Spice Region Posts</h2>
            <?php echo do_shortcode('[spice_region_posts terms="lemongrass,cumin,other"]'); ?>
        </div>
        
    </div>
</div>

<style>
.spice-region-page {
    padding: 20px 0;
}

.spice-region-page .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.spice-region-page h2 {
    font-size: 2rem;
    margin: 2rem 0 1rem 0;
    color: #333;
}

.latest-post-section {
    margin-bottom: 3rem;
}

.current-posts-section {
    margin-bottom: 3rem;
}

.all-posts-section {
    margin-bottom: 3rem;
}
</style>

<?php get_footer(); ?>

