# Spice Region WordPress Documentation

## Overview
This documentation covers all custom shortcodes and TagDiv Composer elements for the Spice Region functionality.

## 🚀 **Quick Start - Using Shortcode Element (Recommended)**

Since custom elements may not appear in TagDiv Composer, use the **"Shortcode"** element instead:

### **Step 1: Add Shortcode Element**
1. Open TagDiv Composer
2. Look for **"Shortcode"** element (in basic elements)
3. Drag it to your page

### **Step 2: Use These Shortcodes**
Paste these shortcodes directly into the Shortcode element:

**For current spice region posts:**
```
[spice_region_current_posts]
```

**For specific spice region posts:**
```
[spice_region_posts terms="lemongrass,cumin"]
```

**For homepage banner:**
```
[latest_post_banner]
```

**For single spice region card:**
```
[spice_region_single term="lemongrass"]
```

---

## 🔧 **Troubleshooting**

### **Custom Elements Not Appearing?**
If you don't see the custom elements in TagDiv Composer:

1. **Use Shortcode Element instead** (see Quick Start above)
2. **Check debug logs** for registration errors
3. **Clear all caches** (WordPress, browser, CDN)
4. **Try different hooks** - elements may register at different times

### **Shortcodes Not Working?**
1. **Check spelling** - shortcodes are case-sensitive
2. **Verify terms exist** - make sure spice region terms are created
3. **Check posts** - ensure posts are assigned to spice region terms
4. **Test with simple shortcode** - try `[spice_region_current_posts]` first

---

## 🎯 TagDiv Composer Elements (If Available)

### 1. Spice Region Posts
**Element Name:** "Spice Region Posts"  
**Icon:** Location pin  
**Purpose:** Display posts filtered by specific spice region terms

**Controls:**
- **Spice Region Terms:** Comma-separated slugs (e.g., `lemongrass,cumin`)
- **Posts Per Page:** Number of posts (default: 3)
- **Columns:** 1-4 columns
- **Image Aspect Ratio:** 16:9, 4:3, 1:1, 21:9
- **No Watermark:** Toggle original images

**How to Use:**
1. Open TagDiv Composer
2. Drag "Spice Region Posts" element to page
3. Configure settings in the panel
4. Save

---

### 2. Current Spice Region Posts
**Element Name:** "Current Spice Region Posts"  
**Icon:** Location pin (alt)  
**Purpose:** Auto-detect current spice region and show its posts

**Controls:**
- **Posts Per Page:** Number of posts (default: 3)
- **Columns:** 1-4 columns
- **Image Aspect Ratio:** 16:9, 4:3, 1:1, 21:9
- **No Watermark:** Toggle original images

**How to Use:**
1. Open TagDiv Composer
2. Drag "Current Spice Region Posts" element to page
3. Configure settings in the panel
4. Save

---

### 3. Latest Post Banner
**Element Name:** "Latest Post Banner"  
**Icon:** Image icon  
**Purpose:** Homepage cover banner with latest post

**Controls:**
- **Banner Aspect Ratio:** 16:9, 4:3, 1:1, 21:9
- **Text Color:** Color picker (default: white)
- **Overlay Color:** CSS rgba value (default: rgba(0,0,0,0.4))
- **No Watermark:** Toggle original images

**Features:**
- 1-line title (clamped with ellipsis)
- 2-line description
- 600px max-width on desktop, full-width on mobile
- Center-aligned text
- Responsive design

---

### 4. Spice Region Card
**Element Name:** "Spice Region Card"  
**Icon:** Location pin  
**Purpose:** Display single spice region term info

**Controls:**
- **Spice Region:** Dropdown of available terms

**Features:**
- Shows term name, subtitle, and icon
- Centered layout
- Fira Sans font for taxonomy
- Merriweather font for content

---

## 📝 Manual Shortcodes (Alternative)

### Basic Shortcodes

#### 1. Spice Region Posts
```
[spice_region_posts terms="lemongrass,cumin" posts_per_page="6" columns="3" ratio="1600x872" no_watermark="1"]
```

**Parameters:**
- `terms`: Comma-separated term slugs
- `posts_per_page`: Number of posts (default: 3)
- `columns`: 1-6 columns (default: 1)
- `ratio`: Aspect ratio (default: 1600x872)
- `no_watermark`: 1 to use original images

#### 2. Current Spice Region Posts
```
[spice_region_current_posts posts_per_page="3" columns="2" ratio="4x3"]
```

**Parameters:**
- `posts_per_page`: Number of posts (default: 3)
- `columns`: 1-6 columns (default: 1)
- `ratio`: Aspect ratio (default: 1600x872)
- `no_watermark`: 1 to use original images

#### 3. Latest Post Banner
```
[latest_post_banner ratio="1600x872" text_color="#ffffff" overlay_color="rgba(0,0,0,0.4)" no_watermark="1"]
```

**Parameters:**
- `ratio`: Aspect ratio (default: 1600x872)
- `text_color`: Text color (default: #ffffff)
- `overlay_color`: Overlay color (default: rgba(0,0,0,0.4))
- `no_watermark`: 1 to use original images

#### 4. Spice Region Single
```
[spice_region_single term="lemongrass"]
```

**Parameters:**
- `term`: Term slug to display

#### 5. Spice Region Debug
```
[spice_region_debug term="lemongrass"]
```

**Purpose:** Debug spice region data in browser console

---

## 🎨 Styling Features

### Typography
- **Taxonomy Header:** Fira Sans font
- **Post Content:** Merriweather font
- **Category Badge:** Fira Sans, uppercase, 15px

### Layout
- **Post Title:** 1 line, left-aligned, no padding
- **Post Description:** 3 lines, left-aligned
- **Category Badge:** Bottom-left of thumbnail, half outside image
- **Banner Text:** 600px max-width on desktop, full-width on mobile

### Colors
- **Taxonomy:** #a40d02 (red)
- **Category Badge:** White background, black text
- **Banner Text:** Customizable (default: white)

---

## 🔧 Technical Details

### Taxonomy Registration
- **Name:** spice_region
- **Post Type:** post
- **Hierarchical:** Yes
- **Public:** Yes
- **REST API:** Yes

### Required ACF Fields (if using ACF)
- **subtitle:** Text field for spice region
- **icon:** Image field for spice region

### Image Handling
- **Default Size:** Full resolution
- **Aspect Ratios:** 1600x872, 4x3, 1x1, 21x9
- **Watermark Removal:** Uses wp-content/ew-backup/ for original images

---

## 📱 Responsive Design

### Desktop
- **Banner Text:** 600px max-width, centered
- **Post Grid:** Flexible columns (1-6)
- **Typography:** Larger fonts

### Mobile
- **Banner Text:** Full width with padding
- **Post Grid:** Responsive columns
- **Typography:** Smaller fonts (28px title, 16px description)

---

## 🚀 Quick Start Guide

### For TagDiv Composer Users:
1. Open any page in TagDiv Composer
2. Look for these elements in the panel:
   - "Spice Region Posts"
   - "Current Spice Region Posts" 
   - "Latest Post Banner"
   - "Spice Region Card"
3. Drag and drop onto your page
4. Configure settings in the panel
5. Save

### For Manual Users:
1. Add shortcodes to any content area
2. Use the examples above
3. Customize parameters as needed

---

## 🔍 Troubleshooting

### Images Not Showing
- Check if post has featured image
- Try `no_watermark="1"` parameter
- Clear any caching

### Taxonomy Not Appearing
- Ensure terms are assigned to posts
- Check taxonomy registration
- Verify REST API is enabled

### Styling Issues
- Clear browser cache
- Check for CSS conflicts
- Verify font loading

---

## 📞 Support

If you need help:
1. Check this documentation first
2. Test with default parameters
3. Clear all caches
4. Check browser console for errors

---

**Last Updated:** January 2025  
**Version:** 1.0  
**Theme:** Newspaper by TagDiv
