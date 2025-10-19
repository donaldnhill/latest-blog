<?php
/*
 * Template Name: Spice Region Test
 * Description: Test page for spice region functionality
 */

get_header(); ?>

<div class="td-main-content-wrap td-container-wrap">
    <div class="td-container">
        <div class="td-crumb-container">
            <?php echo tagdiv_page_generator::get_breadcrumbs(array(
                'template' => 'page',
                'title' => get_the_title(),
            )); ?>
        </div>

        <div class="td-pb-row">
            <div class="td-pb-span12 td-main-content">
                <div class="td-ss-main-content">
                    
                    <h1 class="entry-title">🌶️ Spice Region Plugin Test</h1>
                    
                    <!-- Test Plugin Status -->
                    <?php echo do_shortcode('[spice_region_test]'); ?>
                    
                    <!-- Test Basic Filter -->
                    <div style="margin: 40px 0;">
                        <h2>Test 1: Basic Filter</h2>
                        <?php echo do_shortcode('[td_spice_region_filter filter_type="buttons" filter_style="modern"]'); ?>
                    </div>
                    
                    <!-- Test Posts -->
                    <div style="margin: 40px 0;">
                        <h2>Test 2: Filtered Posts</h2>
                        <?php echo do_shortcode('[spice_region_posts terms="lemongrass" posts_per_page="3" columns="1"]'); ?>
                    </div>
                    
                    <!-- Test Banner -->
                    <div style="margin: 40px 0;">
                        <h2>Test 3: Latest Post Banner</h2>
                        <?php echo do_shortcode('[latest_post_banner show_categories="1" show_date="1"]'); ?>
                    </div>
                    
                    <!-- Instructions -->
                    <div style="background: #e8f4fd; padding: 20px; border-left: 4px solid #2196F3; margin: 40px 0;">
                        <h3>📋 Next Steps:</h3>
                        <ol>
                            <li><strong>Check the test results above</strong> - Look for any error messages</li>
                            <li><strong>If TagDiv Composer shows "Not Available"</strong> - The elements won't appear in composer</li>
                            <li><strong>If no spice region terms are found</strong> - Go to Posts → Spice Regions and add some terms</li>
                            <li><strong>If everything looks good</strong> - Try using the shortcodes on your pages</li>
                        </ol>
                        
                        <h4>🔧 Troubleshooting:</h4>
                        <ul>
                            <li><strong>Elements not in TagDiv Composer?</strong> - Use shortcodes instead</li>
                            <li><strong>No posts showing?</strong> - Assign spice region terms to your posts</li>
                            <li><strong>Filter not working?</strong> - Check browser console for JavaScript errors</li>
                        </ul>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.entry-title {
    color: #a40d02;
    font-size: 32px;
    margin-bottom: 30px;
}

h2 {
    color: #333;
    font-size: 24px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #a40d02;
}
</style>

<?php get_footer(); ?>

