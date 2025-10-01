=== Review Manager ===
Contributors: surefirestudios
Tags: reviews, testimonials, customer reviews, user reviews, review management, business reviews, review display, review slider, review grid
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A comprehensive WordPress plugin for managing and displaying customer reviews with user submission capabilities, multiple display layouts, and complete editorial control.

== Description ==

**Review Manager** is a powerful WordPress plugin that gives you complete control over your customer reviews. Unlike other review plugins that rely on external APIs, Review Manager lets you manually curate, edit, and display reviews exactly how you want them.

= 🌟 Key Features =

**User Review Submissions (NEW!)**
* Allow customers to submit reviews directly on your website
* User-friendly review submission form with photo uploads
* Automatic approval workflow for moderation
* Email notifications for new review submissions
* Logged-in user requirement for spam prevention

**Complete Review Management**
* Add, edit, and manage reviews through intuitive admin interface
* Support for multiple business locations
* Full editorial control - edit any aspect of reviews
* Bulk text replacement tool for rebranding
* Import reviews from any source (Google, Yelp, Facebook, etc.)

**Professional Display Options**
* **Grid Layout** - Clean, responsive grid display
* **List Layout** - Traditional list format
* **Review Slider** - Auto-advancing carousel
* **Grid Slider** - Multi-column sliding display
* **Review Statistics** - Display totals, averages, and breakdowns

**Advanced Features**
* Photo upload support for reviewers
* Platform badges (Google, Yelp, Facebook icons)
* Star rating system (1-5 stars)
* Responsive design for all devices
* SEO optimized with structured data markup
* Dark/light theme support
* Customizable button colors
* Read more/less functionality for long reviews

= 🎯 Perfect For =

* **Service Businesses** - Restaurants, salons, repair shops
* **E-commerce Stores** - Product testimonials and reviews
* **Multi-location Businesses** - Manage reviews across locations
* **Professional Services** - Lawyers, doctors, consultants
* **Any Business** wanting complete control over review display

= 🚀 User Review Submission Workflow =

1. Add `show_review_button="true"` to any shortcode
2. Customers click "Leave Your Own Review" button
3. Users log in (required for spam prevention)
4. Fill out review form with optional photo upload
5. Reviews are submitted for admin approval
6. Admin receives email notification
7. Approve/reject reviews from admin dashboard
8. Approved reviews appear on your website

= 📱 Responsive & SEO Friendly =

* Mobile-optimized responsive design
* Structured data markup for search engines
* Fast loading with optimized assets
* Accessibility compliant
* Translation ready

= 🔧 Easy Setup =

1. Install and activate the plugin
2. Add your business locations
3. Start adding reviews or enable user submissions
4. Use shortcodes to display reviews anywhere
5. Customize appearance and settings

= 💡 No External Dependencies =

* No API keys required
* No external service dependencies
* Complete data ownership
* Works offline
* GDPR compliant

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/review-manager/` directory
2. Activate "Review Manager" through the 'Plugins' menu in WordPress
3. Go to "Review Manager" → "Locations" to add your business locations
4. Go to "Review Manager" → "Add Review" to start adding reviews manually
5. Use shortcodes to display reviews on your website

== Frequently Asked Questions ==

= How do I allow customers to submit reviews? =

Add `show_review_button="true"` to any review shortcode. This will display a "Leave Your Own Review" button that allows logged-in users to submit reviews for approval.

= Can I edit user-submitted reviews? =

Yes! You have complete editorial control. You can edit any aspect of user-submitted reviews including text, ratings, and reviewer information.

= Do I need API keys from Google or Yelp? =

No! Review Manager is completely self-contained. You can manually add reviews from any source without needing external API access.

= Can I use this for multiple business locations? =

Absolutely! The plugin is designed for multi-location businesses. You can create separate locations and filter reviews by location.

= Are the reviews SEO friendly? =

Yes! The plugin includes structured data markup (JSON-LD) that helps search engines understand and display your reviews in search results.

= Can I customize the appearance? =

Yes! The plugin includes multiple themes (light, dark, auto), customizable button colors, and responsive layouts. You can also add custom CSS for further customization.

== Screenshots ==

1. Admin dashboard showing review statistics and recent submissions
2. Review management interface with approval workflow
3. Add/edit review form with all options
4. Frontend grid display of reviews with photos and ratings
5. Review slider carousel in action
6. User review submission form on frontend
7. Location management for multi-location businesses
8. Settings page with theme and display options

== Shortcodes ==

= Basic Display =
`[review_manager]` - Display reviews with default settings
`[review_manager layout="grid" columns="3" max_reviews="12"]` - 3-column grid with 12 reviews
`[review_manager layout="list" max_reviews="5"]` - List layout with 5 reviews

= Review Slider =
`[review_slider autoplay="true" speed="3000"]` - Auto-advancing slider
`[review_slider arrows="true" dots="true"]` - Slider with navigation

= Grid Slider =
`[review_grid_slider columns="3" autoplay="true"]` - Multi-column sliding display

= Review Statistics =
`[review_stats show_breakdown="true"]` - Display review statistics with rating breakdown

= User Submissions =
`[review_manager show_review_button="true"]` - Enable user review submissions

= Location Specific =
`[review_manager location_id="1" show_review_button="true"]` - Reviews for specific location with submission

== Shortcode Parameters ==

**Common Parameters:**
* `layout` - "grid", "list", "slider" (default: "grid")
* `columns` - Number of columns for grid layout (default: 3)
* `max_reviews` - Maximum reviews to display (default: 10)
* `min_rating` - Minimum star rating to show (default: 1)
* `location_id` - Show reviews for specific location only
* `platform` - Filter by platform: "google", "yelp", "facebook", etc.
* `show_photos` - Show reviewer photos: "true"/"false" (default: "true")
* `show_dates` - Show review dates: "true"/"false" (default: "true")
* `show_platform` - Show platform badges: "true"/"false" (default: "true")
* `show_review_button` - Enable user submissions: "true"/"false" (default: "false")
* `theme` - Force theme: "light", "dark", "auto"
* `photo_size` - Photo size: "small", "large" (default: "small")

**Slider Parameters:**
* `autoplay` - Auto-advance slides: "true"/"false" (default: "true")
* `speed` - Autoplay speed in milliseconds (default: 5000)
* `arrows` - Show navigation arrows: "true"/"false" (default: "true")
* `dots` - Show navigation dots: "true"/"false" (default: "true")

== Changelog ==

= 1.2.0 =
* NEW: User review submission system with approval workflow
* NEW: Frontend review submission form with photo uploads
* NEW: Admin email notifications for new review submissions
* NEW: User management and tracking for submitted reviews
* NEW: Enhanced responsive design and mobile optimization
* NEW: Grid slider layout for multi-column sliding displays
* NEW: Improved theme system with auto-detection
* NEW: Customizable button colors and styling options
* IMPROVED: Better photo handling and optimization
* IMPROVED: Enhanced security with proper escaping and nonces
* IMPROVED: Code optimization and WordPress standards compliance
* FIXED: Various UI improvements and bug fixes

= 1.1.0 =
* Added review slider functionality
* Improved responsive design
* Added platform badges for review sources
* Enhanced admin interface
* Added bulk text replacement tool
* Improved SEO with structured data

= 1.0.0 =
* Initial release
* Basic review management functionality
* Grid and list display layouts
* Multi-location support
* Admin interface for review management

== Upgrade Notice ==

= 1.2.0 =
Major update with user review submission capabilities! Users can now submit reviews directly on your website. Includes enhanced security, better responsive design, and new display options. Backup recommended before upgrading.

== Support ==

For support, documentation, and feature requests, please visit our [GitHub repository](https://github.com/SurefireStudios/ReviewManager) or contact us through [Surefire Studios](https://surefirestudios.io/).

== Privacy Policy ==

This plugin stores review data in your WordPress database. When users submit reviews, we collect:
- Name and email address (required)
- Review text and rating (required)
- Optional profile photo
- User ID for logged-in users

No data is sent to external services. All data remains on your server and is subject to your website's privacy policy.