<?php

/**
 * Enhanced Smart List 5 with Taxonomy Filtering
 * Extends the original tdb_smart_list_5 with spice region filtering capabilities
 */
class tdb_smart_list_5_enhanced extends tdb_smart_list_5 {
    
    protected $filtered_items = array();
    protected $filter_taxonomy = 'spice_region';
    protected $filter_terms = array();
    
    function __construct($atts) {
        parent::__construct($atts);
        
        // Initialize filtering
        $this->init_filtering();
    }
    
    /**
     * Initialize filtering based on URL parameters and post taxonomy
     */
    protected function init_filtering() {
        // Get current post's spice regions
        $current_post_terms = wp_get_post_terms(get_the_ID(), $this->filter_taxonomy);
        $this->filter_terms = array_map(function($term) {
            return $term->slug;
        }, $current_post_terms);
        
        // Check for URL filter parameters
        if (isset($_GET['tdb_tax_' . $this->filter_taxonomy])) {
            $url_terms = array_map('sanitize_title', explode(',', $_GET['tdb_tax_' . $this->filter_taxonomy]));
            $this->filter_terms = array_intersect($this->filter_terms, $url_terms);
        }
    }
    
    /**
     * Override render_from_post_content to add filtering
     */
    function render_from_post_content($smart_list_settings) {
        // Get the original content
        $original_content = parent::render_from_post_content($smart_list_settings);
        
        // If no filter terms, return original content
        if (empty($this->filter_terms)) {
            return $original_content;
        }
        
        // Parse and filter the content
        $filtered_content = $this->filter_smart_list_content($original_content, $smart_list_settings);
        
        return $filtered_content;
    }
    
    /**
     * Filter smart list content based on spice region terms
     */
    protected function filter_smart_list_content($content, $smart_list_settings) {
        // Create a new tokenizer to parse the content
        $td_tokenizer = new td_tokenizer();
        $td_tokenizer->token_title_start = $smart_list_settings['td_smart_list_h'];
        $td_tokenizer->token_title_end = $smart_list_settings['td_smart_list_h'];
        
        // Get the list items
        $list_items = $td_tokenizer->split_to_list_items(array(
            'content' => $smart_list_settings['post_content'],
            'extract_first_image' => $smart_list_settings['extract_first_image']
        ));
        
        if (empty($list_items['list_items'])) {
            return $content;
        }
        
        // Filter items based on spice region terms
        $filtered_items = array();
        foreach ($list_items['list_items'] as $item) {
            if ($this->item_matches_filter($item)) {
                $filtered_items[] = $item;
            }
        }
        
        // If no items match, return a message
        if (empty($filtered_items)) {
            return $this->render_no_results_message();
        }
        
        // Update the list items
        $list_items['list_items'] = $filtered_items;
        
        // Re-render with filtered items
        return $this->render_filtered_smart_list($list_items, $smart_list_settings);
    }
    
    /**
     * Check if an item matches the current filter terms
     */
    protected function item_matches_filter($item) {
        // Extract text content from the item
        $item_content = $item['title'] . ' ' . $item['description'];
        
        // Check if any of the filter terms appear in the content
        foreach ($this->filter_terms as $term) {
            if (stripos($item_content, $term) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Render filtered smart list
     */
    protected function render_filtered_smart_list($list_items, $smart_list_settings) {
        $this->counting_order_asc = $smart_list_settings['counting_order_asc'];
        
        // Add numbers to list items
        $list_items = $this->add_numbers_to_list_items($list_items);
        
        if ($this->use_pagination === true) {
            $current_page = $this->get_current_page($list_items);
            return $this->render($list_items, $current_page);
        } else {
            return $this->render($list_items);
        }
    }
    
    /**
     * Render no results message
     */
    protected function render_no_results_message() {
        $terms_list = implode(', ', $this->filter_terms);
        return '<div class="tdb-smart-list-no-results" style="padding: 20px; text-align: center; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; margin: 20px 0;">
                    <h3>No items found</h3>
                    <p>No smart list items match the current spice region filter: <strong>' . esc_html($terms_list) . '</strong></p>
                    <p><a href="' . esc_url(remove_query_arg('tdb_tax_' . $this->filter_taxonomy)) . '" class="button">Clear Filter</a></p>
                </div>';
    }
    
    /**
     * Override render_before_list_wrap to add filter controls
     */
    protected function render_before_list_wrap() {
        $buffy = parent::render_before_list_wrap();
        
        // Add filter controls
        $buffy .= $this->render_filter_controls();
        
        return $buffy;
    }
    
    /**
     * Render filter controls
     */
    protected function render_filter_controls() {
        // Get all spice region terms
        $all_terms = get_terms(array(
            'taxonomy' => $this->filter_taxonomy,
            'hide_empty' => false,
        ));
        
        if (empty($all_terms) || is_wp_error($all_terms)) {
            return '';
        }
        
        $current_url = remove_query_arg('tdb_tax_' . $this->filter_taxonomy);
        $active_terms = $this->filter_terms;
        
        ob_start();
        ?>
        <div class="tdb-smart-list-filters" style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <h4 style="margin: 0 0 10px 0; color: #333;">Filter by Spice Region:</h4>
            <div class="tdb-filter-buttons" style="display: flex; flex-wrap: wrap; gap: 8px;">
                <a href="<?php echo esc_url($current_url); ?>" 
                   class="tdb-filter-btn <?php echo empty($active_terms) ? 'active' : ''; ?>"
                   style="padding: 6px 12px; background: <?php echo empty($active_terms) ? '#007cba' : '#e0e0e0'; ?>; color: <?php echo empty($active_terms) ? 'white' : '#333'; ?>; text-decoration: none; border-radius: 3px; font-size: 12px;">
                    All
                </a>
                <?php foreach ($all_terms as $term): ?>
                    <?php 
                    $is_active = in_array($term->slug, $active_terms);
                    $filter_url = $is_active ? 
                        remove_query_arg('tdb_tax_' . $this->filter_taxonomy) : 
                        add_query_arg('tdb_tax_' . $this->filter_taxonomy, $term->slug);
                    ?>
                    <a href="<?php echo esc_url($filter_url); ?>" 
                       class="tdb-filter-btn <?php echo $is_active ? 'active' : ''; ?>"
                       style="padding: 6px 12px; background: <?php echo $is_active ? '#007cba' : '#e0e0e0'; ?>; color: <?php echo $is_active ? 'white' : '#333'; ?>; text-decoration: none; border-radius: 3px; font-size: 12px;">
                        <?php echo esc_html($term->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <?php if (!empty($active_terms)): ?>
                <div class="tdb-active-filters" style="margin-top: 10px; font-size: 12px; color: #666;">
                    <strong>Active filters:</strong> <?php echo esc_html(implode(', ', $active_terms)); ?>
                    <a href="<?php echo esc_url($current_url); ?>" style="margin-left: 10px; color: #007cba;">Clear all</a>
                </div>
            <?php endif; ?>
        </div>
        
        <style>
            .tdb-filter-btn:hover {
                opacity: 0.8;
                transition: opacity 0.2s ease;
            }
            .tdb-filter-btn.active {
                font-weight: bold;
            }
        </style>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Override render_list_item to add spice region indicators
     */
    protected function render_list_item($item_array, $current_item_id, $current_item_number, $total_items_number) {
        $buffy = parent::render_list_item($item_array, $current_item_id, $current_item_number, $total_items_number);
        
        // Add spice region indicators if filtering is active
        if (!empty($this->filter_terms)) {
            $buffy = $this->add_spice_region_indicators($buffy, $item_array);
        }
        
        return $buffy;
    }
    
    /**
     * Add spice region indicators to list items
     */
    protected function add_spice_region_indicators($content, $item_array) {
        // Find matching terms in the item content
        $item_content = $item_array['title'] . ' ' . $item_array['description'];
        $matched_terms = array();
        
        foreach ($this->filter_terms as $term_slug) {
            if (stripos($item_content, $term_slug) !== false) {
                $term_obj = get_term_by('slug', $term_slug, $this->filter_taxonomy);
                if ($term_obj) {
                    $matched_terms[] = $term_obj->name;
                }
            }
        }
        
        if (!empty($matched_terms)) {
            $indicators = '<div class="tdb-spice-region-indicators" style="margin: 8px 0; font-size: 11px;">';
            $indicators .= '<span style="color: #666;">Spice Regions: </span>';
            $indicators .= '<span style="background: #a40d02; color: white; padding: 2px 6px; border-radius: 3px; margin-right: 4px; font-size: 10px;">';
            $indicators .= esc_html(implode('</span><span style="background: #a40d02; color: white; padding: 2px 6px; border-radius: 3px; margin-right: 4px; font-size: 10px;">', $matched_terms));
            $indicators .= '</span>';
            $indicators .= '</div>';
            
            // Insert indicators after the title
            $content = str_replace('</h2>', '</h2>' . $indicators, $content);
        }
        
        return $content;
    }
}

