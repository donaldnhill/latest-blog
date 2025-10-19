<?php
/**
 * Register Enhanced Smart List 5 with Taxonomy Filtering
 * This file registers the enhanced smart list with TagDiv Composer
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include the enhanced smart list class
require_once __DIR__ . '/tdb_smart_list_5_enhanced.php';

/**
 * Register the enhanced smart list with TagDiv
 */
add_action('td_api_smart_list', function() {
    td_api_smart_list::add('tdb_smart_list_5_enhanced', array(
        'file' => __DIR__ . '/tdb_smart_list_5_enhanced.php',
        'text' => 'Smart List 5 Enhanced (with Spice Region Filtering)',
        'img' => '', // You can add an image path here
        'extract_first_image' => true,
        'show_item_numbers' => true,
        'show_item_pagination' => true,
        'use_pagination' => true,
        'description' => 'Enhanced version of Smart List 5 with spice region taxonomy filtering capabilities. Includes filter controls and spice region indicators.',
        'features' => array(
            'Taxonomy Filtering',
            'Filter Controls',
            'Spice Region Indicators',
            'No Results Message',
            'URL Parameter Support'
        )
    ));
});

/**
 * Add custom CSS for the enhanced smart list
 */
add_action('wp_head', function() {
    if (is_single()) {
        ?>
        <style>
            .tdb-smart-list-filters {
                border: 1px solid #ddd;
                border-radius: 5px;
                background: #f8f9fa;
                padding: 15px;
                margin: 20px 0;
            }
            
            .tdb-filter-buttons {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 10px;
            }
            
            .tdb-filter-btn {
                padding: 6px 12px;
                background: #e0e0e0;
                color: #333;
                text-decoration: none;
                border-radius: 3px;
                font-size: 12px;
                transition: all 0.2s ease;
                border: none;
                cursor: pointer;
            }
            
            .tdb-filter-btn:hover {
                opacity: 0.8;
                background: #d0d0d0;
            }
            
            .tdb-filter-btn.active {
                background: #007cba;
                color: white;
                font-weight: bold;
            }
            
            .tdb-active-filters {
                margin-top: 10px;
                font-size: 12px;
                color: #666;
                padding-top: 10px;
                border-top: 1px solid #ddd;
            }
            
            .tdb-smart-list-no-results {
                padding: 20px;
                text-align: center;
                background: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 4px;
                margin: 20px 0;
            }
            
            .tdb-spice-region-indicators {
                margin: 8px 0;
                font-size: 11px;
            }
            
            .tdb-spice-region-indicators span {
                background: #a40d02;
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                margin-right: 4px;
                font-size: 10px;
                display: inline-block;
            }
            
            @media (max-width: 768px) {
                .tdb-filter-buttons {
                    flex-direction: column;
                }
                
                .tdb-filter-btn {
                    text-align: center;
                    margin-bottom: 5px;
                }
            }
        </style>
        <?php
    }
});

/**
 * Add JavaScript for enhanced functionality
 */
add_action('wp_footer', function() {
    if (is_single()) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Add smooth scrolling for filter buttons
            $('.tdb-filter-btn').on('click', function(e) {
                // Add loading state
                $(this).addClass('loading');
                
                // Remove loading state after a short delay
                setTimeout(function() {
                    $('.tdb-filter-btn').removeClass('loading');
                }, 500);
            });
            
            // Add keyboard navigation for filters
            $('.tdb-filter-btn').on('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    $(this).click();
                }
            });
        });
        </script>
        <?php
    }
});

