<?php
/**
 * Enhanced Smart List Integration
 * Include this file in your theme's functions.php to activate the enhanced smart list
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include the enhanced smart list registration
require_once WP_CONTENT_DIR . '/plugins/td-cloud-library/smart_lists/register_enhanced_smart_list.php';

/**
 * Add custom shortcode for enhanced smart list
 */
add_shortcode('enhanced_smart_list', function($atts) {
    $atts = shortcode_atts(array(
        'post_id' => get_the_ID(),
        'filter_taxonomy' => 'spice_region',
        'show_filters' => true,
        'show_indicators' => true,
    ), $atts, 'enhanced_smart_list');
    
    if (empty($atts['post_id'])) {
        return '<p>No post ID provided.</p>';
    }
    
    // Get the post content
    $post = get_post($atts['post_id']);
    if (!$post) {
        return '<p>Post not found.</p>';
    }
    
    // Set up smart list settings
    $smart_list_settings = array(
        'post_content' => $post->post_content,
        'counting_order_asc' => false,
        'td_smart_list_h' => 'h3',
        'extract_first_image' => true
    );
    
    // Create enhanced smart list instance
    $enhanced_smart_list = new tdb_smart_list_5_enhanced($atts);
    
    // Render the smart list
    return $enhanced_smart_list->render_from_post_content($smart_list_settings);
});

/**
 * Add admin notice for enhanced smart list
 */
add_action('admin_notices', function() {
    if (current_user_can('manage_options')) {
        ?>
        <div class="notice notice-info is-dismissible">
            <p><strong>Enhanced Smart List 5</strong> is now available with spice region filtering capabilities!</p>
            <p>You can use the shortcode <code>[enhanced_smart_list]</code> or select "Smart List 5 Enhanced" from the smart list options.</p>
        </div>
        <?php
    }
});

/**
 * Add filter controls to single posts automatically
 */
add_action('wp_head', function() {
    if (is_single() && has_shortcode(get_post()->post_content, 'tdb_smart_list_5')) {
        ?>
        <style>
            /* Enhanced smart list styles */
            .tdb-smart-list-filters {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 20px;
                margin: 20px 0;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            .tdb-filter-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 15px;
            }
            
            .tdb-filter-btn {
                padding: 8px 16px;
                background: #ffffff;
                color: #495057;
                text-decoration: none;
                border-radius: 20px;
                font-size: 13px;
                font-weight: 500;
                transition: all 0.3s ease;
                border: 2px solid #e9ecef;
                display: inline-block;
            }
            
            .tdb-filter-btn:hover {
                background: #007cba;
                color: white;
                border-color: #007cba;
                transform: translateY(-1px);
                box-shadow: 0 2px 8px rgba(0,124,186,0.3);
            }
            
            .tdb-filter-btn.active {
                background: #a40d02;
                color: white;
                border-color: #a40d02;
                font-weight: bold;
            }
            
            .tdb-active-filters {
                margin-top: 15px;
                font-size: 13px;
                color: #6c757d;
                padding-top: 15px;
                border-top: 1px solid #dee2e6;
            }
            
            .tdb-smart-list-no-results {
                padding: 30px;
                text-align: center;
                background: #f8f9fa;
                border: 2px dashed #dee2e6;
                border-radius: 8px;
                margin: 20px 0;
            }
            
            .tdb-smart-list-no-results h3 {
                color: #495057;
                margin-bottom: 10px;
            }
            
            .tdb-smart-list-no-results .button {
                background: #007cba;
                color: white;
                padding: 10px 20px;
                text-decoration: none;
                border-radius: 5px;
                display: inline-block;
                margin-top: 10px;
            }
            
            .tdb-spice-region-indicators {
                margin: 10px 0;
                font-size: 12px;
            }
            
            .tdb-spice-region-indicators span {
                background: linear-gradient(135deg, #a40d02 0%, #8a0a02 100%);
                color: white;
                padding: 4px 8px;
                border-radius: 12px;
                margin-right: 6px;
                font-size: 11px;
                font-weight: 500;
                display: inline-block;
                box-shadow: 0 1px 3px rgba(164,13,2,0.3);
            }
            
            @media (max-width: 768px) {
                .tdb-filter-buttons {
                    flex-direction: column;
                }
                
                .tdb-filter-btn {
                    text-align: center;
                    margin-bottom: 8px;
                }
            }
        </style>
        <?php
    }
});

