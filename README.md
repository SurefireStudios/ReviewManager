# Manual Review Manager

A WordPress plugin for manually managing and displaying customer reviews from multiple business locations with full editing control and professional display options.

## Features

- **Manual Review Entry** - Add reviews through the admin interface
- **Full Editing Control** - Edit any aspect of reviews including text, ratings, and reviewer info
- **Multi-Location Support** - Manage reviews for multiple business locations
- **Bulk Text Replacement** - Perfect for changing business names across all reviews
- **Professional Display Options** - Grid, list, and slider layouts
- **Responsive Design** - Mobile-friendly display
- **SEO Optimized** - Includes structured data markup
- **No API Keys Required** - Completely self-contained

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
```

### Review Statistics
```
[review_stats show_breakdown="true"]
```

### Location-Specific Reviews
```
[review_manager location_id="1" layout="list"]
```

## Shortcode Parameters

### `[review_manager]`
- `layout` - Display layout: "grid", "list", or "slider" (default: "grid")
- `columns` - Number of columns for grid layout (default: 3)
- `max_reviews` - Maximum number of reviews to display (default: 10)
- `min_rating` - Minimum star rating to display (default: 1)
- `location_id` - Show reviews for specific location only
- `show_photos` - Show reviewer photos: "true" or "false" (default: "true")
- `show_dates` - Show review dates: "true" or "false" (default: "true")
- `show_platform` - Show review platform: "true" or "false" (default: "true")

### `[review_slider]`
- `autoplay` - Auto-advance slides: "true" or "false" (default: "false")
- `speed` - Autoplay speed in milliseconds (default: 5000)
- `location_id` - Show reviews for specific location only
- `min_rating` - Minimum star rating to display (default: 1)

### `[review_stats]`
- `location_id` - Show stats for specific location only
- `show_breakdown` - Show rating breakdown: "true" or "false" (default: "false")

## Usage Guide

### Adding Locations
1. Go to "Review Manager" → "Locations"
2. Click "Add New Location"
3. Enter location details (name, address, etc.)
4. Save the location

### Adding Reviews
1. Go to "Review Manager" → "Add Review"
2. Select the location
3. Enter review details:
   - Reviewer name and photo
   - Star rating (1-5)
   - Review text
   - Review date
   - Platform (Google, Yelp, etc.)
4. Save the review

### Managing Reviews
- View all reviews in "Review Manager" → "Reviews"
- Edit any review by clicking the "Edit" link
- Delete reviews you no longer want to display
- Use the bulk actions for managing multiple reviews

### Bulk Text Replacement
1. Go to "Review Manager" → "Settings"
2. Use the "Bulk Text Replacement" tool
3. Enter text to find and replace across all reviews
4. Perfect for changing business names or updating information

### Display Settings
Configure global display options in "Review Manager" → "Settings":
- Default number of reviews to show
- Minimum rating threshold
- Show/hide reviewer photos
- Show/hide review dates
- Show/hide platform badges

## Best Use Cases

- **Business Rebranding** - Use bulk replacement to update business names
- **Content Curation** - Manually select and edit your best reviews
- **Multi-Location Businesses** - Manage reviews for multiple locations
- **Complete Control** - Edit review content to fix typos or inappropriate language
- **No API Costs** - Avoid Google Places API or Yelp API fees
- **Offline Reviews** - Add reviews from any source, not just online platforms

## System Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Support

For support and documentation, visit [Surefire Studios](https://surefirestudios.io).

## License

This plugin is licensed under the GNU General Public License v2 (or later).

**Author:** Surefire Studios  
**License:** GPL v2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html