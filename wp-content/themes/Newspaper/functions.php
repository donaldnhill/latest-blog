<?php

define('TAGDIV_ROOT', get_template_directory_uri());
define('TAGDIV_ROOT_DIR', get_template_directory());

// Debug user capabilities for post settings
function debug_user_capabilities() {
    if (is_admin() && current_user_can('edit_posts')) {
        $user = wp_get_current_user();
        $user_roles = $user->roles;
        $user_caps = $user->allcaps;
        
        // Log to error log for debugging
        error_log('=== USER CAPABILITIES DEBUG ===');
        error_log('User ID: ' . $user->ID);
        error_log('User Login: ' . $user->user_login);
        error_log('User Roles: ' . print_r($user_roles, true));
        error_log('Has publish_posts: ' . (current_user_can('publish_posts') ? 'YES' : 'NO'));
        error_log('Has edit_posts: ' . (current_user_can('edit_posts') ? 'YES' : 'NO'));
        error_log('Has edit_others_posts: ' . (current_user_can('edit_others_posts') ? 'YES' : 'NO'));
        error_log('=== END DEBUG ===');
    }
}
add_action('admin_init', 'debug_user_capabilities');

// Ensure editors have publish_posts capability for post settings
function ensure_editor_capabilities() {
    $editor_role = get_role('editor');
    if ($editor_role) {
        // Force add all necessary capabilities for post settings
        $editor_role->add_cap('publish_posts');
        $editor_role->add_cap('edit_posts');
        $editor_role->add_cap('edit_others_posts');
        $editor_role->add_cap('edit_published_posts');
        error_log('Ensured editor role has all post capabilities');
    }
}
add_action('init', 'ensure_editor_capabilities');

// Force show post settings for editors
function force_show_post_settings_for_editors() {
    if (current_user_can('edit_posts') && !current_user_can('publish_posts')) {
        $user = wp_get_current_user();
        $user->add_cap('publish_posts');
        error_log('Force added publish_posts capability to user: ' . $user->user_login);
    }
}
add_action('admin_init', 'force_show_post_settings_for_editors');

// Override the TagDiv Composer capability check for post settings
function override_td_post_settings_capability() {
    // Force the capability check to return true for editors
    add_filter('user_has_cap', function($allcaps, $caps, $args) {
        if (in_array('publish_posts', $caps) && current_user_can('edit_posts')) {
            $allcaps['publish_posts'] = true;
        }
        return $allcaps;
    }, 10, 3);
}
add_action('init', 'override_td_post_settings_capability');

// Force register post settings meta box for editors
function force_register_post_settings_metabox() {
    if (current_user_can('edit_posts')) {
        // Include the WPAlchemy MetaBox class if not already loaded
        if (!class_exists('WPAlchemy_MetaBox')) {
            $tdc_path = defined('TDC_PATH') ? TDC_PATH : '';
            if (empty($tdc_path)) {
                // Try to find the td-composer plugin path
                $plugins = get_plugins();
                foreach ($plugins as $plugin_file => $plugin_data) {
                    if (strpos($plugin_file, 'td-composer') !== false) {
                        $tdc_path = WP_PLUGIN_DIR . '/' . dirname($plugin_file) . '/legacy/common/wp_booster/wp-admin/external/wpalchemy/MetaBox.php';
                        break;
                    }
                }
            } else {
                $tdc_path .= '/legacy/common/wp_booster/wp-admin/external/wpalchemy/MetaBox.php';
            }
            
            if (file_exists($tdc_path)) {
                include_once $tdc_path;
            }
        }
        
        // Register the meta box directly
        if (class_exists('WPAlchemy_MetaBox')) {
            $post_settings_mb_setup_options = array(
                'id' => 'td_post_theme_settings',
                'title' => 'Post Settings',
                'types' => array('post'),
                'priority' => 'high',
                'template' => defined('TDC_PATH') ? TDC_PATH . '/legacy/common/wp_booster/wp-admin/content-metaboxes/td_set_post_settings.php' : ''
            );
            
            new WPAlchemy_MetaBox($post_settings_mb_setup_options);
            error_log('Force registered Post Settings meta box for editor');
        }
    }
}
add_action('admin_init', 'force_register_post_settings_metabox');

// load the deploy mode
require_once( TAGDIV_ROOT_DIR . '/tagdiv-deploy-mode.php' );

/**
 * Theme configuration.
 */
require_once TAGDIV_ROOT_DIR . '/includes/tagdiv-config.php';

/**
 * Theme wp booster.
 */
require_once( TAGDIV_ROOT_DIR . '/includes/wp-booster/tagdiv-wp-booster-functions.php');

/**
 * Theme page generator support.
 */
if ( ! class_exists('tagdiv_page_generator' ) ) {
	include_once ( TAGDIV_ROOT_DIR . '/includes/tagdiv-page-generator.php');
}

// Shortcode: [latest_post_banner] - Homepage cover banner with latest post
add_shortcode('latest_post_banner', function($atts) {
    error_log('LATEST POST BANNER SHORTCODE CALLED');
    $atts = shortcode_atts(array(
        'posts_per_page' => 1,
        'post_type' => 'post',
        'image_size' => 'full',
        'ratio' => '1600x872',
        'no_watermark' => true,
        'text_color' => '#ffffff',
        'overlay_color' => 'rgba(0,0,0,0.4)',
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

    $imageSize = trim($atts['image_size']);
    if (preg_match('/^\\d+x\\d+$/', $imageSize)) {
        list($w, $h) = array_map('intval', explode('x', $imageSize));
        $imageSize = array($w, $h);
    }

    // Calculate aspect ratio
    $ratio_css = '56.25%';
    if (!empty($atts['ratio']) && preg_match('/^(\\d+)x(\\d+)$/', trim($atts['ratio']), $m)) {
        $rw = max(1, intval($m[1]));
        $rh = max(1, intval($m[2]));
        $ratio_css = (round($rh / $rw, 4) * 100) . '%';
    }

    // Get thumbnail URL
    $thumb_url = get_the_post_thumbnail_url(get_the_ID(), $imageSize);
    if (!empty($atts['no_watermark'])) {
        $thumb_id = get_post_thumbnail_id();
        if ($thumb_id) {
            $file = get_attached_file($thumb_id);
            $uploads = wp_get_upload_dir();
            if ($file && $uploads && strpos($file, $uploads['basedir']) === 0) {
                $rel = ltrim(substr($file, strlen($uploads['basedir'])), '/');
                $backup_path = WP_CONTENT_DIR . '/ew-backup/' . $rel;
                if (file_exists($backup_path)) {
                    $thumb_url = content_url('ew-backup/' . str_replace(['\\','//'], ['/', '/'], $rel));
                }
            }
        }
    }

    $banner_style = $thumb_url ? 'background-image: url(\'' . esc_url($thumb_url) . '\');' : '';
    $banner_style .= 'background-size: cover; background-position: center;';

    ?>
    <div class="latest-post-banner-wrap" style="margin-bottom: 20px;">
        <style>
            .latest-post-banner {
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
            .latest-post-banner-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: transparent;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                align-items: center;
                padding: 20px;
                box-sizing: border-box;
            }
            /* Individual Container Styles */
            .latest-post-banner-category-container {
                max-width: 600px;
                width: 100%;
                text-align: center;
                margin: 0 auto;
            }
            
            .latest-post-banner-title-container {
                width: 100%;
                text-align: center;
                margin: 10px 0;
            }
            
            .latest-post-banner-description-container {
                max-width: 600px;
                width: 100%;
                text-align: center;
                margin: 0 auto;
            }
            
            .latest-post-banner-date-container {
                max-width: 600px;
                width: 100%;
                text-align: center;
                margin: 0 auto;
            }
            
            
            
            @media (max-width: 768px) {
                .latest-post-banner-category-container,
                .latest-post-banner-description-container,
                .latest-post-banner-date-container {
                    max-width: 100%;
                    padding: 0 10px;
                }
            }
            
            .latest-post-banner-title-link {
                display: block;
                width: 100%;
                margin: 0;
            }
            .latest-post-banner-title {
                color: #ffffff;
                font-family: Fira Sans !important;
                font-size: 40px !important;
                line-height: 1.2 !important;
                font-weight: 600 !important;
                display: inline-block;
                position: relative;
                margin: 0;
                word-wrap: break-word;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
            }
            .latest-post-banner-description {
                font-family: Merriweather, serif !important;
                font-size: 18px;
                line-height: 1.5;
                color: <?php echo esc_attr($atts['text_color']); ?>;
                text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
                margin: 0;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            .latest-post-banner .tdb-category {
                margin: 0 0 10px 0;
                line-height: 1;
                font-family: 'Fira Sans', sans-serif;
            }
            .latest-post-banner .tdb-entry-category {
                pointer-events: auto;
                font-size: 10px;
                display: inline-block;
                margin: 0 5px 5px 0;
                line-height: 1;
                color: #fff;
                padding: 3px 6px 4px 6px;
                white-space: nowrap;
                position: relative;
                vertical-align: middle;
                background-color: transparent;
                text-decoration: none;
                font-family: 'Fira Sans', sans-serif !important;
                font-weight: 400 !important;
                text-transform: uppercase !important;
                letter-spacing: 1px !important;
            }
            .latest-post-banner .tdb-entry-category:hover .tdb-cat-bg {
                opacity: 0.9;
            }
            .latest-post-banner .tdb-entry-category:hover .tdb-cat-bg:before {
                opacity: 1;
            }
            .latest-post-banner .tdb-cat-bg {
                position: absolute;
                background-color: rgba(0,0,0,0) !important;
                border: 1px solid rgba(255,255,255,0.3) !important;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                z-index: -1;
            }
            .latest-post-banner .tdb-entry-category:hover .tdb-cat-bg {
                border-color: #ffffff !important;
            }
            .latest-post-banner .entry-date {
                font-family: Fira Sans !important;
                font-weight: 400;
                font-size: 12px !important;
                color: #ffffff !important;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
                margin-top: 15px;
                text-align: center;
                vertical-align: middle;
            }
            .latest-post-banner .tdb-post-meta span, 
            .latest-post-banner .tdb-post-meta i, 
            .latest-post-banner .tdb-post-meta time {
                vertical-align: middle;
            }
            .latest-post-banner-support-btn {
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
            
            .latest-post-banner-support-btn:hover {
                background: #8a0a02;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.4);
                color: white;
                text-decoration: none;
            }
            
            @media (max-width: 768px) {
                .latest-post-banner-title { 
                    font-size: 32px !important;
                }
                .latest-post-banner-description { font-size: 16px; }
                .latest-post-banner-support-btn {
                    top: 15px;
                    right: 35px;
                    padding: 10px 16px;
                    font-size: 13px;
                }
            }
        </style>
        <div class="latest-post-banner">
            <a href="https://nowpayments.io/donation/spicyauntie" target="_blank" class="latest-post-banner-support-btn">Support</a>
            <div class="latest-post-banner-overlay">
                <!-- Category Container -->
                <?php
                // Get post categories
                $categories = get_the_category();
                if (!empty($categories)) : ?>
                    <div class="latest-post-banner-category-container">
                        <div class="tdb-category td-fix-index">
                            <?php foreach ($categories as $category) : ?>
                                <a class="tdb-entry-category" href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
                                    <span class="tdb-cat-bg"></span><?php echo esc_html($category->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Title Container - Full Width -->
                <div class="latest-post-banner-title-container">
                    <a href="<?php echo esc_url(get_permalink()); ?>" rel="bookmark" title="<?php echo esc_attr(get_the_title()); ?>" class="latest-post-banner-title-link">
                        <h2 class="latest-post-banner-title"><?php echo esc_html(get_the_title()); ?></h2>
                    </a>
                </div>
                    <?php 
                    // Get subtitle from TagDiv's theme settings (serialized data)
                    $post_id = get_the_ID();
                    $subtitle = '';
                    
                    // Get the theme settings which contains the subtitle
                    $theme_settings = get_post_meta($post_id, 'td_post_theme_settings', true);
                    error_log('Banner Debug - Theme settings raw: ' . print_r($theme_settings, true));
                    
                    if (!empty($theme_settings) && is_string($theme_settings)) {
                        $unserialized = maybe_unserialize($theme_settings);
                        error_log('Banner Debug - Unserialized theme settings: ' . print_r($unserialized, true));
                        
                        if (is_array($unserialized) && isset($unserialized['td_subtitle'])) {
                            $subtitle = $unserialized['td_subtitle'];
                            error_log('Banner Debug - Found subtitle in theme settings: ' . $subtitle);
                        } else {
                            error_log('Banner Debug - No td_subtitle found in theme settings');
                        }
                    } else {
                        error_log('Banner Debug - Theme settings empty or not string');
                    }
                    
                    // Debug: Log the subtitle value
                    error_log('Banner Debug - Post ID: ' . $post_id);
                    error_log('Banner Debug - td_subtitle value: ' . ($subtitle ? $subtitle : 'EMPTY'));
                    error_log('Banner Debug - Has excerpt: ' . (has_excerpt() ? 'YES' : 'NO'));
                    
                    // Get description text - subtitle first, then excerpt, then content
                    $description_text = '';
                    
                    if (!empty($subtitle)) {
                        $description_text = $subtitle;
                        error_log('Banner Debug - Using subtitle: ' . $subtitle);
                    } elseif (has_excerpt()) {
                        $description_text = get_the_excerpt();
                        error_log('Banner Debug - Using excerpt: ' . $description_text);
                    } elseif (get_the_content()) {
                        $description_text = get_the_content();
                        error_log('Banner Debug - Using content: ' . $description_text);
                    }
                    
                    error_log('Banner Debug - Final description: ' . $description_text);
                    ?>
                    
                    <!-- Description Container -->
                    <?php if ($description_text) : ?>
                        <div class="latest-post-banner-description-container">
                            <div class="latest-post-banner-description">
                                <?php echo esc_html(wp_trim_words($description_text, 30)); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    
                    
                    <!-- Date Container -->
                    <div class="latest-post-banner-date-container">
                        <div class="entry-date updated td-module-date">
                            <?php echo esc_html(get_the_date('F j, Y')); ?>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
});

// Global CSS for td-post-category styling
add_action('wp_head', function() {
    echo '<style>
        .td-post-category {
            font-weight: normal !important;
            bottom: 0px !important;
            left: 0px !important;
            font-size: 15px !important;
            font-family: Fira Sans !important;
        }
    </style>';
});

// Commentary styling to match "Auntie Spices It Out" branding
add_action('wp_head', function() {
    echo '<style>
        /* Commentary Field Styling - Auntie Spices It Out Brand */
        .post-commentary {
            margin: 25px 0;
            padding: 0;
            background: transparent;
            border: none;
            font-family: "Fira Sans", sans-serif;
            text-align: center;
        }
        
        .post-commentary .commentary-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .post-commentary .commentary-title {
            color: #a40d02;
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0 0 5px 0;
            line-height: 1.2;
        }
        
        .post-commentary .commentary-subtitle {
            color: #a40d02;
            font-size: 14px;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            opacity: 0.8;
        }
        
        .post-commentary .commentary-content {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
            font-style: italic;
            text-align: left;
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Commentary Box Styling */
        .commentary-box {
            margin: 25px 0;
            padding: 0;
            background: transparent;
            border: none;
            font-family: "Fira Sans", sans-serif;
            text-align: center;
        }
        
        .commentary-box .commentary-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .commentary-box .commentary-title {
            color: #a40d02;
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 0 0 5px 0;
            line-height: 1.2;
        }
        
        .commentary-box .commentary-subtitle {
            color: #a40d02;
            font-size: 14px;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            opacity: 0.8;
        }
        
        .commentary-box .commentary-icon {
            margin-right: 10px;
            font-size: 24px;
        }
        
        .commentary-box .commentary-content {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
            font-style: italic;
            text-align: left;
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Different Commentary Box Styles */
        .commentary-box.minimal {
            border: none;
            background: transparent;
            box-shadow: none;
            padding: 0;
        }
        
        .commentary-box.minimal .commentary-title {
            font-size: 22px;
            color: #a40d02;
        }
        
        .commentary-box.card {
            background: transparent;
            border: none;
            box-shadow: none;
            padding: 0;
        }
        
        .commentary-box.card .commentary-title {
            color: #a40d02;
            font-size: 24px;
        }
        
        .commentary-box.highlight {
            background: transparent;
            color: #333;
            border: none;
            padding: 0;
        }
        
        .commentary-box.highlight .commentary-title {
            color: #a40d02;
            font-size: 24px;
        }
        
        .commentary-box.highlight .commentary-content {
            color: #333;
            font-style: italic;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .post-commentary, .commentary-box {
                margin: 20px 0;
                padding: 15px;
            }
            
            .post-commentary strong, .commentary-box .commentary-title {
                font-size: 16px;
            }
            
            .post-commentary .commentary-content, .commentary-box .commentary-content {
                font-size: 15px;
            }
        }
    </style>';
});


/**
 * Shortcode to display post commentary
 * Usage: [post_commentary] or [post_commentary post_id="123"]
 */
function display_post_commentary_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_id' => get_the_ID(),
        'show_label' => true,
        'label_text' => 'Commentary:',
        'class' => 'post-commentary',
    ), $atts, 'post_commentary');
    
    $post_id = intval($atts['post_id']);
    $commentary = get_field('post_commentary', $post_id);
    
    if (empty($commentary)) {
        return '';
    }
    
    $label = $atts['show_label'] ? '<strong>' . esc_html($atts['label_text']) . '</strong> ' : '';
    $class = esc_attr($atts['class']);
    
    $output = '<div class="' . $class . '">';
    $output .= '<div class="commentary-header">';
    $output .= '<h2 class="commentary-title">Auntie Spices It Out</h2>';
    $output .= '<p class="commentary-subtitle">Commentary</p>';
    $output .= '</div>';
    $output .= '<div class="commentary-content">' . wp_kses_post($commentary) . '</div>';
    $output .= '</div>';
    
    return $output;
}
add_shortcode('post_commentary', 'display_post_commentary_shortcode');

/**
 * Advanced commentary shortcode with more options
 * Usage: [commentary_box] or [commentary_box style="minimal" show_icon="true"]
 */
function display_commentary_box_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_id' => get_the_ID(),
        'style' => 'default', // default, minimal, card, highlight
        'show_icon' => false,
        'show_label' => true,
        'label_text' => 'Editorial Commentary',
        'max_words' => 0, // 0 = no limit
        'class' => 'commentary-box',
    ), $atts, 'commentary_box');
    
    $post_id = intval($atts['post_id']);
    $commentary = get_field('post_commentary', $post_id);
    
    if (empty($commentary)) {
        return '';
    }
    
    // Apply word limit if specified
    if ($atts['max_words'] > 0) {
        $commentary = wp_trim_words($commentary, $atts['max_words']);
    }
    
    $label = $atts['show_label'] ? esc_html($atts['label_text']) : '';
    $icon = $atts['show_icon'] ? '<span class="commentary-icon">💭</span>' : '';
    $class = esc_attr($atts['class']) . ' ' . esc_attr($atts['style']);
    
    $output = '<div class="' . $class . '">';
    $output .= '<div class="commentary-header">';
    $output .= $icon;
    $output .= '<h2 class="commentary-title">Auntie Spices It Out</h2>';
    $output .= '<p class="commentary-subtitle">Commentary</p>';
    $output .= '</div>';
    $output .= '<div class="commentary-content">' . wp_kses_post($commentary) . '</div>';
    $output .= '</div>';
    
    return $output;
}
add_shortcode('commentary_box', 'display_commentary_box_shortcode');

/**
 * Simple commentary shortcode - just shows commentary content
 * Usage: [show_commentary] or [show_commentary post_id="123"]
 */
function show_commentary_shortcode($atts) {
    $atts = shortcode_atts(array(
        'post_id' => get_the_ID(),
        'show_icon' => true,
    ), $atts, 'show_commentary');
    
    $post_id = intval($atts['post_id']);
    $commentary = get_field('post_commentary', $post_id);
    
    if (empty($commentary)) {
        return '';
    }
    
    $output = '<div class="post-commentary-simple">';
    $output .= '<div class="commentary-header">';
    $output .= '<span class="tdm-title-s-text">Auntie Spices It Out</span>';
    $output .= '<span class="tdm-title-s-subtitle">Commentary</span>';
    $output .= '</div>';
    $output .= '<div class="commentary-content">' . wp_kses_post($commentary) . '</div>';
    $output .= '</div>';
    
    return $output;
}
add_shortcode('show_commentary', 'show_commentary_shortcode');







/**
 * Automatically append post commentary after the main content on single posts.
 * This keeps placement inside the content container and avoids duplicates if shortcode is present.
 */
add_filter('the_content', function ($content) {
    if (!is_single() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    // If user already inserted the shortcode manually, don't append again
    if (stripos($content, '[show_commentary') !== false || stripos($content, '[post_commentary') !== false) {
        return $content;
    }

    $postId = get_the_ID();
    if (!$postId) {
        return $content;
    }

    // Only append when there is commentary content
    if (function_exists('get_field')) {
        $commentary = get_field('post_commentary', $postId);
        if (!empty($commentary)) {
            // Append rendered shortcode so styling remains consistent
            $content .= "\n\n" . do_shortcode('[show_commentary]');
        }
    }

    return $content;
}, 20);


/**
 * Add CSS for commentary shortcode
 */
add_action('wp_head', function() {
    echo '<style>
        /* Simple Commentary Shortcode Styling */
        .post-commentary-simple {
            margin: 20px 0;
            padding: 0;
            text-align: center;
        }
        
        .post-commentary-simple .commentary-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .post-commentary-simple .tdm-title-s-text {
            color: #a40d02;
            font-family: "Fira Sans", sans-serif;
            font-size: 32px !important;
            font-weight: 700 !important;
            display: block;
            margin: 0;
        }
        
        .post-commentary-simple .tdm-title-s-subtitle {
            margin: 1px 0 0;
            padding-top: 8px;
            color: #a40d02;
            font-family: "Fira Sans", sans-serif;
            line-height: 1 !important;
            font-weight: 600 !important;
            text-transform: capitalize !important;
            display: block;
        }
        
        
        .post-commentary-simple .commentary-content {
            font-family: "Fira Sans", sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #333;
            font-style: normal;
            text-align: left;
            margin: 0;
            max-width: 800px;
            margin: 0 auto;
        }
    </style>';
});

// 3️⃣ Register all TagDiv Composer custom elements
add_action('tdc_init', function() {
    if (function_exists('tdc_register_elements')) {
        tdc_register_elements(array(
            array(
                'text' => 'Latest Post Banner',
                'title' => 'Latest Post Banner',
                'class' => 'tdc-latest-post-banner',
                'icon' => 'tdc-font-icon tdc-font-icon-latest-post-banner',
                'file' => TAGDIV_ROOT_DIR . '/includes/tagdiv-composer/elements/tdc-latest-post-banner.php',
                'params' => array(
                    array(
                        'param_name' => 'ratio',
                        'type' => 'textfield',
                        'value' => '1600x872',
                        'heading' => 'Aspect Ratio',
                        'description' => 'Enter aspect ratio (e.g., 1600x872)',
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
                        'value' => 'rgba(0,0,0,0.4)',
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
                'shortcode' => 'td_latest_post_banner',
            ),
        ));
    }
});

// Shortcode: [td_latest_post_banner] - TagDiv Composer wrapper
add_shortcode('td_latest_post_banner', function($atts) {
    $atts = shortcode_atts(array(
        'ratio' => '1600x872',
        'text_color' => '#ffffff',
        'overlay_color' => 'rgba(0,0,0,0.4)',
        'no_watermark' => true,
    ), $atts, 'td_latest_post_banner');

    return do_shortcode('[latest_post_banner ratio="' . esc_attr($atts['ratio']) . '" text_color="' . esc_attr($atts['text_color']) . '" overlay_color="' . esc_attr($atts['overlay_color']) . '" no_watermark="' . ($atts['no_watermark'] ? '1' : '0') . '"]');
});
