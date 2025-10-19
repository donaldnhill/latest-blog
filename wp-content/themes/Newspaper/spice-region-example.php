<?php
/*
 * Template Name: Spice Region Filter Example
 * Description: Example page showing how to use spice region filtering
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
                    
                    <!-- Page Title -->
                    <h1 class="entry-title">Spice Region Filtering Examples</h1>
                    
                    <!-- Example 1: Modern Button Filter -->
                    <div style="margin: 40px 0;">
                        <h2>Example 1: Modern Button Filter</h2>
                        <?php echo do_shortcode('[td_spice_region_filter filter_type="buttons" filter_style="modern" all_text="All Spice Regions"]'); ?>
                    </div>
                    
                    <!-- Example 2: Dropdown Filter -->
                    <div style="margin: 40px 0;">
                        <h2>Example 2: Dropdown Filter</h2>
                        <?php echo do_shortcode('[td_spice_region_filter filter_type="dropdown" show_all_option="true" all_text="Show All"]'); ?>
                    </div>
                    
                    <!-- Example 3: Checkbox Filter -->
                    <div style="margin: 40px 0;">
                        <h2>Example 3: Checkbox Filter (Multiple Selection)</h2>
                        <?php echo do_shortcode('[td_spice_region_filter filter_type="checkboxes" all_text="All Regions"]'); ?>
                    </div>
                    
                    <!-- Example 4: Filtered Posts Grid -->
                    <div style="margin: 40px 0;">
                        <h2>Example 4: Filtered Posts Grid</h2>
                        <?php echo do_shortcode('[spice_region_posts terms="lemongrass,cumin" posts_per_page="6" columns="3" show_taxonomy_title="true"]'); ?>
                    </div>
                    
                    <!-- Example 5: Enhanced Smart List -->
                    <div style="margin: 40px 0;">
                        <h2>Example 5: Enhanced Smart List with Filtering</h2>
                        <?php echo do_shortcode('[enhanced_smart_list show_filters="true" show_indicators="true"]'); ?>
                    </div>
                    
                    <!-- Example 6: Latest Post Banner -->
                    <div style="margin: 40px 0;">
                        <h2>Example 6: Latest Post Banner</h2>
                        <?php echo do_shortcode('[latest_post_banner show_categories="1" show_date="1" show_excerpt="1"]'); ?>
                    </div>
                    
                    <!-- Example 7: Advanced Filtering -->
                    <div style="margin: 40px 0;">
                        <h2>Example 7: Advanced Filtering (Category + Tag + Spice Region)</h2>
                        <?php echo do_shortcode('[spice_region_posts terms="lemongrass" posts_per_page="4" columns="2" filter_by_category="1,2" filter_by_tag="spicy,hot" show_taxonomy_title="true"]'); ?>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styling for the example page */
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

/* Make sure filters are responsive */
.spice-region-filter {
    margin: 20px 0;
}

/* Style the filtered content */
.spice-region-posts-wrap {
    margin: 20px 0;
}
</style>

<script>
// Listen for filter changes and update content accordingly
window.addEventListener('spiceRegionFilter', function(event) {
    console.log('Filter changed to:', event.detail.termSlug);
    
    // You can add custom logic here to update other elements
    // For example, hide/show content based on the filter
    const filteredContent = document.querySelectorAll('.spice-region-posts-wrap');
    
    filteredContent.forEach(function(content) {
        if (event.detail.termSlug) {
            // Add visual feedback that content is filtered
            content.style.opacity = '0.7';
            content.style.transition = 'opacity 0.3s ease';
            
            setTimeout(function() {
                content.style.opacity = '1';
            }, 300);
        }
    });
});
</script>

<?php get_footer(); ?>

