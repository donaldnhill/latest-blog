# 🌶️ Spice Region Filtering - Usage Guide

## 🎯 Quick Start

### **1. In TagDiv Composer (Easiest)**
1. Open TagDiv Composer on any page
2. Click "+" to add elements
3. Look for:
   - **"Spice Region Filter"** - Add filtering interface
   - **"Spice Region Posts"** - Add filtered post grids
   - **"Smart List 5 Enhanced"** - Add filtered smart lists
4. Drag and drop, then configure settings

### **2. Using Shortcodes**

#### **Basic Filter:**
```
[td_spice_region_filter]
```

#### **Button Filter (Modern Style):**
```
[td_spice_region_filter filter_type="buttons" filter_style="modern"]
```

#### **Dropdown Filter:**
```
[td_spice_region_filter filter_type="dropdown" show_all_option="true"]
```

#### **Checkbox Filter (Multiple Selection):**
```
[td_spice_region_filter filter_type="checkboxes" all_text="All Regions"]
```

## 📋 Available Shortcodes

### **Filter Elements:**
- `[td_spice_region_filter]` - Main filter interface
- `[enhanced_smart_list]` - Smart list with filtering

### **Content Elements:**
- `[spice_region_posts]` - Filtered post grids
- `[latest_post_banner]` - Homepage banner
- `[spice_region_single]` - Single spice region card

## ⚙️ Configuration Options

### **Filter Types:**
- **`dropdown`** - Clean dropdown selection
- **`buttons`** - Clickable buttons with active states
- **`checkboxes`** - Multiple selection checkboxes

### **Filter Styles:**
- **`default`** - Clean, simple design
- **`modern`** - Gradient background with modern styling
- **`minimal`** - Clean white design

### **Advanced Filtering:**
```php
[spice_region_posts 
    terms="lemongrass,cumin"           // Spice region terms
    posts_per_page="6"                 // Number of posts
    columns="3"                        // Grid columns
    filter_by_category="1,2,3"        // Category IDs
    filter_by_tag="spicy,hot"         // Tag slugs
    exclude_posts="123,456"            // Post IDs to exclude
    show_taxonomy_title="true"         // Show taxonomy title
]
```

## 🔗 URL Parameters

Users can filter using URL parameters:
- `?spice_region=lemongrass` - Single term
- `?spice_region=lemongrass,cumin` - Multiple terms

## 🎨 Styling Options

### **Modern Style:**
- Gradient background
- White text
- Modern button styling

### **Minimal Style:**
- White background
- Clean borders
- Simple typography

### **Default Style:**
- Light gray background
- Standard styling
- Good for most themes

## 📱 Responsive Design

All filters are mobile-responsive:
- **Mobile:** Stacked layout
- **Tablet:** 2-column layout
- **Desktop:** Full grid layout

## 🔧 JavaScript Events

Listen for filter changes:
```javascript
window.addEventListener('spiceRegionFilter', function(event) {
    console.log('Filter changed to:', event.detail.termSlug);
    // Update other elements accordingly
});
```

## 📝 Examples

### **Homepage with Filter:**
```php
<!-- Add filter at top -->
[td_spice_region_filter filter_type="buttons" filter_style="modern"]

<!-- Add filtered content below -->
[spice_region_posts terms="lemongrass,cumin" posts_per_page="6" columns="3"]
```

### **Category Page with Advanced Filtering:**
```php
[spice_region_posts 
    terms="lemongrass" 
    posts_per_page="8" 
    columns="4"
    filter_by_category="1,2"
    filter_by_tag="spicy,hot"
    show_taxonomy_title="true"
]
```

### **Smart List with Filtering:**
```php
[enhanced_smart_list show_filters="true" show_indicators="true"]
```

## 🚀 Pro Tips

1. **Combine Filters:** Use multiple filter types on the same page
2. **URL Deep Linking:** Users can bookmark filtered views
3. **Mobile First:** All filters work great on mobile devices
4. **Custom Styling:** Override CSS for your brand colors
5. **JavaScript Integration:** Listen for filter events to update other content

## 🎯 Best Practices

1. **Use Button Filters** for touch devices
2. **Use Dropdown Filters** for single selection
3. **Use Checkbox Filters** for multiple selection
4. **Test on Mobile** to ensure good UX
5. **Add Loading States** for better user experience

## 🔍 Troubleshooting

### **Filter Not Showing:**
- Check if spice_region taxonomy has terms
- Verify shortcode syntax
- Check for JavaScript errors

### **Posts Not Filtering:**
- Ensure posts have spice_region terms assigned
- Check term slugs are correct
- Verify taxonomy is registered

### **Styling Issues:**
- Check for CSS conflicts
- Use browser developer tools
- Override styles with !important if needed

## 📞 Support

For issues or questions:
1. Check the example page: `/spice-region-example/`
2. Review the shortcode parameters
3. Test with different filter types
4. Check browser console for errors

