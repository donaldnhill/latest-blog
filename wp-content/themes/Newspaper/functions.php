<?php

define('TAGDIV_ROOT', get_template_directory_uri());
define('TAGDIV_ROOT_DIR', get_template_directory());

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
