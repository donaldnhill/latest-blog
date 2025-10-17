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
function register_spice_region_taxonomy() {
    register_taxonomy('spice_region', 'post', array(
        'labels' => array(
            'name' => 'Spice Regions',
            'singular_name' => 'Spice Region',
        ),
        'public' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_in_nav_menus' => true,
        'hierarchical' => true,
        'publicly_queryable' => true,
        'query_var' => true,
        'show_admin_column' => true,
        'rewrite' => array(
            'slug' => 'spice-region',
            'with_front' => false
        ),
    ));
}
add_action('init', 'register_spice_region_taxonomy');

function spice_region_single_term_shortcode($atts) {
    $atts = shortcode_atts(array(
        'term' => '', // Term slug
    ), $atts, 'spice_region_single');

    if (empty($atts['term'])) return '';

    $term = get_term_by('slug', $atts['term'], 'spice_region');
    if (!$term || is_wp_error($term)) return '';

    // Get subtitle and icon (image) from taxonomy fields
    $subtitle = get_field('subtitle', 'spice_region_' . $term->term_id);
    $icon = get_field('icon', 'spice_region_' . $term->term_id); // This should be an image array

    $output = '<div class="spice-region-single" style="text-align:center; margin-bottom:20px;">';



    $output .= '<h2 style="color:#a40d02;">' . esc_html($term->name) . '</h2>';

    if ($subtitle) {
        $output .= '<p style="color:#a40d02; font-weight:600; text-transform:capitalize;">' . esc_html($subtitle) . '</p>';
    }
	    if ($icon && isset($icon['url'])) {
        $output .= '<img src="' . esc_url($icon['url']) . '" alt="' . esc_attr($term->name) . '" style="width:70px; margin-bottom:10px;">';
    }

    $output .= '</div>';

    return $output;
}
add_shortcode('spice_region_single', 'spice_region_single_term_shortcode');

function spice_region_debug_shortcode($atts) {
    $atts = shortcode_atts(array(
        'term' => '', // Term slug
    ), $atts, 'spice_region_debug');

    if (empty($atts['term'])) return '';

    $term = get_term_by('slug', $atts['term'], 'spice_region');
    if (!$term || is_wp_error($term)) return '';

    // Get ACF fields
    $subtitle = get_field('subtitle', 'spice_region_' . $term->term_id);
    $icon = get_field('icon', 'spice_region_' . $term->term_id); // Could be image array

    // Prepare data to log
    $data = array(
        'term' => $term,
        'subtitle' => $subtitle,
        'icon' => $icon,
    );

    // Convert to JSON for console.log
    $json = json_encode($data);

    // Output to browser console
    $output = '<script>console.log("Spice Region Data:", ' . $json . ');</script>';

    return $output;
}
add_shortcode('spice_region_debug', 'spice_region_debug_shortcode');



// Shortcode: [spice_region_posts terms="lemongrass,cumin" posts_per_page="6" operator="IN"]
add_shortcode('spice_region_posts', function($atts) {
    $atts = shortcode_atts(array(
        'terms' => '',            // comma-separated slugs
        'operator' => 'IN',       // IN | AND | NOT IN
        'posts_per_page' => 3,
        'post_type' => 'post',
        'columns' => 1,           // 1-6
        'image_size' => 'full',   // thumbnail|medium|large|full or WxH like 400x300
        'ratio' => '1600x872',    // widthxheight used to set CSS aspect ratio
        'show_excerpt' => true,
        'no_watermark' => false,
        'show_taxonomy_title' => true,
    ), $atts, 'spice_region_posts');

    $termSlugs = array_filter(array_map('trim', explode(',', strtolower($atts['terms']))));
    if (empty($termSlugs)) {
        return '';
    }

    $q = new WP_Query(array(
        'post_type' => $atts['post_type'],
        'posts_per_page' => intval($atts['posts_per_page']),
        'ignore_sticky_posts' => true,
        'tax_query' => array(
            array(
                'taxonomy' => 'spice_region',
                'field' => 'slug',
                'terms' => $termSlugs,
                'operator' => in_array($atts['operator'], array('IN','AND','NOT IN'), true) ? $atts['operator'] : 'IN',
            )
        )
    ));

    if (!$q->have_posts()) {
        wp_reset_postdata();
        return '';
    }

    $columns = max(1, min(6, intval($atts['columns'])));
    $imageSize = trim($atts['image_size']);
    if (preg_match('/^\\d+x\\d+$/', $imageSize)) {
        list($w, $h) = array_map('intval', explode('x', $imageSize));
        $imageSize = array($w, $h);
    }
    $ratio = '56.25%';
    if (!empty($atts['ratio']) && preg_match('/^(\\d+)x(\\d+)$/', trim($atts['ratio']), $m)) {
        $rw = max(1, intval($m[1]));
        $rh = max(1, intval($m[2]));
        $ratio = number_format(($rh / $rw) * 100, 4, '.', '') . '%';
    }

    // header for the first term (icon next to title, centered vertically, h=50)
    $header_html = '';
    $first_term = get_term_by('slug', $termSlugs[0], 'spice_region');
    if ($first_term && !is_wp_error($first_term)) {
        $subtitle = function_exists('get_field') ? get_field('subtitle', 'spice_region_' . $first_term->term_id) : '';
        $icon = function_exists('get_field') ? get_field('icon', 'spice_region_' . $first_term->term_id) : '';
        $icon_url = is_array($icon) && !empty($icon['url']) ? $icon['url'] : '';
        $header_html = '';
        if (!empty($atts['show_taxonomy_title'])) {
            $header_html = '<div class="spice-region-header" style="display:flex;align-items:center;justify-content:center;margin:0 10px 20px;text-align:center;">'
                . '<div style="color:#a40d02;font-weight:700;font-size:36px;line-height:1;font-family:Fira Sans,sans-serif !important;">' . esc_html($first_term->name) . '</div>'
              . '</div>';
        }
    }

    ob_start();
    echo '<div class="spice-region-posts" style="margin:-10px;">' . $header_html . '
            <style>
                .spice-region-grid{display:flex;flex-wrap:wrap;margin:-10px}
                .spice-region-card{padding:10px;box-sizing:border-box}
                .spice-region-card-inner{background:#fff;border:0}
                .spice-region-thumb img{width:100%;height:auto;display:block}
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
                .spice-region-posts .td-post-category{margin:0 0 -15px 0;padding:8px 12px 7px;background-color:#ffffff;color:#000000;font-family:Fira Sans !important;font-size:15px !important;line-height:1 !important;font-weight:600 !important;text-transform:uppercase !important;letter-spacing:1px !important;text-decoration:none !important;display:inline-block !important;position:absolute;left:0px;bottom:3px;z-index:2;}
            </style>';
    echo '<div class="spice-region-grid">';
    while ($q->have_posts()) {
        $q->the_post();
        $colPct = 100 / $columns;
        echo '<div class="spice-region-card" style="width:' . esc_attr($colPct) . '%">';
            echo '<div class="spice-region-card-inner">';
                // resolve thumbnail URL; optionally use Easy Watermark backup to avoid watermark
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
                $thumb_style = $thumb_url ? ' style="background-image: url(\'' . esc_url($thumb_url) . '\');background-size:cover;background-position:center;display:block;width:100%;padding-top:' . $ratio . ';"' : '';
                echo '<div class="td-image-container" style="position:relative;">';
                    // show WP category at bottom-left
                    $cats = get_the_category();
                    if (!empty($cats)) {
                        $cat = $cats[0];
                        $cat_link = get_category_link($cat->term_id);
                        echo '<a href="' . esc_url($cat_link) . '" class="td-post-category" style="position:absolute;left:8px;bottom:8px;z-index:2;">' . esc_html($cat->name) . '</a>';
                    }
                    echo '<div class="td-module-thumb">'
                        . '<a href="' . esc_url(get_permalink()) . '" rel="bookmark" class="td-image-wrap" title="' . esc_attr(get_the_title()) . '">'
                        . '<span class="entry-thumb td-thumb-css"' . $thumb_style . '></span>'
                        . '</a>'
                    . '</div>';
                echo '</div>';
                echo '<h3 class="entry-title td-module-title"><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h3>';
                if (!empty($atts['show_excerpt'])) {
                    echo '<div class="td-excerpt">' . esc_html(wp_trim_words(get_the_excerpt(), 28)) . '</div>';
                }
            echo '</div>';
        echo '</div>';
    }
    echo '</div></div>';
    wp_reset_postdata();

    return ob_get_clean();
});

// Shortcode: [spice_region_current_posts] - auto-detect current spice_region term and list latest 3 posts
add_shortcode('spice_region_current_posts', function($atts) {
    $atts = shortcode_atts(array(
        'posts_per_page' => 3,
        'columns' => 1,
        'image_size' => 'medium',
        'show_excerpt' => true,
        'post_type' => 'post',
    ), $atts, 'spice_region_current_posts');

    // detect current term slug from archive or single
    $termSlugs = array();
    if (is_tax('spice_region')) {
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            $termSlugs[] = $term->slug;
        }
    } else if (is_single()) {
        $terms = wp_get_post_terms(get_the_ID(), 'spice_region');
        if (!is_wp_error($terms) && !empty($terms)) {
            $termSlugs[] = $terms[0]->slug; // first term
        }
    }

    if (empty($termSlugs)) {
        return '';
    }

    $q = new WP_Query(array(
        'post_type' => $atts['post_type'],
        'posts_per_page' => intval($atts['posts_per_page']),
        'ignore_sticky_posts' => true,
        'tax_query' => array(
            array(
                'taxonomy' => 'spice_region',
                'field' => 'slug',
                'terms' => $termSlugs,
                'operator' => 'IN',
            )
        )
    ));

    if (!$q->have_posts()) {
        wp_reset_postdata();
        return '';
    }

    $columns = max(1, min(6, intval($atts['columns'])));
    $imageSize = trim($atts['image_size']);
    if (preg_match('/^\\d+x\\d+$/', $imageSize)) {
        list($w, $h) = array_map('intval', explode('x', $imageSize));
        $imageSize = array($w, $h);
    }

    ob_start();
    echo '<div class="spice-region-posts" style="margin:-10px;">
            <style>
                .spice-region-grid{display:flex;flex-wrap:wrap;margin:-10px}
                .spice-region-card{padding:10px;box-sizing:border-box}
                .spice-region-card-inner{background:#fff;border:0}
                .spice-region-thumb img{width:100%;height:auto;display:block}
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
                .spice-region-posts .td-post-category{margin:0 0 -15px 0;padding:8px 12px 7px;background-color:#ffffff;color:#000000;font-family:Fira Sans !important;font-size:15px !important;line-height:1 !important;font-weight:600 !important;text-transform:uppercase !important;letter-spacing:1px !important;text-decoration:none !important;display:inline-block !important;position:absolute;left:0px;bottom:3px;z-index:2;}
            </style>';
    echo '<div class="spice-region-grid">';
    while ($q->have_posts()) {
        $q->the_post();
        $colPct = 100 / $columns;
        echo '<div class="spice-region-card" style="width:' . esc_attr($colPct) . '%">';
            echo '<div class="spice-region-card-inner">';
                $thumb_url = get_the_post_thumbnail_url(get_the_ID(), $imageSize);
                $thumb_style = $thumb_url ? ' style="background-image: url(\'' . esc_url($thumb_url) . '\')"' : '';
                $sp_terms = wp_get_post_terms(get_the_ID(), 'spice_region');
                echo '<div class="td-image-container" style="position:relative;">';
                    if (!is_wp_error($sp_terms) && !empty($sp_terms)) {
                        $sp = $sp_terms[0];
                        $sp_link = get_term_link($sp);
                        echo '<a href="' . esc_url($sp_link) . '" class="td-post-category" style="position:absolute;left:8px;top:8px;z-index:2;">' . esc_html($sp->name) . '</a>';
                    }
                    echo '<div class="td-module-thumb">'
                        . '<a href="' . esc_url(get_permalink()) . '" rel="bookmark" class="td-image-wrap" title="' . esc_attr(get_the_title()) . '">'
                        . '<span class="entry-thumb td-thumb-css"' . $thumb_style . '></span>'
                        . '</a>'
                    . '</div>';
                echo '</div>';
                echo '<h3 class="entry-title td-module-title"><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h3>';
                if (!empty($atts['show_excerpt'])) {
                    echo '<div class="td-excerpt">' . esc_html(wp_trim_words(get_the_excerpt(), 28)) . '</div>';
                }
            echo '</div>';
        echo '</div>';
    }
    echo '</div></div>';
    wp_reset_postdata();

    return ob_get_clean();
});

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
            .latest-post-banner-content {
                max-width: 600px;
                width: 100%;
                text-align: center;
            }
            @media (max-width: 768px) {
                .latest-post-banner-content {
                    max-width: 100%;
                    padding: 0 10px;
                }
            }
            .latest-post-banner-title {
                font-family: Merriweather, serif !important;
                font-weight: 700;
                font-size: 36px;
                line-height: 1.2;
                color: <?php echo esc_attr($atts['text_color']); ?>;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
                margin: 0 0 10px 0;
                display: block;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
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
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                justify-content: center;
                margin-bottom: 15px;
            }
            .latest-post-banner .tdb-entry-category {
                padding: 5px 6px 3px;
                font-family: Fira Sans !important;
                font-size: 11px !important;
                line-height: 1 !important;
                font-weight: 400 !important;
                text-transform: uppercase !important;
                letter-spacing: 1px !important;
                color: <?php echo esc_attr($atts['text_color']); ?> !important;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
                text-decoration: none;
                background: rgba(255, 255, 255, 0.2);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 4px;
                display: inline-block;
                margin: 0 5px 5px 0;
                white-space: nowrap;
                position: relative;
                vertical-align: middle;
            }
            .latest-post-banner .entry-date {
                font-family: Fira Sans !important;
                font-weight: 400;
                font-size: 12px !important;
                color: <?php echo esc_attr($atts['text_color']); ?> !important;
                text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
                margin-top: 15px;
                text-align: center;
            }
            .latest-post-banner-support-btn {
                position: absolute;
                top: 20px;
                right: 20px;
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
                .latest-post-banner-title { font-size: 28px; }
                .latest-post-banner-description { font-size: 16px; }
                .latest-post-banner-support-btn {
                    top: 15px;
                    right: 15px;
                    padding: 10px 16px;
                    font-size: 13px;
                }
            }
        </style>
        <div class="latest-post-banner">
            <a href="https://nowpayments.io/donation/spicyauntie" target="_blank" class="latest-post-banner-support-btn">Support</a>
            <div class="latest-post-banner-overlay">
                <div class="latest-post-banner-content">
                    <?php
                    // Get post categories
                    $categories = get_the_category();
                    if (!empty($categories)) : ?>
                        <div class="tdb-category td-fix-index">
                            <?php foreach ($categories as $category) : ?>
                                <a class="tdb-entry-category" href="<?php echo esc_url(get_category_link($category->term_id)); ?>">
                                    <span class="tdb-cat-bg"></span><?php echo esc_html($category->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <a href="<?php echo esc_url(get_permalink()); ?>" rel="bookmark" title="<?php echo esc_attr(get_the_title()); ?>">
                        <h2 class="latest-post-banner-title"><?php echo esc_html(get_the_title()); ?></h2>
                    </a>
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
                    
                    if ($description_text) : ?>
                        <div class="latest-post-banner-description">
                            <?php echo esc_html(wp_trim_words($description_text, 30)); ?>
                        </div>
                    <?php endif; ?>
                    
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
    error_log('TagDiv Composer: tdc_init hook fired');
    
    if (!function_exists('tdc_add_custom_element')) {
        error_log('TagDiv Composer: tdc_add_custom_element function not available');
        return;
    }
    
    error_log('TagDiv Composer: Starting to register custom elements');

    // 1. Spice Region Posts (filtered by terms)
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
                'description' => 'Use original images from backup',
                'default' => ''
            )
        ),
    ));

    // 2. Current Spice Region Posts (auto-detect)
    tdc_add_custom_element(array(
        'name' => 'Current Spice Region Posts',
        'shortcode' => 'td_spice_region_current',
        'icon' => 'dashicons-location-alt',
        'params' => array(
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
                'description' => 'Use original images from backup',
                'default' => ''
            )
        ),
    ));

    // 3. Latest Post Banner
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
                'type' => 'textfield',
                'label' => 'Overlay Color',
                'description' => 'CSS rgba value (e.g., rgba(0,0,0,0.4))',
                'default' => 'rgba(0,0,0,0.4)'
            ),
            array(
                'name' => 'no_watermark',
                'type' => 'checkbox',
                'label' => 'No Watermark',
                'description' => 'Use original images from backup',
                'default' => ''
            )
        ),
    ));

    // 4. Spice Region Single Card (original)
    tdc_add_custom_element(array(
        'name' => 'Spice Region Card',
        'shortcode' => 'td_spice_region_card',
        'icon' => 'dashicons-location',
        'params' => array(
            array(
                'name' => 'term',
                'type' => 'dropdown',
                'label' => 'Spice Region',
                'options' => function() {
                    $terms = get_terms(array(
                        'taxonomy' => 'spice_region',
                        'hide_empty' => false
                    ));
                    $list = array();
                    foreach ($terms as $term) {
                        $list[$term->slug] = $term->name;
                    }
                    return $list;
                },
                'default' => ''
            ),
        ),
    ));

});

// Multiple registration attempts
add_action('wp_loaded', function() {
    error_log('TagDiv Composer: wp_loaded hook fired');
    if (function_exists('tdc_add_custom_element')) {
        error_log('TagDiv Composer: tdc_add_custom_element available in wp_loaded');
        // Re-register if tdc_init didn't work
        tdc_add_custom_element(array(
            'name' => 'Spice Region Posts (Alt)',
            'shortcode' => 'td_spice_region_posts_alt',
            'icon' => 'dashicons-location',
            'params' => array(
                array(
                    'name' => 'terms',
                    'type' => 'textfield',
                    'label' => 'Spice Region Terms',
                    'description' => 'Comma-separated term slugs (e.g., lemongrass,cumin)',
                    'default' => ''
                ),
            ),
        ));
    }
});

// Try admin_init as well
add_action('admin_init', function() {
    error_log('TagDiv Composer: admin_init hook fired');
    if (function_exists('tdc_add_custom_element')) {
        error_log('TagDiv Composer: tdc_add_custom_element available in admin_init');
        tdc_add_custom_element(array(
            'name' => 'Spice Region Posts (Admin)',
            'shortcode' => 'td_spice_region_posts_admin',
            'icon' => 'dashicons-location',
            'params' => array(
                array(
                    'name' => 'terms',
                    'type' => 'textfield',
                    'label' => 'Spice Region Terms',
                    'description' => 'Comma-separated term slugs (e.g., lemongrass,cumin)',
                    'default' => ''
                ),
            ),
        ));
    }
});

// Try init hook
add_action('init', function() {
    error_log('TagDiv Composer: init hook fired');
    if (function_exists('tdc_add_custom_element')) {
        error_log('TagDiv Composer: tdc_add_custom_element available in init');
        tdc_add_custom_element(array(
            'name' => 'Spice Region Posts (Init)',
            'shortcode' => 'td_spice_region_posts_init',
            'icon' => 'dashicons-location',
            'params' => array(
                array(
                    'name' => 'terms',
                    'type' => 'textfield',
                    'label' => 'Spice Region Terms',
                    'description' => 'Comma-separated term slugs (e.g., lemongrass,cumin)',
                    'default' => ''
                ),
            ),
        ));
    } else {
        error_log('TagDiv Composer: tdc_add_custom_element NOT available in init');
    }
});

// Try using WordPress admin_init hook
add_action('admin_init', function() {
    error_log('TagDiv Composer: admin_init hook fired');
    if (function_exists('tdc_add_custom_element')) {
        error_log('TagDiv Composer: tdc_add_custom_element available in admin_init');
        tdc_add_custom_element(array(
            'name' => 'Spice Region Posts (Admin)',
            'shortcode' => 'td_spice_region_posts_admin',
            'icon' => 'dashicons-location',
            'params' => array(
                array(
                    'name' => 'terms',
                    'type' => 'textfield',
                    'label' => 'Spice Region Terms',
                    'description' => 'Comma-separated term slugs (e.g., lemongrass,cumin)',
                    'default' => ''
                ),
            ),
        ));
    } else {
        error_log('TagDiv Composer: tdc_add_custom_element NOT available in admin_init');
    }
});

// Try using WordPress admin_menu hook
add_action('admin_menu', function() {
    error_log('TagDiv Composer: admin_menu hook fired');
    if (function_exists('tdc_add_custom_element')) {
        error_log('TagDiv Composer: tdc_add_custom_element available in admin_menu');
        tdc_add_custom_element(array(
            'name' => 'Spice Region Posts (Menu)',
            'shortcode' => 'td_spice_region_posts_menu',
            'icon' => 'dashicons-location',
            'params' => array(
                array(
                    'name' => 'terms',
                    'type' => 'textfield',
                    'label' => 'Spice Region Terms',
                    'description' => 'Comma-separated term slugs (e.g., lemongrass,cumin)',
                    'default' => ''
                ),
            ),
        ));
    } else {
        error_log('TagDiv Composer: tdc_add_custom_element NOT available in admin_menu');
    }
});

// Try TagDiv's native method
add_action('wp_loaded', function() {
    if (class_exists('tdc_util')) {
        error_log('TagDiv Composer: tdc_util class available');
        // Try using TagDiv's internal method
        if (method_exists('tdc_util', 'add_custom_element')) {
            error_log('TagDiv Composer: tdc_util::add_custom_element method available');
        }
    }
    
    // Try using TagDiv's internal registration
    if (class_exists('tdc_util')) {
        error_log('TagDiv Composer: Trying tdc_util registration');
        try {
            // Try to call the method directly
            if (method_exists('tdc_util', 'add_custom_element')) {
                tdc_util::add_custom_element(array(
                    'name' => 'Spice Region Posts (Util)',
                    'shortcode' => 'td_spice_region_posts_util',
                    'icon' => 'dashicons-location',
                    'params' => array(
                        array(
                            'name' => 'terms',
                            'type' => 'textfield',
                            'label' => 'Spice Region Terms',
                            'description' => 'Comma-separated term slugs (e.g., lemongrass,cumin)',
                            'default' => ''
                        ),
                    ),
                ));
                error_log('TagDiv Composer: tdc_util registration successful');
            }
        } catch (Exception $e) {
            error_log('TagDiv Composer: tdc_util registration failed - ' . $e->getMessage());
        }
    }
    
    // Try direct registration without function check
    if (function_exists('tdc_add_custom_element')) {
        error_log('TagDiv Composer: Direct registration attempt');
        try {
            tdc_add_custom_element(array(
                'name' => 'Spice Region Posts (Direct)',
                'shortcode' => 'td_spice_region_posts_direct',
                'icon' => 'dashicons-location',
                'params' => array(
                    array(
                        'name' => 'terms',
                        'type' => 'textfield',
                        'label' => 'Spice Region Terms',
                        'description' => 'Comma-separated term slugs (e.g., lemongrass,cumin)',
                        'default' => ''
                    ),
                ),
            ));
            error_log('TagDiv Composer: Direct registration successful');
        } catch (Exception $e) {
            error_log('TagDiv Composer: Direct registration failed - ' . $e->getMessage());
        }
    }
});

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

add_shortcode('td_spice_region_current', function($atts) {
    $atts = shortcode_atts(array(
        'posts_per_page' => 3,
        'columns' => 1,
        'ratio' => '1600x872',
        'no_watermark' => false,
    ), $atts, 'td_spice_region_current');
    
    return do_shortcode('[spice_region_current_posts posts_per_page="' . esc_attr($atts['posts_per_page']) . '" columns="' . esc_attr($atts['columns']) . '" ratio="' . esc_attr($atts['ratio']) . '" no_watermark="' . ($atts['no_watermark'] ? '1' : '0') . '"]');
});

add_shortcode('td_latest_post_banner', function($atts) {
    $atts = shortcode_atts(array(
        'ratio' => '1600x872',
        'text_color' => '#ffffff',
        'overlay_color' => 'rgba(0,0,0,0.4)',
        'no_watermark' => true,
    ), $atts, 'td_latest_post_banner');
    
    return do_shortcode('[latest_post_banner ratio="' . esc_attr($atts['ratio']) . '" text_color="' . esc_attr($atts['text_color']) . '" overlay_color="' . esc_attr($atts['overlay_color']) . '" no_watermark="' . ($atts['no_watermark'] ? '1' : '0') . '"]');
});

add_shortcode('td_spice_region_card', function($atts) {
    $atts = shortcode_atts(array(
        'term' => '',
    ), $atts, 'td_spice_region_card');
    
    return do_shortcode('[spice_region_single term="' . esc_attr($atts['term']) . '"]');
});

add_shortcode('td_spice_region_posts_alt', function($atts) {
    $atts = shortcode_atts(array(
        'terms' => '',
    ), $atts, 'td_spice_region_posts_alt');
    
    return do_shortcode('[spice_region_posts terms="' . esc_attr($atts['terms']) . '"]');
});

add_shortcode('td_spice_region_posts_admin', function($atts) {
    $atts = shortcode_atts(array(
        'terms' => '',
    ), $atts, 'td_spice_region_posts_admin');
    
    return do_shortcode('[spice_region_posts terms="' . esc_attr($atts['terms']) . '"]');
});

add_shortcode('td_spice_region_posts_init', function($atts) {
    $atts = shortcode_atts(array(
        'terms' => '',
    ), $atts, 'td_spice_region_posts_init');
    
    return do_shortcode('[spice_region_posts terms="' . esc_attr($atts['terms']) . '"]');
});

add_shortcode('td_spice_region_posts_direct', function($atts) {
    $atts = shortcode_atts(array(
        'terms' => '',
    ), $atts, 'td_spice_region_posts_direct');
    
    return do_shortcode('[spice_region_posts terms="' . esc_attr($atts['terms']) . '"]');
});

add_shortcode('td_spice_region_posts_util', function($atts) {
    $atts = shortcode_atts(array(
        'terms' => '',
    ), $atts, 'td_spice_region_posts_util');
    
    return do_shortcode('[spice_region_posts terms="' . esc_attr($atts['terms']) . '"]');
});

add_shortcode('td_spice_region_posts_menu', function($atts) {
    $atts = shortcode_atts(array(
        'terms' => '',
    ), $atts, 'td_spice_region_posts_menu');
    
    return do_shortcode('[spice_region_posts terms="' . esc_attr($atts['terms']) . '"]');
});

//==============================
/* ----------------------------------------------------------------------------
 * Add theme support for sidebar
 */
add_action( 'widgets_init', function() {
    register_sidebar(
        array(
            'name'=> 'Newspaper default',
            'id' => 'td-default',
            'before_widget' => '<aside id="%1$s" class="widget %1$s %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<div class="block-title"><span>',
            'after_title' => '</span></div>'
        )
    );
});

add_filter( 'pre_handle_404', 'tagdiv_pre_handle_404', 10, 2);
function tagdiv_pre_handle_404( $param1, $param2 ) {

    global $_SERVER;

    $req_scheme = is_ssl() ? 'https' : 'http';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $post_id = url_to_postid($req_scheme . '://' . $host . $uri);

    if ( defined('TD_COMPOSER') && !empty($post_id) ) {
        $td_post_theme_settings = td_util::get_post_meta_array($post_id, 'td_post_theme_settings');
        $td_default_site_post_template = td_util::get_option('td_default_site_post_template');

        $is_smart_list_template = false;

        // Check if smart list template is specified in post theme settings
        if (is_array($td_post_theme_settings) && array_key_exists('smart_list_template', $td_post_theme_settings)) {
            $is_smart_list_template = true;
        } elseif (td_global::is_tdb_registered()) {
            // Check if smart list template is used individually or global post template
            $template_to_check = (!empty($td_post_theme_settings['td_post_template'])) ? $td_post_theme_settings['td_post_template'] : $td_default_site_post_template;

            if (!empty($template_to_check) && td_global::is_tdb_template($template_to_check, true)) {
                $td_template_id = td_global::tdb_get_template_id($template_to_check);
                $td_template_content = get_post_field('post_content', $td_template_id);
                $is_tdb_smartlist = tdb_util::get_shortcode($td_template_content, 'tdb_single_smartlist');

                if ($is_tdb_smartlist) {
                    $is_smart_list_template = true;
                }
            }
        }

        return $is_smart_list_template;

    }
    return $param1;
}



/**
 * Theme setup.
 */
add_action( 'after_setup_theme', function () {

	/**
	 * Loads the theme's translated strings.
	 */
	load_theme_textdomain( strtolower(TD_THEME_NAME ), get_template_directory() . '/translation' );

	/**
	 * Theme menu location.
	 */
	register_nav_menus(
		array(
			'header-menu' => 'Header Menu (main)',
			'footer-menu' => 'Footer Menu',
		)
	);
});


/* ----------------------------------------------------------------------------
 * Add theme support for features
 */
add_theme_support('title-tag');
add_theme_support('post-thumbnails');
add_theme_support('automatic-feed-links');
add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption'));
add_theme_support('woocommerce');
add_theme_support('bbpress');
add_theme_support('align-wide');
add_theme_support('align-full');


global $content_width;
if ( !isset($content_width) ) {
    $content_width = 696;
}



/* ----------------------------------------------------------------------------
 * Woo Commerce
 */
// breadcrumb
add_filter('woocommerce_breadcrumb_defaults', 'tagdiv_woocommerce_breadcrumbs');
function tagdiv_woocommerce_breadcrumbs() {
    return array(
        'delimiter' => ' <i class="td-icon-right td-bread-sep"></i> ',
        'wrap_before' => '<div class="entry-crumbs" itemprop="breadcrumb">',
        'wrap_after' => '</div>',
        'before' => '',
        'after' => '',
        'home' => _x('Home', 'breadcrumb', 'newspaper'),
    );
}



/* ----------------------------------------------------------------------------
 * SEOPress
 */
// Fix custom title meta tags from not applying
if( defined( 'SEOPRESS_VERSION' ) ) {

    add_action( 'after_setup_theme', 'tagdiv_seopress_fix_title_meta', 99999999999999 );
    function tagdiv_seopress_fix_title_meta() {
        add_theme_support( 'title-tag' );
        remove_all_filters( 'wp_title' );
        remove_all_filters( 'wpseo_title' );
        remove_all_actions( 'wp_head', 'theme_slug_render_title' );
        add_filter( 'wp_title', 'tagdiv_seopress_remove_title', 9999999999999, 2 );
    }
    function tagdiv_seopress_remove_title( $title, $sep ) {
        return false;
    }

    add_action( 'wp_loaded', 'tagdiv_seopress_buffer_start' );
    function tagdiv_seopress_buffer_start() {
        ob_start( 'tagdiv_seopress_remove_empty_title' );
    }
    function tagdiv_seopress_remove_empty_title( $buffer ) {
        return str_replace( '<title></title>', '', $buffer );
    }

}



/* ----------------------------------------------------------------------------
* front end css files
*/
if( !function_exists('tagdiv_theme_css') ) {
    function tagdiv_theme_css() {
        wp_enqueue_style('td-theme', get_stylesheet_uri() );

        // load the WooCommerce CSS only when needed
        if ( class_exists('WooCommerce', false) ) {
            wp_enqueue_style('td-theme-woo', get_template_directory_uri() . '/style-woocommerce.css' );
        }

        // load the Bbpress CSS only when needed
        if ( class_exists('bbPress', false) ) {
            wp_enqueue_style('td-theme-bbpress', get_template_directory_uri() . '/style-bbpress.css' );
        }
    }
}
add_action('wp_enqueue_scripts', 'tagdiv_theme_css', 1001);



/* ----------------------------------------------------------------------------
* dequeue the front end gutenberg block library css files
*/
if( !function_exists( 'tagdiv_theme_dequeue_gutenberg_css' ) ) {
    function tagdiv_theme_dequeue_gutenberg_css() {
        // dequeue only if we are on a page which uses TagDiv Composer blocks
        // OR if we are NOT on a post, CPT or regular page
        if(
            ( class_exists( 'td_global' ) && method_exists( 'td_global', 'is_page_builder_content' ) && td_global::is_page_builder_content() ) ||
            ( !( is_single() && !is_attachment() && get_post_type() != 'product' ) && !is_page() )
        ) {
            wp_dequeue_style( 'wp-block-library' );
            wp_dequeue_style( 'wp-block-library-theme' );
            wp_dequeue_style( 'wc-blocks-style' );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'tagdiv_theme_dequeue_gutenberg_css', 100 );

add_filter('upgrader_clear_destination', function($removed, $local_destination, $remote_destination, $args) {
    usleep(500);
    return $removed;
}, 10, 4);


if ( defined('TD_COMPOSER' ) ) {

    if ( is_admin() ) {

        $user = get_userdata( get_current_user_id() );
		if ( $user instanceof WP_User && in_array( 'administrator', (array) $user->roles ) ) {

		    $value = get_transient( 'td_update_theme_' . TD_THEME_NAME );
			if ( false === $value ) {

				tagdiv_check_theme_version();

			} else {

				$td_theme_update_to_version = get_transient( 'td_update_theme_to_version_' . TD_THEME_NAME );
				if ( false !== $td_theme_update_to_version ) {
					$theme_update_to_version = tagdiv_util::get_option( 'theme_update_to_version' );

					if ( ! empty( $theme_update_to_version ) ) {

						add_filter( 'pre_set_site_transient_update_themes', function ( $transient ) {

							$to_version = tagdiv_util::get_option( 'theme_update_to_version' );
							if ( ! empty( $to_version ) ) {
								$args            = array();
								$to_version      = json_decode( $to_version, true );
								$to_version_keys = array_keys( $to_version );
								if ( is_array( $to_version_keys ) && count( $to_version_keys ) ) {
									$to_version_serial = $to_version_keys[ 0 ];
									$to_version_url    = $to_version[ $to_version_serial ];
									$theme_slug        = get_template();

									$transient->response[ $theme_slug ] = array(
										'theme'             => $theme_slug,
										'new_version'       => $to_version_serial,
										'url'               => "https://tagdiv.com/" . TD_THEME_NAME,
										'clear_destination' => true,
										'package'           => add_query_arg( $args, $to_version_url ),
									);
								}
							}

							return $transient;
						} );
						delete_site_transient( 'update_themes' );
					}
				} else {

					$td_theme_update_latest_version = get_transient( 'td_update_theme_latest_version_' . TD_THEME_NAME );

					if ( false !== $td_theme_update_latest_version ) {

						add_filter( 'pre_set_site_transient_update_themes', function ( $transient ) {

							$latest_version = tagdiv_util::get_option( 'theme_update_latest_version' );
							if ( ! empty( $latest_version ) ) {
								$args           = array();
								$latest_version = json_decode( $latest_version, true );

								$latest_version_keys = array_keys( $latest_version );
								if ( is_array( $latest_version_keys ) && count( $latest_version_keys ) ) {
									$latest_version_serial = $latest_version_keys[ 0 ];
									$latest_version_url    = $latest_version[ $latest_version_serial ];
									$theme_slug            = get_template();

									$transient->response[ $theme_slug ] = array(
										'theme'             => $theme_slug,
										'new_version'       => $latest_version_serial,
										'url'               => "https://tagdiv.com/" . TD_THEME_NAME,
										'clear_destination' => true,
										'package'           => add_query_arg( $args, $latest_version_url ),
									);
								}
							}

							return $transient;
						} );
						delete_site_transient( 'update_themes' );
					}
				}
			}

			if ( is_plugin_active('td-subscription/td-subscription.php') && defined('TD_SUBSCRIPTION_VERSION') ) {

				$transient_plugin_subscription = get_transient( 'td_update_plugin_subscription' );
				if ( false === $transient_plugin_subscription ) {

					tagdiv_check_plugin_subscription_version();

				} else {

                    $transient_update_plugin_subscription_latest_version = get_transient( 'td_update_plugin_subscription_latest_version' );

                    if ( false !== $transient_update_plugin_subscription_latest_version ) {

                        $latest_version = tagdiv_util::get_option('plugin_subscription_update_latest_version');
                        $latest_version = json_decode($latest_version, true);

                        if ( !empty($latest_version) && is_array($latest_version) && count($latest_version) ) {
                            $latest_version_keys = array_keys($latest_version);
                            if ( is_array($latest_version_keys) && count($latest_version_keys) ) {
                                $latest_version_serial = $latest_version_keys[0];

                                if ( 1 == version_compare($latest_version_serial, TD_SUBSCRIPTION_VERSION) ) {

                                    add_filter('pre_set_site_transient_update_plugins', function ($transient) {
                                        $latest_version = tagdiv_util::get_option('plugin_subscription_update_latest_version');
                                        $latest_version = json_decode($latest_version, true);

                                        if ( !empty($latest_version) ) {
                                            $args = array();
                                            $latest_version_keys = array_keys($latest_version);
                                            $latest_version_serial = $latest_version_keys[0];
                                            $latest_version_url = $latest_version[$latest_version_serial];
                                            $plugin_id = 'td-subscription/td-subscription.php';

                                            $transient->response[$plugin_id] = (object)array(
                                                'id' => $plugin_id,
                                                'slug' => 'td-subscription',
                                                'plugin' => $plugin_id,
                                                'new_version' => $latest_version_serial,
                                                'url' => "https://tagdiv.com/td_subscription",
                                                'package' => add_query_arg($args, $latest_version_url),
                                            );
                                        }


                                        return $transient;
                                    });

                                    delete_site_transient('update_plugins');
                                }
                            }
                        }

                    }
                }
			}
		}
    }


    add_filter( 'admin_body_class', function ( $classes ) {

        // Check for Theme updates
		$new_update_available = false;
        $latest_version = tagdiv_util::get_option( 'theme_update_latest_version' );

        if ( ! empty( $latest_version ) ) {
            $latest_version = json_decode( $latest_version, true );

            $latest_version_keys = array_keys( $latest_version );
            if ( is_array( $latest_version_keys ) && count( $latest_version_keys ) ) {
                $latest_version_serial = $latest_version_keys[ 0 ];

                if ( 1 == version_compare( $latest_version_serial, TD_THEME_VERSION ) ) {
                    $new_update_available = true;
                }
            }
        }

        if ( $new_update_available ) {
            $classes .= ' td-theme-update';
        }

        // Check for Plugin updates
		wp_update_plugins();
		$plugin_updates = get_site_transient( 'update_plugins' );

		foreach ( tagdiv_global::get_td_plugins() as $constant => $settings ) {
		    $plugin_id = 'td-subscription/td-subscription.php';
		    $plugin_name = strtolower( str_replace('_', '-', $constant ) );
			$plugin = $plugin_name . '/' . $plugin_name . '.php';
			if ( $plugin === $plugin_id && !empty($plugin_updates->response[$plugin_id]) ) {
				$classes .= ' td-subscription-plugin-update';
                break;
			}
		}

		return $classes;
	} );


	add_filter( 'admin_head', function () {

		$td_update_theme_ready = get_transient( 'td_update_theme_' . TD_THEME_NAME );
		if ( false !== $td_update_theme_ready ) {

            $td_checked_licence = get_transient( 'TD_CHECKED_LICENSE' );

            $update_data = '';

			$td_theme_update_to_version = get_transient( 'td_update_theme_to_version_' . TD_THEME_NAME );
			if ( false !== $td_theme_update_to_version ) {

				$data = tagdiv_util::get_option( 'theme_update_to_version' );
				if ( ! empty( $data ) ) {
					$update_data = $data;
				}
			} else {

				$data = tagdiv_util::get_option( 'theme_update_latest_version' );
				if ( ! empty( $data ) ) {
					$update_data = $data;
				}
			}

			if ( ! empty( $update_data ) ) {
				ob_start();
				?>
                <script>
                    var tdData = {
                        version: <?php echo '' . $update_data ?>,
                        adminUrl: "<?php echo admin_url() ?>",
                        themeName: "<?php echo TD_THEME_NAME ?>",
                        checkedLicence: "<?php echo $td_checked_licence ?>",
                    };
                </script>
				<?php
				echo ob_get_clean();
			}
		}
	} );
}

// Load comments reply support if needed
add_action( 'comment_form_before', function() {
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
});

//add_action( 'admin_footer', 'tdc_error_report' );
function tdc_error_report() {
    ?>
    <iframe id="iframe-reports" src="http://report.tagdiv.com/?name=form_report" style="width:0px;height:0px;display:none"></iframe>
    <?php
}


add_action('upgrader_process_complete', function($upgrader, $data) {

    if ($data['action'] == 'update' && $data['type'] == 'theme' ) {

         // clear flag to update theme
        delete_transient( 'td_update_theme_' . TD_THEME_NAME );

        // clear flag to update theme to latest version
        delete_transient( 'td_update_theme_latest_version_' . TD_THEME_NAME );

        // clear flag to update theme to specific version
        delete_transient( 'td_update_theme_to_version_' . TD_THEME_NAME );

        // clear flag to update to a specific version
        tagdiv_util::update_option( 'theme_update_to_version', '' );

        $current_deactivated_plugins = tagdiv_options::get_array('td_theme_deactivated_current_plugins' );

        if ( ! empty( $current_deactivated_plugins ) ) {
            $theme_setup = tagdiv_theme_plugins_setup::get_instance();
            $theme_setup->theme_plugins( array_keys( $current_deactivated_plugins ) );

            ob_start();

            ?>

            <script>
                window.onload = (event) =>
                {
                    setTimeout(function () {
                        jQuery('.td-button-install-plugins').trigger('click');
                    }, 1000);
                }
            </script>

            <?php

            echo ob_get_clean();
        }
    }

}, 10, 2);


add_action('upgrader_process_complete', function($upgrader, $data) {

    if ($data['action'] == 'update' && $data['type'] == 'plugin' &&
        (( !empty($data['plugins']) && in_array('td-subscription/td-subscription.php', $data['plugins'])) || (!empty($data['plugin']) && 'td-subscription/td-subscription.php' === $data['plugin'])) )  {

        // clear flag to update theme to latest version
        delete_transient( 'td_update_plugin_subscription' );

        // clear flag to update theme to specific version
        delete_transient( 'td_update_plugin_subscription_latest_version');
    }

}, 10, 2);

add_filter('upgrader_pre_install', function( $return, $theme) {
    if ( is_wp_error( $return ) ) {
        return $return;
    }

    $theme = isset( $theme['theme'] ) ? $theme['theme'] : '';

    if ( ! in_array( get_stylesheet(), array($theme, $theme . '-child'))) { //If not current
        return $return;
    }

    tagdiv_options::update_array( 'td_theme_deactivated_current_plugins', '' );
    $deactivation = new tagdiv_current_plugins_deactivation();
    $deactivation->td_deactivate_current_plugins( true );

    return $return;

}, 10, 2);



add_action( 'current_screen', function() {
    $current_screen = get_current_screen();

    if ( 'update-core' === $current_screen->id && isset( $_REQUEST['update_theme'] )) {

        add_action('admin_head', function() {

            $theme_name = $_REQUEST['update_theme'];

            ob_start();
            ?>

            <script>
                jQuery(window).ready(function() {

                    'use strict';

                    var $formUpgradeThemes = jQuery('form[name="upgrade-themes"]');
                    if ( $formUpgradeThemes.length ) {
                        var $input = $formUpgradeThemes.find('input[type="checkbox"][value="<?php echo esc_js( $theme_name ) ?>"]');
                        if ($input.length) {
                            $input.attr( 'checked', true );
                            $formUpgradeThemes.submit();
                        }
                    }
                });
            </script>

            <?php
            echo ob_get_clean();
        });
    }
});

add_filter( 'render_block', 'td_filter_youtube_embed', 10, 3);
function td_filter_youtube_embed( $block_content, $block ) {

    if( 'core-embed/youtube' !== $block['blockName'] ) {
        return $block_content;
    }

    $matches = array();
    preg_match('/iframe(.*)src=\"(\S*)\"/', $block_content, $matches);

    if ( !empty($matches) && is_array($matches) && 3 === count($matches)) {
        $url = $matches[2];
        if (strpos($url, '?') > 0 ) {
            $new_url = $url . '&enablejsapi=1';
        } else {
            $new_url = $url . '?enablejsapi=1';
        }
        $block_content = str_replace( $url, $new_url, $block_content);
    }

  return $block_content;
}


add_filter('the_content', 'td_remove_inactive_shortcodes');
// post_content is used for header content
add_filter('post_content', 'td_remove_inactive_shortcodes');

/**
 * Function to remove inactive shortcodes from content
 *
 * @param string $content The content of the post.
 * @return string Modified content with inactive shortcodes removed.
 */
function td_remove_inactive_shortcodes($content) {

    // Return the content unmodified for administrators
    if (current_user_can('administrator')) {
        return $content;
    }

    $header_content_filtered = array();

    // Check if the content is base64 encoded (header content)
    if ( tagdiv_util::is_base64($content) ) {
        $header_content_arr = json_decode(base64_decode($content), true);

        if ( is_array($header_content_arr) ) {
            foreach ( $header_content_arr as $index => $header_content ) {
                $header_content_filtered[$index] = tagdiv_util::remove_inactive_shortcodes($header_content);
            }
        }

        // Encode the filtered content back to base64
        $content = base64_encode(json_encode($header_content_filtered));

    } else {
        // Remove inactive shortcodes from basic content
        $content = tagdiv_util::remove_inactive_shortcodes($content);
    }

    return $content;
}