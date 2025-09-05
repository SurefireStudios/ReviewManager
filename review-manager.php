<?php
/**
 * Plugin Name: Manual Review Manager
 * Plugin URI: https://github.com/SurefireStudios/ReviewManager.git
 * Description: Manually manage and display customer reviews from multiple business locations with editing capabilities and professional display options.
 * Version: 1.1.0
 * Author: Surefire Studios
 * License: GPL v2 or later
 * Text Domain: manual-review-manager
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MRM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MRM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MRM_VERSION', '1.1.0');

class ManualReviewManager {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Load plugin files
        $this->load_dependencies();
        
        // Initialize components
        if (is_admin()) {
            new MRM_Admin();
        }
        
        new MRM_Frontend();
        new MRM_Shortcodes();
    }
    
    private function load_dependencies() {
        require_once MRM_PLUGIN_DIR . 'includes/class-database.php';
        require_once MRM_PLUGIN_DIR . 'includes/class-admin.php';
        require_once MRM_PLUGIN_DIR . 'includes/class-frontend.php';
        require_once MRM_PLUGIN_DIR . 'includes/class-shortcodes.php';
    }
    
    public function activate() {
        // Load database class for activation
        require_once MRM_PLUGIN_DIR . 'includes/class-database.php';
        MRM_Database::create_tables();
        
        // Set default options
        add_option('mrm_version', MRM_VERSION);
        add_option('mrm_display_settings', array(
            'show_photos' => 1,
            'show_dates' => 1,
            'show_platform' => 1,
            'max_reviews' => 10,
            'min_rating' => 1,
            'photo_size' => 'small'
        ));
    }
    
    public function deactivate() {
        // Clean up scheduled events if any
        wp_clear_scheduled_hook('mrm_cleanup_temp_files');
    }
}

// Initialize the plugin
new ManualReviewManager(); 