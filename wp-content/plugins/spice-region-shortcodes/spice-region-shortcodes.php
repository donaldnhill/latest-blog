<?php
/**
 * Plugin Name: Spice Region Shortcodes
 * Plugin URI: https://yourwebsite.com
 * Description: Custom shortcodes for latest post banner functionality.
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
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function init() {
        // Register all shortcodes
        $this->register_shortcodes();
        
        // Register TagDiv Composer elements
        add_action('tdc_init', array($this, 'register_tagdiv_elements'));
        add_action('wp_loaded', array($this, 'register_tagdiv_elements'));
        add_action('admin_init', array($this, 'register_tagdiv_elements'));
    }
    
    public function enqueue_scripts() {
        // Enqueue any necessary scripts/styles here
    }
    
    /**
     * Register all shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('latest_post_banner', array($this, 'latest_post_banner_shortcode'));
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
                    justify-content: center;
                    overflow: hidden;
                }
                .life-news-banner-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: <?php echo esc_attr($atts['overlay_color']); ?>;
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-end;
                    align-items: center;
                    padding: 40px 20px;
                    box-sizing: border-box;
                }
                .life-news-banner-content {
                    max-width: 800px;
                    width: 100%;
                    text-align: center;
                }
                .life-news-banner-title {
                    font-family: 'Merriweather', serif;
                    font-weight: 700;
                    font-size: 48px;
                    line-height: 1.2;
                    color: <?php echo esc_attr($atts['text_color']); ?>;
                    text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
                    margin: 0 0 20px 0;
                }
                .life-news-banner-description {
                    font-family: 'Merriweather', serif;
                    font-size: 20px;
                    line-height: 1.5;
                    color: <?php echo esc_attr($atts['text_color']); ?>;
                    text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
                    margin: 0 0 20px 0;
                }
                .life-news-banner-category {
                    display: inline-block;
                    background: #a40d02;
                    color: white;
                    padding: 8px 16px;
                    border-radius: 4px;
                    text-decoration: none;
                    font-family: 'Fira Sans', sans-serif;
                    font-weight: 600;
                    font-size: 14px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    margin-bottom: 20px;
                    transition: all 0.3s ease;
                }
                .life-news-banner-category:hover {
                    background: #8a0a02;
                    color: white;
                    text-decoration: none;
                }
                .life-news-banner-support-btn {
                    position: absolute;
                    top: 20px;
                    right: 40px;
                    background: #a40d02;
                    color: white;
                    padding: 12px 20px;
                    border: none;
                    border-radius: 6px;
                    font-family: 'Fira Sans', sans-serif;
                    font-weight: 600;
                    font-size: 16px;
                    text-decoration: none;
                    display: inline-block;
                    transition: all 0.3s ease;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                    z-index: 10;
                }
                .life-news-banner-support-btn:hover {
                    background: #8a0a02;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0,0,0,0.4);
                    color: white;
                    text-decoration: none;
                }
                .life-news-banner .entry-date {
                    font-family: Fira Sans !important;
                    font-weight: 400;
                    font-size: 12px !important;
                    color: <?php echo esc_attr($atts['text_color']); ?> !important;
                    text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
                    margin-top: 15px;
                    text-align: center;
                }
                @media (max-width: 768px) {
                    .life-news-banner-title { font-size: 32px; }
                    .life-news-banner-description { font-size: 18px; }
                    .life-news-banner-support-btn {
                        top: 15px;
                        right: 20px;
                        padding: 10px 16px;
                        font-size: 14px;
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
                        
                        // Fallback to excerpt if no subtitle
                        if (empty($description_text) && has_excerpt()) {
                            $description_text = get_the_excerpt();
                        }
                        
                        if (!empty($description_text) && !empty($atts['show_excerpt'])) : ?>
                            <div class="life-news-banner-description">
                                <?php echo esc_html(wp_trim_words($description_text, 30)); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($atts['show_date'])): ?>
                            <div class="entry-date updated td-module-date">
                                <?php echo esc_html(get_the_date('F j, Y')); ?>
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
     * Register TagDiv Composer elements
     */
    public function register_tagdiv_elements() {
        if (function_exists('tdc_register_elements')) {
            tdc_register_elements(array(
                array(
                    'text' => 'Latest Post Banner',
                    'title' => 'Latest Post Banner',
                    'class' => 'tdc-latest-post-banner',
                    'icon' => 'tdc-font-icon tdc-font-icon-latest-post-banner',
                    'file' => SPICE_REGION_PLUGIN_PATH . 'includes/tdc-latest-post-banner.php',
                    'params' => array(
                        array(
                            'param_name' => 'ratio',
                            'type' => 'textfield',
                            'value' => '16:9',
                            'heading' => 'Aspect Ratio',
                            'description' => 'Enter aspect ratio (e.g., 16:9)',
                        ),
                        array(
                            'param_name' => 'text_color',
                            'type' => 'colorpicker',
                            'value' => '#ffffff',
                            'heading' => 'Text Color',
                            'description' => 'Choose text color',
                        ),
                        array(
                            'param_name' => 'overlay_color',
                            'type' => 'colorpicker',
                            'value' => 'rgba(0,0,0,0.3)',
                            'heading' => 'Overlay Color',
                            'description' => 'Choose overlay color',
                        ),
                        array(
                            'param_name' => 'no_watermark',
                            'type' => 'checkbox',
                            'value' => '1',
                            'heading' => 'No Watermark',
                            'description' => 'Remove watermark from images',
                        ),
                    ),
                    'shortcode' => 'latest_post_banner',
                ),
            ));
        }
    }
}

// Initialize the plugin
new SpiceRegionShortcodes();
