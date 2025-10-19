<?php
/**
 * Plugin Name: Spice Region Shortcodes
 * Plugin URI: https://yourwebsite.com
 * Description: Custom shortcodes for spice region functionality including latest post banner and filtered posts display.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: spice-region-shortcodes
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SPICE_REGION_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SPICE_REGION_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SPICE_REGION_VERSION', '1.0.0');

/**
 * Main Spice Region Shortcodes Class
 */
class SpiceRegionShortcodes {
    
    public function __construct() {
        // Debug: Log plugin initialization
        error_log('Spice Region Plugin: Plugin constructor called');
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function init() {
        // Debug: Log plugin init
        error_log('Spice Region Plugin: Init function called');
        
        // Register spice region taxonomy
        $this->register_spice_region_taxonomy();
        
        // Register all shortcodes
        $this->register_shortcodes();
        
        // Debug: Log shortcode registration
        error_log('Spice Region Plugin: Shortcodes registered');
        
        // Register TagDiv Composer elements - use multiple hooks to ensure it works
        add_action('tdc_init', array($this, 'register_tagdiv_elements'));
        add_action('wp_loaded', array($this, 'register_tagdiv_elements'));
        add_action('admin_init', array($this, 'register_tagdiv_elements'));
    }
    
    /**
     * Register spice region taxonomy
     */
    public function register_spice_region_taxonomy() {
        // We'll use existing categories instead of creating a new taxonomy
        // Categories: south-asia, southeast-asia, east-asia
        // This function is kept for compatibility but does nothing
    }
    
    /**
     * Register all shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('spice_region_single', array($this, 'spice_region_single_shortcode'));
        add_shortcode('spice_region_posts', array($this, 'spice_region_posts_shortcode'));
        add_shortcode('spice_region_current_posts', array($this, 'spice_region_current_posts_shortcode'));
        add_shortcode('latest_post_banner', array($this, 'latest_post_banner_shortcode'));
        add_shortcode('spice_region_debug', array($this, 'spice_region_debug_shortcode'));
        add_shortcode('td_spice_region_filter', array($this, 'spice_region_filter_shortcode'));
        add_shortcode('spice_region_test', array($this, 'spice_region_test_shortcode'));
        add_shortcode('tdb_single_bg_featured_image', array($this, 'tdb_single_bg_featured_image_shortcode'));
        add_shortcode('spice_region_debug_simple', array($this, 'spice_region_debug_simple_shortcode'));
        add_shortcode('spice_region_related_posts', array($this, 'spice_region_related_posts_shortcode'));
        add_shortcode('spice_region_test_simple', array($this, 'spice_region_test_simple_shortcode'));
        
        // Add spice_region taxonomy to TagDiv Composer filters
        add_action('init', array($this, 'add_spice_region_filter'));
    }
    
    /**
     * Register TagDiv Composer elements
     */
    public function register_tagdiv_elements() {
        if (!function_exists('tdc_add_custom_element')) {
            // Debug: Log that TagDiv Composer is not available
            error_log('Spice Region Plugin: TagDiv Composer not available');
            return;
        }
        
        // Debug: Log that we're registering elements
        error_log('Spice Region Plugin: Registering TagDiv Composer elements');
        
        // Spice Region Posts Element
        tdc_add_custom_element(array(
            'name' => 'Spice Region Posts',
            'shortcode' => 'td_spice_region_posts',
            'icon' => 'dashicons-location',
            'params' => array(
                array(
                    'name' => 'terms',
                    'type' => 'textfield',
                    'label' => 'Spice Region Terms',
                    'description' => 'Comma-separated term slugs (e.g., lemongrass,cumin)',
                    'default' => ''
                ),
                array(
                    'name' => 'posts_per_page',
                    'type' => 'textfield',
                    'label' => 'Posts Per Page',
                    'default' => '3'
                ),
                array(
                    'name' => 'columns',
                    'type' => 'dropdown',
                    'label' => 'Columns',
                    'options' => array(
                        '1 Column' => '1',
                        '2 Columns' => '2',
                        '3 Columns' => '3',
                        '4 Columns' => '4'
                    ),
                    'default' => '1'
                ),
                array(
                    'name' => 'ratio',
                    'type' => 'dropdown',
                    'label' => 'Image Aspect Ratio',
                    'options' => array(
                        '16:9 (1600x872)' => '1600x872',
                        '4:3' => '4x3',
                        '1:1 (Square)' => '1x1',
                        '21:9 (Wide)' => '21x9'
                    ),
                    'default' => '1600x872'
                ),
                array(
                    'name' => 'no_watermark',
                    'type' => 'checkbox',
                    'label' => 'No Watermark',
                    'default' => false
                ),
                array(
                    'name' => 'show_taxonomy_title',
                    'type' => 'checkbox',
                    'label' => 'Show Taxonomy Title',
                    'default' => true
                ),
                array(
                    'name' => 'filter_by_category',
                    'type' => 'textfield',
                    'label' => 'Filter by Category IDs',
                    'description' => 'Comma-separated category IDs (e.g., 1,2,3)',
                    'default' => ''
                ),
                array(
                    'name' => 'filter_by_tag',
                    'type' => 'textfield',
                    'label' => 'Filter by Tag Slugs',
                    'description' => 'Comma-separated tag slugs (e.g., spicy,hot)',
                    'default' => ''
                ),
                array(
                    'name' => 'exclude_posts',
                    'type' => 'textfield',
                    'label' => 'Exclude Post IDs',
                    'description' => 'Comma-separated post IDs to exclude (e.g., 123,456)',
                    'default' => ''
                )
            )
        ));
        
        // Spice Region Filter Element
        tdc_add_custom_element(array(
            'name' => 'Spice Region Filter',
            'shortcode' => 'td_spice_region_filter',
            'icon' => 'dashicons-filter',
            'params' => array(
                array(
                    'name' => 'filter_type',
                    'type' => 'dropdown',
                    'label' => 'Filter Type',
                    'options' => array(
                        'Dropdown Filter' => 'dropdown',
                        'Button Filter' => 'buttons',
                        'Checkbox Filter' => 'checkboxes'
                    ),
                    'default' => 'dropdown'
                ),
                array(
                    'name' => 'show_all_option',
                    'type' => 'checkbox',
                    'label' => 'Show "All" Option',
                    'default' => true
                ),
                array(
                    'name' => 'all_text',
                    'type' => 'textfield',
                    'label' => 'All Option Text',
                    'default' => 'All Spice Regions'
                ),
                array(
                    'name' => 'filter_style',
                    'type' => 'dropdown',
                    'label' => 'Filter Style',
                    'options' => array(
                        'Default' => 'default',
                        'Modern' => 'modern',
                        'Minimal' => 'minimal'
                    ),
                    'default' => 'modern'
                )
            )
        ));
        
        // Latest Post Banner Element
        tdc_add_custom_element(array(
            'name' => 'Latest Post Banner',
            'shortcode' => 'td_latest_post_banner',
            'icon' => 'dashicons-format-image',
            'params' => array(
                array(
                    'name' => 'ratio',
                    'type' => 'dropdown',
                    'label' => 'Banner Aspect Ratio',
                    'options' => array(
                        '16:9 (1600x872)' => '1600x872',
                        '4:3' => '4x3',
                        '1:1 (Square)' => '1x1',
                        '21:9 (Wide)' => '21x9'
                    ),
                    'default' => '1600x872'
                ),
                array(
                    'name' => 'text_color',
                    'type' => 'colorpicker',
                    'label' => 'Text Color',
                    'default' => '#ffffff'
                ),
                array(
                    'name' => 'overlay_color',
                    'type' => 'colorpicker',
                    'label' => 'Overlay Color',
                    'default' => 'rgba(0,0,0,0.3)'
                ),
                array(
                    'name' => 'show_categories',
                    'type' => 'checkbox',
                    'label' => 'Show Categories',
                    'default' => true
                ),
                array(
                    'name' => 'show_date',
                    'type' => 'checkbox',
                    'label' => 'Show Date',
                    'default' => true
                ),
                array(
                    'name' => 'show_excerpt',
                    'type' => 'checkbox',
                    'label' => 'Show Excerpt',
                    'default' => true
                )
            )
        ));
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'spice-region-styles',
            SPICE_REGION_PLUGIN_URL . 'assets/css/spice-region.css',
            array(),
            SPICE_REGION_VERSION
        );
    }
    
    /**
     * Add spice_region taxonomy to TagDiv Composer filters
     */
    public function add_spice_region_filter() {
        // Add spice_region to the taxonomy filter options
        add_filter('tdc_ajax_filter_taxonomies', array($this, 'add_spice_region_to_filters'));
        add_filter('tdc_ajax_filter_taxonomy_terms', array($this, 'get_spice_region_terms'));
    }
    
    /**
     * Add spice_region taxonomy to available taxonomies for filtering
     */
    public function add_spice_region_to_filters($taxonomies) {
        $taxonomies['spice_region'] = 'Spice Regions';
        return $taxonomies;
    }
    
    /**
     * Get spice_region terms for filter dropdown
     */
    public function get_spice_region_terms($terms) {
        $spice_terms = get_terms(array(
            'taxonomy' => 'spice_region',
            'hide_empty' => false,
        ));
        
        if (!is_wp_error($spice_terms)) {
            foreach ($spice_terms as $term) {
                $terms[$term->term_id] = $term->name;
            }
        }
        
        return $terms;
    }
    
    /**
     * Spice Region Filter Shortcode
     */
    public function spice_region_filter_shortcode($atts) {
        $atts = shortcode_atts(array(
            'filter_type' => 'dropdown',
            'show_all_option' => true,
            'all_text' => 'All Spice Regions',
            'filter_style' => 'modern',
        ), $atts, 'td_spice_region_filter');
        
        // Get all spice region terms
        $terms = get_terms(array(
            'taxonomy' => 'spice_region',
            'hide_empty' => false,
        ));
        
        if (is_wp_error($terms) || empty($terms)) {
            return '<p>No spice regions found.</p>';
        }
        
        ob_start();
        ?>
        <div class="spice-region-filter spice-region-filter-<?php echo esc_attr($atts['filter_style']); ?>" data-filter-type="<?php echo esc_attr($atts['filter_type']); ?>">
            <style>
                .spice-region-filter {
                    margin: 20px 0;
                    padding: 20px;
                    background: #f8f9fa;
                    border-radius: 8px;
                }
                .spice-region-filter-modern {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                }
                .spice-region-filter-minimal {
                    background: white;
                    border: 1px solid #e1e5e9;
                }
                .spice-region-filter h3 {
                    margin: 0 0 15px 0;
                    font-size: 18px;
                    font-weight: 600;
                }
                .spice-region-filter-modern h3 {
                    color: white;
                }
                .spice-region-filter-dropdown select {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    font-size: 16px;
                }
                .spice-region-filter-buttons {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                }
                .spice-region-filter-buttons button {
                    padding: 8px 16px;
                    border: 1px solid #ddd;
                    background: white;
                    border-radius: 20px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }
                .spice-region-filter-buttons button:hover,
                .spice-region-filter-buttons button.active {
                    background: #a40d02;
                    color: white;
                    border-color: #a40d02;
                }
                .spice-region-filter-checkboxes {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 10px;
                }
                .spice-region-filter-checkboxes label {
                    display: flex;
                    align-items: center;
                    cursor: pointer;
                    padding: 8px;
                    border-radius: 4px;
                    transition: background 0.3s ease;
                }
                .spice-region-filter-checkboxes label:hover {
                    background: rgba(0,0,0,0.05);
                }
                .spice-region-filter-checkboxes input[type="checkbox"] {
                    margin-right: 8px;
                }
            </style>
            
            <h3>Filter by Spice Region</h3>
            
            <?php if ($atts['filter_type'] === 'dropdown'): ?>
                <select class="spice-region-dropdown" onchange="filterBySpiceRegion(this.value)">
                    <?php if ($atts['show_all_option']): ?>
                        <option value=""><?php echo esc_html($atts['all_text']); ?></option>
                    <?php endif; ?>
                    <?php foreach ($terms as $term): ?>
                        <option value="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></option>
                    <?php endforeach; ?>
                </select>
                
            <?php elseif ($atts['filter_type'] === 'buttons'): ?>
                <div class="spice-region-filter-buttons">
                    <?php if ($atts['show_all_option']): ?>
                        <button class="spice-region-btn active" data-term="" onclick="filterBySpiceRegion('')">
                            <?php echo esc_html($atts['all_text']); ?>
                        </button>
                    <?php endif; ?>
                    <?php foreach ($terms as $term): ?>
                        <button class="spice-region-btn" data-term="<?php echo esc_attr($term->slug); ?>" onclick="filterBySpiceRegion('<?php echo esc_attr($term->slug); ?>')">
                            <?php echo esc_html($term->name); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                
            <?php elseif ($atts['filter_type'] === 'checkboxes'): ?>
                <div class="spice-region-filter-checkboxes">
                    <?php if ($atts['show_all_option']): ?>
                        <label>
                            <input type="checkbox" class="spice-region-checkbox" data-term="" onchange="filterBySpiceRegionCheckboxes()" checked>
                            <?php echo esc_html($atts['all_text']); ?>
                        </label>
                    <?php endif; ?>
                    <?php foreach ($terms as $term): ?>
                        <label>
                            <input type="checkbox" class="spice-region-checkbox" data-term="<?php echo esc_attr($term->slug); ?>" onchange="filterBySpiceRegionCheckboxes()">
                            <?php echo esc_html($term->name); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <script>
        function filterBySpiceRegion(termSlug) {
            // Update URL parameter
            const url = new URL(window.location);
            if (termSlug) {
                url.searchParams.set('spice_region', termSlug);
            } else {
                url.searchParams.delete('spice_region');
            }
            window.history.pushState({}, '', url);
            
            // Update button states
            document.querySelectorAll('.spice-region-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.term === termSlug) {
                    btn.classList.add('active');
                }
            });
            
            // Trigger custom event for other elements to listen
            window.dispatchEvent(new CustomEvent('spiceRegionFilter', {
                detail: { termSlug: termSlug }
            }));
        }
        
        function filterBySpiceRegionCheckboxes() {
            const checkboxes = document.querySelectorAll('.spice-region-checkbox');
            const allCheckbox = document.querySelector('.spice-region-checkbox[data-term=""]');
            const otherCheckboxes = document.querySelectorAll('.spice-region-checkbox:not([data-term=""])');
            
            // If "All" is checked, uncheck others
            if (allCheckbox && allCheckbox.checked) {
                otherCheckboxes.forEach(cb => cb.checked = false);
                filterBySpiceRegion('');
                return;
            }
            
            // If any other is checked, uncheck "All"
            if (allCheckbox) {
                allCheckbox.checked = false;
            }
            
            // Get selected terms
            const selectedTerms = Array.from(otherCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.dataset.term);
            
            // Update URL
            const url = new URL(window.location);
            if (selectedTerms.length > 0) {
                url.searchParams.set('spice_region', selectedTerms.join(','));
            } else {
                url.searchParams.delete('spice_region');
            }
            window.history.pushState({}, '', url);
            
            // Trigger custom event
            window.dispatchEvent(new CustomEvent('spiceRegionFilter', {
                detail: { termSlug: selectedTerms.join(',') }
            }));
        }
        
        // Initialize filter from URL on page load
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const spiceRegion = urlParams.get('spice_region');
            
            if (spiceRegion) {
                // Set dropdown value
                const dropdown = document.querySelector('.spice-region-dropdown');
                if (dropdown) {
                    dropdown.value = spiceRegion;
                }
                
                // Set button states
                document.querySelectorAll('.spice-region-btn').forEach(btn => {
                    btn.classList.remove('active');
                    if (btn.dataset.term === spiceRegion) {
                        btn.classList.add('active');
                    }
                });
                
                // Set checkbox states
                const terms = spiceRegion.split(',');
                document.querySelectorAll('.spice-region-checkbox').forEach(cb => {
                    cb.checked = terms.includes(cb.dataset.term);
                });
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Spice Region Test Shortcode
     */
    public function spice_region_test_shortcode($atts) {
        $output = '<div style="background: #f0f0f0; padding: 20px; border: 2px solid #a40d02; margin: 20px 0;">';
        $output .= '<h3>🌶️ Spice Region Plugin Test</h3>';
        
        // Test 1: Check if plugin is loaded
        $output .= '<p><strong>✅ Plugin Status:</strong> Loaded and Active</p>';
        
        // Test 2: Check if TagDiv Composer is available
        if (function_exists('tdc_add_custom_element')) {
            $output .= '<p><strong>✅ TagDiv Composer:</strong> Available</p>';
        } else {
            $output .= '<p><strong>❌ TagDiv Composer:</strong> Not Available</p>';
        }
        
        // Test 3: Check spice_region taxonomy
        if (taxonomy_exists('spice_region')) {
            $output .= '<p><strong>✅ Spice Region Taxonomy:</strong> Registered</p>';
            
            // Get terms
            $terms = get_terms(array(
                'taxonomy' => 'spice_region',
                'hide_empty' => false,
            ));
            
            if (!is_wp_error($terms) && !empty($terms)) {
                $output .= '<p><strong>✅ Spice Region Terms:</strong> ' . count($terms) . ' terms found</p>';
                $output .= '<ul>';
                foreach ($terms as $term) {
                    $output .= '<li>' . esc_html($term->name) . ' (slug: ' . esc_html($term->slug) . ')</li>';
                }
                $output .= '</ul>';
            } else {
                $output .= '<p><strong>⚠️ Spice Region Terms:</strong> No terms found. Please add some spice region terms.</p>';
            }
        } else {
            $output .= '<p><strong>❌ Spice Region Taxonomy:</strong> Not Registered</p>';
        }
        
        // Test 4: Check shortcodes
        $output .= '<p><strong>✅ Available Shortcodes:</strong></p>';
        $output .= '<ul>';
        $output .= '<li><code>[spice_region_posts]</code> - Filtered post grids</li>';
        $output .= '<li><code>[td_spice_region_filter]</code> - Filter interface</li>';
        $output .= '<li><code>[latest_post_banner]</code> - Homepage banner</li>';
        $output .= '<li><code>[enhanced_smart_list]</code> - Smart list with filtering</li>';
        $output .= '</ul>';
        
        // Test 5: Instructions
        $output .= '<h4>📋 How to Use:</h4>';
        $output .= '<ol>';
        $output .= '<li><strong>In TagDiv Composer:</strong> Look for "Spice Region Posts", "Spice Region Filter", etc.</li>';
        $output .= '<li><strong>As Shortcodes:</strong> Use the shortcodes listed above</li>';
        $output .= '<li><strong>Example:</strong> <code>[spice_region_posts terms="lemongrass" posts_per_page="3"]</code></li>';
        $output .= '</ol>';
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Simple Debug Shortcode
     */
    public function spice_region_debug_simple_shortcode($atts) {
        $output = '<div style="background: #f0f0f0; padding: 10px; border: 1px solid #ccc; margin: 10px 0;">';
        $output .= '<strong>Simple Debug:</strong> Shortcode is working!<br>';
        
        // Check if spice_region taxonomy exists
        if (taxonomy_exists('spice_region')) {
            $output .= '✅ Spice Region taxonomy exists<br>';
            
            // Get terms
            $terms = get_terms(array(
                'taxonomy' => 'spice_region',
                'hide_empty' => false,
            ));
            
            if (!is_wp_error($terms) && !empty($terms)) {
                $output .= '✅ Found ' . count($terms) . ' spice region terms:<br>';
                foreach ($terms as $term) {
                    $output .= '- ' . esc_html($term->name) . ' (slug: ' . esc_html($term->slug) . ')<br>';
                }
            } else {
                $output .= '⚠️ No spice region terms found<br>';
            }
        } else {
            $output .= '❌ Spice Region taxonomy does not exist<br>';
        }
        
        $output .= '</div>';
        return $output;
    }
    
    /**
     * Simple Test Shortcode
     */
    public function spice_region_test_simple_shortcode($atts) {
        $output = '<div style="background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin: 10px 0; border-radius: 4px;">';
        $output .= '<strong>✅ Plugin Test:</strong> Shortcode is working!<br>';
        $output .= 'Current Post ID: ' . get_the_ID() . '<br>';
        $output .= 'Plugin Version: ' . SPICE_REGION_VERSION . '<br>';
        $output .= 'Time: ' . current_time('Y-m-d H:i:s') . '<br>';
        $output .= '</div>';
        return $output;
    }
    
    /**
     * Spice Region Related Posts Shortcode - Smart Auto-Detection
     */
    public function spice_region_related_posts_shortcode($atts) {
        $atts = shortcode_atts(array(
            'posts_per_page' => 6,
            'columns' => 3,
            'ratio' => '1600x872',
            'show_excerpt' => true,
            'show_taxonomy_title' => true,
            'exclude_current' => true,
            'fallback_terms' => '', // Fallback terms if no taxonomy found
            'show_debug' => false, // Show debug information
        ), $atts, 'spice_region_related_posts');
        
        // Get current post ID
        $current_post_id = get_the_ID();
        if (!$current_post_id) {
            return '<p>No current post found.</p>';
        }
        
        // Get current post's categories (spice regions)
        $current_post_terms = wp_get_post_categories($current_post_id, array('fields' => 'all'));
        
        // Always show debug information for troubleshooting
        $debug_info = '<div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border: 1px solid #ccc;">';
        $debug_info .= '<strong>Debug Info:</strong><br>';
        $debug_info .= 'Current Post ID: ' . $current_post_id . '<br>';
        $debug_info .= 'Found ' . count($current_post_terms) . ' spice region categories<br>';
        if (!empty($current_post_terms)) {
            foreach ($current_post_terms as $term) {
                $debug_info .= '- ' . esc_html($term->name) . ' (slug: ' . esc_html($term->slug) . ')<br>';
            }
        } else {
            $debug_info .= 'No spice region categories found for this post<br>';
        }
        $debug_info .= '</div>';
        
        // If no terms found, use fallback or return message
        if (empty($current_post_terms) || is_wp_error($current_post_terms)) {
            if (!empty($atts['fallback_terms'])) {
                // Use fallback terms
                $term_slugs = array_filter(array_map('trim', explode(',', strtolower($atts['fallback_terms']))));
                $debug_info .= '<p>Using fallback terms: ' . esc_html(implode(', ', $term_slugs)) . '</p>';
            } else {
                return $debug_info . '<p>No spice region categories found for current post. Please assign spice region categories to this post or use fallback_terms parameter.</p>';
            }
        } else {
            // Use current post's terms
            $term_slugs = array_map(function($term) {
                return $term->slug;
            }, $current_post_terms);
        }
        
        // Build query arguments using categories
        $query_args = array(
            'post_type' => 'post',
            'posts_per_page' => intval($atts['posts_per_page']),
            'post_status' => 'publish',
            'category_name' => implode(',', $term_slugs), // Use category slugs
        );
        
        // Exclude current post if requested
        if (!empty($atts['exclude_current'])) {
            $query_args['post__not_in'] = array($current_post_id);
        }
        
        // Execute query
        $q = new WP_Query($query_args);
        
        // Add query debug info
        $debug_info .= '<div style="background: #e8f4fd; padding: 10px; margin: 10px 0; border: 1px solid #2196F3;">';
        $debug_info .= '<strong>Query Debug:</strong><br>';
        $debug_info .= 'Query found: ' . $q->found_posts . ' posts<br>';
        $debug_info .= 'Terms searched: ' . esc_html(implode(', ', $term_slugs)) . '<br>';
        $debug_info .= 'Query args: ' . esc_html(print_r($query_args, true)) . '<br>';
        $debug_info .= '</div>';
        
        if (!$q->have_posts()) {
            return $debug_info . '<p>No related posts found for spice regions: ' . esc_html(implode(', ', $term_slugs)) . '</p>';
        }
        
        // Get first term for header
        $first_term = null;
        if (!empty($current_post_terms) && !is_wp_error($current_post_terms)) {
            $first_term = $current_post_terms[0];
        } else {
            $first_term = get_term_by('slug', $term_slugs[0], 'spice_region');
        }
        
        $header_html = '';
        if ($first_term && !empty($atts['show_taxonomy_title'])) {
            $header_html = '<div class="spice-region-header" style="display:flex;align-items:center;justify-content:center;margin:0 10px 20px;text-align:center;">'
                . '<div style="color:#a40d02;font-weight:700;font-size:36px;line-height:1;font-family:Fira Sans,sans-serif !important;">Related Posts: ' . esc_html($first_term->name) . '</div>'
              . '</div>';
        }
        
        ob_start();
        ?>
        <style>
            .spice-region-related-grid {
                display: grid;
                grid-template-columns: repeat(<?php echo esc_attr($atts['columns']); ?>, 1fr);
                gap: 20px;
                margin: 20px 0;
            }
            .spice-region-related-card {
                padding: 10px;
                box-sizing: border-box;
            }
            .spice-region-related-card-inner {
                background: #fff;
                border: 0;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                transition: transform 0.3s ease;
            }
            .spice-region-related-card-inner:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            }
            .spice-region-related-thumb {
                position: relative;
                overflow: hidden;
            }
            .spice-region-related-thumb img {
                width: 100%;
                height: auto;
                display: block;
                transition: transform 0.3s ease;
            }
            .spice-region-related-card-inner:hover .spice-region-related-thumb img {
                transform: scale(1.05);
            }
            .spice-region-related-content {
                padding: 15px;
            }
            .spice-region-related-title {
                margin: 0 0 10px 0;
                font-family: 'Merriweather', serif !important;
                font-size: 18px !important;
                line-height: 1.4 !important;
                font-weight: 700 !important;
                word-wrap: break-word;
            }
            .spice-region-related-title a {
                color: #333;
                text-decoration: none;
                transition: color 0.3s ease;
            }
            .spice-region-related-title a:hover {
                color: #a40d02;
            }
            .spice-region-related-excerpt {
                font-family: 'Merriweather', serif !important;
                font-size: 14px !important;
                line-height: 1.5 !important;
                font-weight: 300 !important;
                color: #666;
                margin: 0;
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .spice-region-related-meta {
                margin-top: 10px;
                font-size: 12px;
                color: #999;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .spice-region-related-category {
                background: #a40d02;
                color: white;
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 10px;
                text-transform: uppercase;
                font-weight: 600;
            }
            .spice-region-related-date {
                color: #999;
            }
            @media (max-width: 768px) {
                .spice-region-related-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        <?php echo $debug_info; ?>
        <div class="spice-region-related-posts-wrap">
            <?php echo $header_html; ?>
            <div class="spice-region-related-grid">
                <?php while ($q->have_posts()): $q->the_post(); ?>
                    <div class="spice-region-related-card">
                        <div class="spice-region-related-card-inner">
                            <?php if (has_post_thumbnail()): ?>
                                <div class="spice-region-related-thumb">
                                    <a href="<?php echo esc_url(get_permalink()); ?>">
                                        <?php the_post_thumbnail('medium', array('alt' => esc_attr(get_the_title()))); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="spice-region-related-content">
                                <h3 class="spice-region-related-title">
                                    <a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a>
                                </h3>
                                
                                <?php if (!empty($atts['show_excerpt'])): ?>
                                    <div class="spice-region-related-excerpt">
                                        <?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="spice-region-related-meta">
                                    <?php
                                    $categories = get_the_category();
                                    if (!empty($categories)): ?>
                                        <span class="spice-region-related-category">
                                            <?php echo esc_html($categories[0]->name); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <span class="spice-region-related-date">
                                        <?php echo esc_html(get_the_date()); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
    
    /**
     * TagDiv Single Background Featured Image Shortcode
     */
    public function tdb_single_bg_featured_image_shortcode($atts) {
        $atts = shortcode_atts(array(
            'image_alignment' => '20',
            'block_height' => '800',
            'image_size' => 'full',
            'show_categories' => true,
            'show_title' => true,
            'show_subtitle' => true,
            'show_date' => true,
            'show_views' => true,
            'show_support_button' => true,
            'support_button_text' => 'Support',
            'support_button_url' => 'https://nowpayments.io/donation/spicyauntie',
        ), $atts, 'tdb_single_bg_featured_image');
        
        // Get latest post
        $q = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => 1,
            'orderby' => 'date',
            'order' => 'DESC',
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
        ));
        
        if (!$q->have_posts()) {
            return '<p>No posts found.</p>';
        }
        
        $q->the_post();
        
        // Get featured image
        $thumb_url = get_the_post_thumbnail_url(get_the_ID(), $atts['image_size']);
        
        // Get subtitle from theme settings
        $subtitle = '';
        $post_id = get_the_ID();
        $theme_settings = get_post_meta($post_id, 'td_post_theme_settings', true);
        if (!empty($theme_settings) && is_string($theme_settings)) {
            $unserialized = maybe_unserialize($theme_settings);
            if (is_array($unserialized) && isset($unserialized['td_subtitle'])) {
                $subtitle = $unserialized['td_subtitle'];
            }
        }
        
        // Fallback to excerpt
        if (empty($subtitle) && has_excerpt()) {
            $subtitle = get_the_excerpt();
        }
        
        ob_start();
        ?>
        <div class="tdb-single-bg-featured-image-wrap" style="height: <?php echo esc_attr($atts['block_height']); ?>px;">
            <style>
                .tdb-single-bg-featured-image-wrap {
                    position: relative;
                    width: 100%;
                    background-image: <?php echo $thumb_url ? 'url(\'' . esc_url($thumb_url) . '\')' : 'none'; ?>;
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;
                    display: flex;
                    align-items: flex-end;
                    overflow: hidden;
                }
                .tdb-single-bg-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(135deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0.6) 100%);
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-end;
                    align-items: center;
                    padding: 20px 10px;
                    box-sizing: border-box;
                }
                .tdb-single-bg-content {
                    max-width: 1200px;
                    width: 100%;
                    text-align: center;
                    position: relative;
                    z-index: 2;
                }
                .tdb-single-categories {
                    margin-bottom: 15px;
                }
                .tdb-single-categories a {
                    display: inline-block;
                    background: rgba(255,255,255,0.9);
                    color: #333;
                    padding: 5px 6px 3px;
                    border: 1px solid rgba(255,255,255,0.3);
                    border-radius: 3px;
                    font-size: 11px;
                    font-weight: 400;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    text-decoration: none;
                    transition: all 0.3s ease;
                }
                .tdb-single-categories a:hover {
                    background: rgba(255,255,255,1);
                    border-color: #ffffff;
                }
                .tdb-single-title {
                    font-family: 'Fira Sans', sans-serif !important;
                    font-weight: 600;
                    font-size: 40px;
                    line-height: 1.2;
                    color: #ffffff;
                    margin: 0 0 15px 0;
                    padding: 0 8%;
                    word-wrap: break-word;
                }
                .tdb-single-subtitle {
                    font-family: 'Merriweather', serif !important;
                    font-size: 16px;
                    line-height: 1.3;
                    color: rgba(255,255,255,0.8);
                    margin: 0 0 10px 0;
                    font-style: normal;
                }
                .tdb-single-meta {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 22px;
                    margin-top: 20px;
                }
                .tdb-single-date {
                    font-family: 'Fira Sans', sans-serif !important;
                    font-size: 12px;
                    font-weight: 300;
                    text-transform: uppercase;
                    color: #ffffff;
                    letter-spacing: 1px;
                }
                .tdb-single-views {
                    font-family: 'Fira Sans', sans-serif !important;
                    font-size: 12px;
                    font-weight: 300;
                    text-transform: uppercase;
                    color: #ffffff;
                    letter-spacing: 1px;
                    display: flex;
                    align-items: center;
                    gap: 5px;
                }
                .tdb-single-views .td-icon {
                    font-size: 12px;
                    color: #ffffff;
                }
                .tdb-single-support-btn {
                    position: absolute;
                    top: 20px;
                    right: 20px;
                    background: rgba(255,255,255,0.95);
                    color: #333;
                    padding: 10px 18px;
                    border-radius: 25px;
                    text-decoration: none;
                    font-size: 12px;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.8px;
                    transition: all 0.3s ease;
                    z-index: 10;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }
                .tdb-single-support-btn:hover {
                    background: rgba(255,255,255,1);
                    transform: translateY(-2px);
                    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
                }
                @media (max-width: 768px) {
                    .tdb-single-bg-featured-image-wrap {
                        height: 500px !important;
                    }
                    .tdb-single-bg-overlay {
                        padding: 20px;
                    }
                    .tdb-single-title {
                        font-size: 26px;
                        padding: 0;
                    }
                    .tdb-single-subtitle {
                        font-size: 16px;
                    }
                    .tdb-single-meta {
                        gap: 16px;
                    }
                    .tdb-single-support-btn {
                        top: 15px;
                        right: 15px;
                        padding: 8px 14px;
                        font-size: 11px;
                    }
                }
                @media (max-width: 1018px) {
                    .tdb-single-bg-featured-image-wrap {
                        height: 500px !important;
                    }
                }
            </style>
            
            <div class="tdb-single-bg-overlay">
                <div class="tdb-single-bg-content">
                    <?php if (!empty($atts['show_support_button'])): ?>
                        <a href="<?php echo esc_url($atts['support_button_url']); ?>" target="_blank" class="tdb-single-support-btn">
                            <?php echo esc_html($atts['support_button_text']); ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($atts['show_categories'])): ?>
                        <div class="tdb-single-categories">
                            <?php
                            $categories = get_the_category();
                            if (!empty($categories)): ?>
                                <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>">
                                    <?php echo esc_html($categories[0]->name); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($atts['show_title'])): ?>
                        <h1 class="tdb-single-title">
                            <a href="<?php echo esc_url(get_permalink()); ?>" style="color: inherit; text-decoration: none;">
                                <?php echo esc_html(get_the_title()); ?>
                            </a>
                        </h1>
                    <?php endif; ?>
                    
                    <?php if (!empty($atts['show_subtitle']) && !empty($subtitle)): ?>
                        <div class="tdb-single-subtitle">
                            <?php echo esc_html($subtitle); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="tdb-single-meta">
                        <?php if (!empty($atts['show_date'])): ?>
                            <span class="tdb-single-date">
                                <?php echo esc_html(get_the_date()); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($atts['show_views'])): ?>
                            <span class="tdb-single-views">
                                <span class="td-icon td-icon-views"></span>
                                <?php echo esc_html(get_post_meta(get_the_ID(), 'post_views_count', true) ?: '0'); ?> views
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
    
    /**
     * Spice Region Single Term Shortcode
     */
    public function spice_region_single_shortcode($atts) {
        $atts = shortcode_atts(array(
            'term' => '',
        ), $atts, 'spice_region_single');
        
        if (empty($atts['term'])) {
            return '<p>Please specify a spice region term.</p>';
        }
        
        $term = get_term_by('slug', $atts['term'], 'spice_region');
        if (!$term) {
            return '<p>Spice region term not found.</p>';
        }
        
        $subtitle = function_exists('get_field') ? get_field('subtitle', 'spice_region_' . $term->term_id) : '';
        $icon = function_exists('get_field') ? get_field('icon', 'spice_region_' . $term->term_id) : '';
        $icon_url = is_array($icon) && !empty($icon['url']) ? $icon['url'] : '';
        
        ob_start();
        ?>
        <div class="spice-region-single-card" style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;">
            <?php if ($icon_url): ?>
                <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($term->name); ?>" style="width: 60px; height: 60px; object-fit: contain; margin-bottom: 15px;">
            <?php endif; ?>
            
            <h3 style="color: #a40d02; font-size: 24px; margin: 0 0 10px 0;"><?php echo esc_html($term->name); ?></h3>
            
            <?php if ($subtitle): ?>
                <p style="color: #666; font-size: 16px; margin: 0;"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
            
            <p style="margin: 15px 0 0 0;">
                <a href="<?php echo esc_url(get_term_link($term)); ?>" style="background: #a40d02; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; display: inline-block;">
                    View Posts
                </a>
            </p>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Spice Region Posts Shortcode
     */
    public function spice_region_posts_shortcode($atts) {
        $atts = shortcode_atts(array(
            'terms' => '',
            'posts_per_page' => 3,
            'columns' => 1,
            'ratio' => '1600x872',
            'no_watermark' => false,
            'show_taxonomy_title' => true,
            'filter_by_category' => '',
            'filter_by_tag' => '',
            'exclude_posts' => '',
        ), $atts, 'spice_region_posts');
        
        // Debug: Log shortcode parameters
        error_log('Spice Region Posts Shortcode - Terms: ' . $atts['terms']);
        
        $termSlugs = array_filter(array_map('trim', explode(',', strtolower($atts['terms']))));
        if (empty($termSlugs)) {
            return '<p>Please specify spice region terms. Current terms: ' . esc_html($atts['terms']) . '</p>';
        }
        
        $query_args = array(
            'post_type' => 'post',
            'posts_per_page' => intval($atts['posts_per_page']),
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => 'spice_region',
                    'field' => 'slug',
                    'terms' => $termSlugs,
                ),
            ),
        );
        
        // Add category filter if specified
        if (!empty($atts['filter_by_category'])) {
            $category_ids = array_map('intval', array_filter(explode(',', $atts['filter_by_category'])));
            if (!empty($category_ids)) {
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => $category_ids,
                );
            }
        }
        
        // Add tag filter if specified
        if (!empty($atts['filter_by_tag'])) {
            $tag_slugs = array_filter(array_map('trim', explode(',', $atts['filter_by_tag'])));
            if (!empty($tag_slugs)) {
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'post_tag',
                    'field' => 'slug',
                    'terms' => $tag_slugs,
                );
            }
        }
        
        // Add exclude posts if specified
        if (!empty($atts['exclude_posts'])) {
            $exclude_ids = array_map('intval', array_filter(explode(',', $atts['exclude_posts'])));
            if (!empty($exclude_ids)) {
                $query_args['post__not_in'] = $exclude_ids;
            }
        }
        
        // Set relation for multiple tax queries
        if (count($query_args['tax_query']) > 1) {
            $query_args['tax_query']['relation'] = 'AND';
        }
        
        $q = new WP_Query($query_args);
        
        // Debug: Log query results
        error_log('Spice Region Posts Query - Found posts: ' . $q->found_posts);
        
        if (!$q->have_posts()) {
            return '<p>No posts found for the specified spice regions. Terms searched: ' . esc_html(implode(', ', $termSlugs)) . '</p>';
        }
        
        // Get first term for header
        $first_term = null;
        if (!empty($termSlugs)) {
            $first_term = get_term_by('slug', $termSlugs[0], 'spice_region');
        }
        
        $header_html = '';
        if ($first_term && !empty($atts['show_taxonomy_title'])) {
            $subtitle = function_exists('get_field') ? get_field('subtitle', 'spice_region_' . $first_term->term_id) : '';
            $icon = function_exists('get_field') ? get_field('icon', 'spice_region_' . $first_term->term_id) : '';
            $icon_url = is_array($icon) && !empty($icon['url']) ? $icon['url'] : '';
            
            $header_html = '<div class="spice-region-header" style="display:flex;align-items:center;justify-content:center;margin:0 10px 20px;text-align:center;">'
                . '<div style="color:#a40d02;font-weight:700;font-size:36px;line-height:1;font-family:Fira Sans,sans-serif !important;">' . esc_html($first_term->name) . '</div>'
              . '</div>';
        }
        
        ob_start();
        ?>
        <style>
            .spice-region-grid {
                display: grid;
                grid-template-columns: repeat(<?php echo esc_attr($atts['columns']); ?>, 1fr);
                gap: 20px;
                margin: 20px 0;
            }
            .spice-region-card {
                padding: 10px;
                box-sizing: border-box;
            }
            .spice-region-card-inner {
                background: #fff;
                border: 0;
            }
            .spice-region-thumb img {
                width: 100%;
                height: auto;
                display: block;
            }
            .spice-region-posts .entry-title {
                margin: 0 6px 0 0;
                font-family: Merriweather !important;
                font-size: 16px !important;
                line-height: 1.5 !important;
                font-weight: 800 !important;
                word-wrap: break-word;
            }
            .spice-region-posts .entry-title a {
                display: block;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                text-decoration: none;
            }
            .spice-region-posts .td-excerpt {
                display: block;
                margin: 4px 0;
                font-family: Merriweather !important;
                font-size: 14px !important;
                line-height: 1.5 !important;
                font-weight: 300 !important;
                color: #767676;
                overflow-wrap: anywhere;
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .spice-region-posts .td-post-category {
                margin: 0 0 -15px 0;
                padding: 8px 12px 7px;
                background-color: #ffffff;
                color: #000000;
                font-family: Fira Sans !important;
                font-size: 15px !important;
                line-height: 1 !important;
                font-weight: 600 !important;
                text-transform: uppercase !important;
                letter-spacing: 1px !important;
                text-decoration: none !important;
                display: inline-block !important;
                position: absolute;
                left: 0px;
                bottom: 3px;
                z-index: 2;
            }
        </style>
        <div class="spice-region-grid">
            <?php echo $header_html; ?>
            <?php while ($q->have_posts()): $q->the_post(); ?>
                <div class="spice-region-card">
                    <div class="spice-region-card-inner">
                        <div class="spice-region-thumb" style="position: relative;">
                            <a href="<?php echo esc_url(get_permalink()); ?>">
                                <?php if (has_post_thumbnail()): ?>
                                    <img src="<?php echo esc_url(get_the_post_thumbnail_url(null, 'medium')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
                                <?php endif; ?>
                            </a>
                            <?php
                            $categories = get_the_category();
                            if (!empty($categories)): ?>
                                <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>" class="td-post-category">
                                    <?php echo esc_html($categories[0]->name); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <h3 class="entry-title td-module-title">
                            <a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a>
                        </h3>
                        <div class="td-excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 28)); ?></div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
    
    /**
     * Spice Region Current Posts Shortcode
     */
    public function spice_region_current_posts_shortcode($atts) {
        $atts = shortcode_atts(array(
            'posts_per_page' => 3,
            'columns' => 1,
            'ratio' => '1600x872',
            'no_watermark' => false,
        ), $atts, 'spice_region_current_posts');
        
        // Get current post's spice regions
        $current_post_terms = wp_get_post_terms(get_the_ID(), 'spice_region');
        if (empty($current_post_terms)) {
            return '<p>No spice regions found for current post.</p>';
        }
        
        $term_slugs = array_map(function($term) {
            return $term->slug;
        }, $current_post_terms);
        
        return $this->spice_region_posts_shortcode(array_merge($atts, array('terms' => implode(',', $term_slugs))));
    }
    
    /**
     * Latest Post Banner Shortcode
     */
    public function latest_post_banner_shortcode($atts) {
        $atts = shortcode_atts(array(
            'posts_per_page' => 1,
            'post_type' => 'post',
            'image_size' => 'full',
            'ratio' => '16:9',
            'no_watermark' => true,
            'text_color' => '#ffffff',
            'overlay_color' => 'rgba(0,0,0,0.3)',
            'show_categories' => true,
            'show_date' => true,
            'show_excerpt' => true,
            'show_support_button' => true,
            'support_button_text' => 'Support',
            'support_button_url' => 'https://nowpayments.io/donation/spicyauntie',
        ), $atts, 'latest_post_banner');
        
        $q = new WP_Query(array(
            'post_type' => $atts['post_type'],
            'posts_per_page' => intval($atts['posts_per_page']),
            'orderby' => 'date',
            'order' => 'DESC',
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
        ));
        
        if (!$q->have_posts()) {
            return '';
        }
        
        ob_start();
        $q->the_post();
        
        // Calculate aspect ratio
        $ratio_css = '56.25%'; // 16:9 ratio
        if (!empty($atts['ratio'])) {
            if (preg_match('/^(\\d+):(\\d+)$/', trim($atts['ratio']), $m)) {
                $rw = max(1, intval($m[1]));
                $rh = max(1, intval($m[2]));
                $ratio_css = (round($rh / $rw, 4) * 100) . '%';
            }
        }
        
        // Get thumbnail URL
        $thumb_url = get_the_post_thumbnail_url(get_the_ID(), $atts['image_size']);
        
        ?>
        <div class="life-news-banner-wrap" style="margin-bottom: 30px;">
            <style>
                .life-news-banner {
                    position: relative;
                    width: 100%;
                    padding-top: <?php echo esc_attr($ratio_css); ?>;
                    background-image: <?php echo $thumb_url ? 'url(\'' . esc_url($thumb_url) . '\')' : 'none'; ?>;
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;
                    display: flex;
                    align-items: flex-end;
                    justify-content: flex-start;
                    overflow: hidden;
                    border-radius: 8px;
                    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
                }
                .life-news-banner-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(135deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0.6) 100%);
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-end;
                    align-items: flex-start;
                    padding: 40px;
                    box-sizing: border-box;
                }
                .life-news-banner-content {
                    max-width: 70%;
                    width: 100%;
                    text-align: left;
                }
                .life-news-banner-category {
                    display: inline-block;
                    background: rgba(255,255,255,0.9);
                    color: #333;
                    padding: 6px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin-bottom: 15px;
                    text-decoration: none;
                    transition: all 0.3s ease;
                }
                .life-news-banner-category:hover {
                    background: rgba(255,255,255,1);
                    transform: translateY(-1px);
                }
                .life-news-banner-title {
                    font-family: 'Merriweather', serif !important;
                    font-weight: 800;
                    font-size: 52px;
                    line-height: 1.1;
                    color: <?php echo esc_attr($atts['text_color']); ?>;
                    text-shadow: 2px 2px 8px rgba(0,0,0,0.8);
                    margin: 0 0 15px 0;
                    display: block;
                    word-wrap: break-word;
                    hyphens: auto;
                }
                .life-news-banner-description {
                    font-family: 'Merriweather', serif !important;
                    font-size: 18px;
                    line-height: 1.6;
                    color: <?php echo esc_attr($atts['text_color']); ?>;
                    text-shadow: 1px 1px 4px rgba(0,0,0,0.8);
                    margin: 0 0 20px 0;
                    display: -webkit-box;
                    -webkit-line-clamp: 3;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                    opacity: 0.95;
                }
                .life-news-banner-meta {
                    display: flex;
                    align-items: center;
                    gap: 20px;
                    margin-top: 20px;
                }
                .life-news-banner-date {
                    font-family: 'Fira Sans', sans-serif !important;
                    font-weight: 400;
                    font-size: 14px;
                    color: <?php echo esc_attr($atts['text_color']); ?>;
                    text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
                    opacity: 0.9;
                }
                .life-news-banner-author {
                    font-family: 'Fira Sans', sans-serif !important;
                    font-weight: 500;
                    font-size: 14px;
                    color: <?php echo esc_attr($atts['text_color']); ?>;
                    text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
                    opacity: 0.9;
                    text-decoration: none;
                }
                .life-news-banner-author:hover {
                    opacity: 1;
                }
                .life-news-banner-support-btn {
                    position: absolute;
                    top: 20px;
                    right: 40px;
                    background: rgba(255,255,255,0.95);
                    color: #333;
                    padding: 10px 18px;
                    border-radius: 25px;
                    text-decoration: none;
                    font-size: 12px;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.8px;
                    transition: all 0.3s ease;
                    z-index: 10;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }
                .life-news-banner-support-btn:hover {
                    background: rgba(255,255,255,1);
                    transform: translateY(-2px);
                    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
                }
                @media (max-width: 768px) {
                    .life-news-banner-overlay {
                        padding: 20px;
                    }
                    .life-news-banner-content {
                        max-width: 100%;
                    }
                    .life-news-banner-title {
                        font-size: 36px;
                    }
                    .life-news-banner-description {
                        font-size: 16px;
                    }
                    .life-news-banner-support-btn {
                        top: 15px;
                        right: 20px;
                        padding: 8px 14px;
                        font-size: 11px;
                    }
                }
            </style>
            <div class="life-news-banner">
                <?php if (!empty($atts['show_support_button'])): ?>
                    <a href="<?php echo esc_url($atts['support_button_url']); ?>" target="_blank" class="life-news-banner-support-btn">
                        <?php echo esc_html($atts['support_button_text']); ?>
                    </a>
                <?php endif; ?>
                <div class="life-news-banner-overlay">
                    <div class="life-news-banner-content">
                        <?php
                        // Get post categories
                        $categories = get_the_category();
                        if (!empty($categories) && !empty($atts['show_categories'])) : ?>
                            <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>" class="life-news-banner-category">
                                <?php echo esc_html($categories[0]->name); ?>
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo esc_url(get_permalink()); ?>" rel="bookmark" title="<?php echo esc_attr(get_the_title()); ?>">
                            <h2 class="life-news-banner-title"><?php echo esc_html(get_the_title()); ?></h2>
                        </a>
                        
                        <?php 
                        // Get description text
                        $description_text = '';
                        $post_id = get_the_ID();
                        
                        // Get subtitle from TagDiv's theme settings
                        $theme_settings = get_post_meta($post_id, 'td_post_theme_settings', true);
                        if (!empty($theme_settings) && is_string($theme_settings)) {
                            $unserialized = maybe_unserialize($theme_settings);
                            if (is_array($unserialized) && isset($unserialized['td_subtitle'])) {
                                $description_text = $unserialized['td_subtitle'];
                            }
                        }
                        
                        // Fallback to excerpt or content
                        if (empty($description_text)) {
                            if (has_excerpt()) {
                                $description_text = get_the_excerpt();
                            } elseif (get_the_content()) {
                                $description_text = get_the_content();
                            }
                        }
                        
                        if ($description_text && !empty($atts['show_excerpt'])) : ?>
                            <div class="life-news-banner-description">
                                <?php echo esc_html(wp_trim_words($description_text, 25)); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($atts['show_date'])) : ?>
                            <div class="life-news-banner-meta">
                                <span class="life-news-banner-date">
                                    <?php echo esc_html(get_the_date('F j, Y')); ?>
                                </span>
                                <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="life-news-banner-author">
                                    By <?php echo esc_html(get_the_author()); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
    
    /**
     * Debug shortcode
     */
    public function spice_region_debug_shortcode($atts) {
        $current_post_terms = wp_get_post_terms(get_the_ID(), 'spice_region');
        ob_start();
        ?>
        <div style="background: #f0f0f0; padding: 20px; margin: 20px 0; border: 1px solid #ccc;">
            <h3>Spice Region Debug Info</h3>
            <p><strong>Current Post ID:</strong> <?php echo get_the_ID(); ?></p>
            <p><strong>Current Post Terms:</strong></p>
            <ul>
                <?php foreach ($current_post_terms as $term): ?>
                    <li><?php echo esc_html($term->name); ?> (<?php echo esc_html($term->slug); ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Initialize the plugin
new SpiceRegionShortcodes();

// TagDiv Composer shortcode handlers
add_shortcode('td_spice_region_posts', function($atts) {
    $atts = shortcode_atts(array(
        'terms' => '',
        'posts_per_page' => 3,
        'columns' => 1,
        'ratio' => '1600x872',
        'no_watermark' => false,
        'show_taxonomy_title' => true,
    ), $atts, 'td_spice_region_posts');
    
    return do_shortcode('[spice_region_posts terms="' . esc_attr($atts['terms']) . '" posts_per_page="' . esc_attr($atts['posts_per_page']) . '" columns="' . esc_attr($atts['columns']) . '" ratio="' . esc_attr($atts['ratio']) . '" no_watermark="' . ($atts['no_watermark'] ? '1' : '0') . '" show_taxonomy_title="' . ($atts['show_taxonomy_title'] ? '1' : '0') . '"]');
});

add_shortcode('td_latest_post_banner', function($atts) {
    $atts = shortcode_atts(array(
        'ratio' => '1600x872',
        'text_color' => '#ffffff',
        'overlay_color' => 'rgba(0,0,0,0.4)',
        'no_watermark' => true,
        'show_categories' => true,
        'show_date' => true,
        'show_excerpt' => true,
    ), $atts, 'td_latest_post_banner');
    
    return do_shortcode('[latest_post_banner ratio="' . esc_attr($atts['ratio']) . '" text_color="' . esc_attr($atts['text_color']) . '" overlay_color="' . esc_attr($atts['overlay_color']) . '" no_watermark="' . ($atts['no_watermark'] ? '1' : '0') . '" show_categories="' . ($atts['show_categories'] ? '1' : '0') . '" show_date="' . ($atts['show_date'] ? '1' : '0') . '" show_excerpt="' . ($atts['show_excerpt'] ? '1' : '0') . '"]');
});

// Initialize the plugin
new SpiceRegionShortcodes();
