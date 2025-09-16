# Manual Review Manager

A comprehensive WordPress plugin for managing and displaying customer reviews from multiple business locations with full editing control, user submissions, and professional display options.

## 🌟 New Features in v1.3.0

- **🎨 Professional WordPress Widget** - Native Latest Posts-style widget for sidebars and widget areas
- **📱 Mobile-Responsive Design** - Perfect responsive layout that adapts to any screen size
- **✨ WordPress Core Styling** - Matches WordPress native widgets for seamless integration
- **🎯 Advanced Widget Options** - Complete control over display settings and filtering
- **⚡ Optimized Performance** - Streamlined code and improved loading times

## Features in v1.2.0

- **✨ User Review Submissions** - Let customers submit their own reviews with photo uploads
- **👥 User Management** - Track review submissions by registered users
- **✅ Review Approval System** - Moderate user-submitted reviews before publishing
- **📧 Admin Notifications** - Get notified when new reviews are submitted
- **🎨 Enhanced Styling** - Improved responsive design and customization options

## Core Features

- **Manual Review Entry** - Add reviews through the admin interface
- **Full Editing Control** - Edit any aspect of reviews including text, ratings, and reviewer info
- **Multi-Location Support** - Manage reviews for multiple business locations
- **Bulk Text Replacement** - Perfect for changing business names across all reviews
- **Professional Display Options** - Grid, list, slider, and grid slider layouts
- **User Review Submissions** - Allow customers to submit reviews with approval workflow
- **Professional WordPress Widget** - Native-style sidebar widget with advanced configuration options
- **Responsive Design** - Mobile-friendly display with customizable themes
- **SEO Optimized** - Includes structured data markup for search engines
- **Photo Upload Support** - Customer and admin photo uploads with automatic optimization
- **Platform Integration** - Support for Google, Yelp, Facebook, and custom platform badges
- **No API Keys Required** - Completely self-contained with no external dependencies

## Installation

1. Upload the plugin files to your `/wp-content/plugins/review-manager/` directory
2. Activate "Manual Review Manager" in your WordPress admin
3. Go to "Review Manager" → "Locations" to add your business locations
4. Go to "Review Manager" → "Add Review" to start adding reviews
5. Use shortcodes to display reviews on your website

## Shortcodes

### Basic Review Display
```
[review_manager]
[review_manager layout="grid" columns="3" max_reviews="12"]
[review_manager layout="list" max_reviews="5" min_rating="4"]
```

### Review Slider
```
[review_slider autoplay="true" speed="3000"]
[review_slider arrows="true" dots="true" max_reviews="10"]
```

### Grid Slider
```
[review_grid_slider columns="3" autoplay="true" speed="5000"]
[review_grid_slider columns="2" arrows="true" dots="true"]
```

### Review Statistics
```
[review_stats show_breakdown="true"]
[review_stats location_id="1" show_total="true" show_average="true"]
```

### Location-Specific Reviews
```
[review_manager location_id="1" layout="list"]
[review_manager location_id="2" show_review_button="true"]
```

### User Review Submissions
```
[review_manager show_review_button="true"]
```

### WordPress Widget (NEW!)
1. Go to "Appearance" → "Widgets"
2. Add "Review Manager: Latest Reviews" widget
3. Configure widget settings:
   - Title and number of reviews to show
   - Minimum rating filter
   - Location-specific display
   - Show/hide photos, dates, platforms
   - Customize excerpt length

## Shortcode Parameters

### `[review_manager]`
- `layout` - Display layout: "grid", "list", or "slider" (default: "grid")
- `columns` - Number of columns for grid layout (default: 3)
- `max_reviews` - Maximum number of reviews to display (default: 10)
- `min_rating` - Minimum star rating to display (default: 1)
- `location_id` - Show reviews for specific location only
- `platform` - Filter by platform: "all", "google", "yelp", "facebook", etc. (default: "all")
- `sort_by` - Sort order: "review_date", "rating", "reviewer_name" (default: "review_date")
- `order` - Sort direction: "ASC" or "DESC" (default: "DESC")
- `show_photos` - Show reviewer photos: "true" or "false" (default: "true")
- `show_dates` - Show review dates: "true" or "false" (default: "true")
- `show_platform` - Show review platform: "true" or "false" (default: "true")
- `show_review_button` - Show "Leave Review" button: "true" or "false" (default: "false")
- `truncate` - Number of words to truncate review text (default: 50)
- `theme` - Apply custom theme styling (optional)
- `photo_size` - Photo size: "small", "medium", "large" (default: "small")

### `[review_slider]`
- `max_reviews` - Maximum number of reviews to display (default: 20)
- `min_rating` - Minimum star rating to display (default: 1)
- `platform` - Filter by platform (default: "all")
- `location_id` - Show reviews for specific location only
- `autoplay` - Auto-advance slides: "true" or "false" (default: "true")
- `speed` - Autoplay speed in milliseconds (default: 5000)
- `arrows` - Show navigation arrows: "true" or "false" (default: "true")
- `dots` - Show navigation dots: "true" or "false" (default: "true")
- `show_photos` - Show reviewer photos: "true" or "false" (default: "true")
- `show_dates` - Show review dates: "true" or "false" (default: "true")
- `show_platform` - Show platform badges: "true" or "false" (default: "true")
- `truncate` - Number of words to truncate review text (default: 50)

### `[review_grid_slider]` (NEW!)
- `columns` - Number of columns per slide: 1-4 (default: 3)
- `max_reviews` - Maximum number of reviews to display (default: 20)
- `min_rating` - Minimum star rating to display (default: 1)
- `platform` - Filter by platform (default: "all")
- `location_id` - Show reviews for specific location only
- `autoplay` - Auto-advance slides: "true" or "false" (default: "true")
- `speed` - Autoplay speed in milliseconds (default: 5000)
- `arrows` - Show navigation arrows: "true" or "false" (default: "true")
- `dots` - Show navigation dots: "true" or "false" (default: "true")
- `show_photos` - Show reviewer photos: "true" or "false" (default: "true")
- `show_dates` - Show review dates: "true" or "false" (default: "true")
- `show_platform` - Show platform badges: "true" or "false" (default: "true")

### `[review_stats]`
- `location_id` - Show stats for specific location only
- `show_total` - Show total review count: "true" or "false" (default: "true")
- `show_average` - Show average rating: "true" or "false" (default: "true")
- `show_breakdown` - Show rating breakdown: "true" or "false" (default: "false")
- `theme` - Apply custom theme styling (optional)

## Usage Guide

### Adding Locations
1. Go to "Review Manager" → "Locations"
2. Click "Add New Location"
3. Enter location details (name, address, etc.)
4. Save the location

### Adding Reviews (Admin)
1. Go to "Review Manager" → "Add Review"
2. Select the location
3. Enter review details:
   - Reviewer name and photo
   - Star rating (1-5)
   - Review text
   - Review date
   - Platform (Google, Yelp, etc.)
4. Save the review

### User Review Submissions (NEW!)
1. Add `show_review_button="true"` to any shortcode
2. Customers click "Leave Your Own Review" button
3. Users must be logged in to submit reviews
4. Reviews are submitted with approval workflow
5. Admin receives notification of new submissions
6. Approve/reject reviews in "Review Manager" → "Reviews"

### Managing Reviews
- View all reviews in "Review Manager" → "Reviews"
- Edit any review by clicking the "Edit" link
- Approve/reject user-submitted reviews
- Delete reviews you no longer want to display
- Use the bulk actions for managing multiple reviews
- Filter reviews by approval status, platform, or location

### Bulk Text Replacement
1. Go to "Review Manager" → "Settings"
2. Use the "Bulk Text Replacement" tool
3. Enter text to find and replace across all reviews
4. Perfect for changing business names or updating information

### WordPress Widget Configuration
The "Review Manager: Latest Reviews" widget offers comprehensive customization:

**Basic Settings:**
- Widget title (appears above reviews)
- Number of reviews to display (1-20)
- Minimum rating filter (1-5 stars)
- Location filter (specific location or all locations)

**Display Options:**
- Show/hide reviewer photos with hover effects
- Show/hide review dates
- Show/hide platform badges (Google, Yelp, Facebook)
- Show/hide review excerpts with custom word count
- Show/hide star ratings

**Styling Features:**
- Native WordPress Latest Posts styling
- Responsive mobile design
- Horizontal layout with photos
- Hover animations and transitions
- Clean white background matching WordPress core

### Display Settings
Configure global display options in "Review Manager" → "Settings":
- Default number of reviews to show
- Minimum rating threshold
- Show/hide reviewer photos
- Show/hide review dates
- Show/hide platform badges
- Photo size settings
- Post-review redirect URL
- Review button styling

## Best Use Cases

- **Customer Engagement** - Let customers submit reviews directly on your website
- **Business Rebranding** - Use bulk replacement to update business names
- **Content Curation** - Manually select and edit your best reviews
- **Multi-Location Businesses** - Manage reviews for multiple locations
- **Complete Control** - Edit review content to fix typos or inappropriate language
- **Social Proof** - Display customer testimonials with photos and ratings
- **Professional Widget Integration** - Native WordPress widget with Latest Posts styling for seamless theme integration
- **Review Moderation** - Approve user submissions before they go live
- **No API Costs** - Avoid Google Places API or Yelp API fees
- **Offline Reviews** - Add reviews from any source, not just online platforms
- **Interactive Displays** - Use grid sliders and carousels for engaging presentations

## System Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Modern web browser for optimal admin experience

## Support

For support and documentation, visit [Surefire Studios](https://surefirestudios.io).

## License

This plugin is licensed under the GNU General Public License v2 (or later).

**Author:** Surefire Studios  
**Version:** 1.3.0  
**License:** GPL v2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  
**Plugin URI:** https://github.com/SurefireStudios/ReviewManager.git