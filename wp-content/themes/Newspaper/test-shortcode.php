<?php
/**
 * Test shortcode functionality
 */

// Add test shortcode to theme
add_shortcode('test_shortcode_working', function() {
    return '<div style="background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin: 10px 0; border-radius: 4px;">
        <strong>✅ Theme Shortcode Working!</strong><br>
        Time: ' . current_time('Y-m-d H:i:s') . '<br>
        This proves shortcodes work in this theme!
    </div>';
});

// Add a simple spice region test shortcode
add_shortcode('spice_region_simple_test', function() {
    $output = '<div style="background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; margin: 10px 0; border-radius: 4px;">';
    $output .= '<strong>🧪 Spice Region Simple Test:</strong><br>';
    $output .= 'Current Post ID: ' . get_the_ID() . '<br>';
    
    // Check spice region categories
    $output .= '✅ Using WordPress categories for spice regions<br>';
    
    // Get current post's categories
    $categories = get_the_category();
    if (!empty($categories)) {
        $output .= '✅ Found ' . count($categories) . ' categories:<br>';
        foreach ($categories as $category) {
            $output .= '- ' . esc_html($category->name) . ' (slug: ' . esc_html($category->slug) . ')<br>';
        }
    } else {
        $output .= '⚠️ No categories found for this post<br>';
    }
    
    $output .= '</div>';
    return $output;
});

// Test if our plugin shortcodes exist
add_action('wp_footer', function() {
    if (shortcode_exists('spice_region_related_posts')) {
        echo '<!-- Spice Region Related Posts shortcode exists -->';
    } else {
        echo '<!-- Spice Region Related Posts shortcode NOT found -->';
    }
    
    if (shortcode_exists('spice_region_test_simple')) {
        echo '<!-- Spice Region Test Simple shortcode exists -->';
    } else {
        echo '<!-- Spice Region Test Simple shortcode NOT found -->';
    }
});
