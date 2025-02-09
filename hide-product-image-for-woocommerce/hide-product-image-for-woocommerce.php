<?php
/*
Plugin Name: Hide Product Image for WooCommerce
Version: 1.0.5
Plugin URI: https://noorsplugin.com/hide-product-image-for-woocommerce-plugin/
Author: naa986
Author URI: https://noorsplugin.com/
Description: Hide product images in WooCommerce
Text Domain: hide-product-image-for-woocommerce
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit;
}

class HIDE_PRODUCT_IMAGE_WC
{
    var $plugin_version = '1.0.5';
    var $db_version = '1.0.1';
    var $plugin_url;
    var $plugin_path;
    function __construct()
    {
        define('HIDE_PRODUCT_IMAGE_WC_VERSION', $this->plugin_version);
        define('HIDE_PRODUCT_IMAGE_WC_DB_VERSION', $this->db_version);
        define('HIDE_PRODUCT_IMAGE_WC_SITE_URL',site_url());
        define('HIDE_PRODUCT_IMAGE_WC_URL', $this->plugin_url());
        define('HIDE_PRODUCT_IMAGE_WC_PATH', $this->plugin_path());
        $this->plugin_includes();
        $this->loader_operations();
    }
    
    function plugin_includes()
    {
        if(is_admin())
        {
            include_once('addons/hide-product-image-wc-addons.php');
        }
    }
    
    function loader_operations()
    {
        register_activation_hook(__FILE__, array($this, 'activate_handler'));
        add_action('plugins_loaded', array($this, 'plugins_loaded_handler'));
        if(is_admin())
        {
            add_filter('plugin_action_links', array($this,'add_plugin_action_links'), 10, 2 );
        }
        add_action('admin_menu', array($this, 'add_options_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_plugin_scripts'));
        add_filter('woocommerce_locate_template', array($this, 'hide_product_image_wc_template'), 20, 3);
    }
    
    function activate_handler() {
        add_option('hide_prod_img_wc_db_version', $this->db_version);
        add_option('hpifwc_hide_all_prod_img', '1');
    }
    
    function plugins_loaded_handler()
    {
        load_plugin_textdomain('hide-product-image-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/'); 
        $this->check_upgrade();
    }
    
    function check_upgrade() {
        if (is_admin()) {
            $db_version = get_option('hide_prod_img_wc_db_version');
            if (!isset($db_version) || $db_version != $this->db_version) {
                $this->activate_handler();
                update_option('hide_prod_img_wc_db_version', $this->db_version);
            }
        }
    }
    
    function enqueue_admin_scripts($hook) {
        if('settings_page_hide-product-image-wc-settings' != $hook) {
            return;
        }
        wp_register_style('hide-prod-img-wc-addons-menu', HIDE_PRODUCT_IMAGE_WC_URL.'/addons/hide-product-image-wc-addons.css');
        wp_enqueue_style('hide-prod-img-wc-addons-menu');
    }
    
    function enqueue_plugin_scripts() {
        if (!is_admin()) {
            wp_register_style('hide-prod-img-wc', HIDE_PRODUCT_IMAGE_WC_URL.'/css/main.css', array('woocommerce-layout'));
            wp_enqueue_style('hide-prod-img-wc');      
        }
    }
    
    function plugin_url()
    {
        if($this->plugin_url) {
            return $this->plugin_url;
        }
        return $this->plugin_url = plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) );
    }
    
    function plugin_path(){ 	
        if ( $this->plugin_path ) {
            return $this->plugin_path;	
        }
        return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
    }
    
    function add_plugin_action_links($links, $file)
    {
        if ( $file == plugin_basename( dirname( __FILE__ ) . '/hide-product-image-for-woocommerce.php' ) )
        {
            $links[] = '<a href="options-general.php?page=hide-product-image-wc-settings">'.__('Settings', 'hide-product-image-for-woocommerce').'</a>';
        }
        return $links;
    }

    function add_options_menu()
    {
        if(is_admin())
        {
            add_options_page(__('Hide Product Image for WooCommerce', 'hide-product-image-for-woocommerce'), __('Hide Product Image for WooCommerce', 'hide-product-image-for-woocommerce'), 'manage_options', 'hide-product-image-wc-settings', array($this, 'display_options_page'));
        }
    }

    function display_options_page()
    {   
        $plugin_tabs = array(
            'hide-product-image-wc-settings' => __('General', 'hide-product-image-for-woocommerce'),
            'hide-product-image-wc-settings&action=addons' => __('Add-ons', 'hide-product-image-for-woocommerce')
        );
        $url = "https://noorsplugin.com/hide-product-image-for-woocommerce-plugin/";
        $link_text = sprintf(__('Please visit the <a target="_blank" href="%s">Hide Product Image for WooCommerce</a> documentation page for setup instructions.', 'hide-product-image-for-woocommerce'), esc_url($url));          
        $allowed_html_tags = array(
            'a' => array(
                'href' => array(),
                'target' => array()
            )
        );
        echo '<div class="wrap">';               
        echo '<h2>Hide Product Image for WooCommerce - v'.HIDE_PRODUCT_IMAGE_WC_VERSION.'</h2>';
        echo '<div class="notice notice-info">'.wp_kses($link_text, $allowed_html_tags).'</div>';
        $current = '';
        $action = '';
        if (isset($_GET['page'])) {
            $current = sanitize_text_field($_GET['page']);
            if (isset($_GET['action'])) {
                $action = sanitize_text_field($_GET['action']);
                $current .= "&action=" . $action;
            }
        }
        $content = '';
        $content .= '<h2 class="nav-tab-wrapper">';
        foreach ($plugin_tabs as $location => $tabname) {
            if ($current == $location) {
                $class = ' nav-tab-active';
            } else {
                $class = '';
            }
            $content .= '<a class="nav-tab' . $class . '" href="?page=' . $location . '">' . $tabname . '</a>';
        }
        $content .= '</h2>';
        $allowed_html_tags = array(
            'a' => array(
                'href' => array(),
                'class' => array()
            ),
            'h2' => array(
                'href' => array(),
                'class' => array()
            )
        );
        echo wp_kses($content, $allowed_html_tags);

        if(!empty($action))
        { 
            switch($action)
            {
                case 'addons':
                    hide_product_image_wc_display_addons();
                    break;
            }
        }
        else
        {
            $this->general_settings();
        }

        echo '</div>'; 
    }

    function general_settings() {
        if (isset($_POST['hide_product_image_wc_update_settings'])) {
            $nonce = sanitize_text_field($_REQUEST['_wpnonce']);
            if (!wp_verify_nonce($nonce, 'hide_product_image_wc_general_settings')) {
                wp_die(__('Error! Nonce Security Check Failed! please save the general settings again.', 'hide-product-image-for-woocommerce'));
            }
            $hide_all_prod_img = '';
            if(isset($_POST['hide_all_product_images']) && !empty($_POST['hide_all_product_images'])){
                $hide_all_prod_img = sanitize_text_field($_POST['hide_all_product_images']);
            }
            $post = $_POST;
            do_action('hide_product_image_wc_general_settings_submitted', $post);
            update_option('hpifwc_hide_all_prod_img', $hide_all_prod_img);
            echo '<div id="message" class="updated fade"><p><strong>';
            echo __('Settings Saved', 'hide-product-image-for-woocommerce').'!';
            echo '</strong></p></div>';
        }
        $hide_all_product_images = get_option('hpifwc_hide_all_prod_img');

        ?>

        <form method="post" action="">
            <?php wp_nonce_field('hide_product_image_wc_general_settings'); ?>

            <table class="form-table">

                <tbody>

                    <tr valign="top">
                        <th scope="row"><label for="hide_all_product_images"><?php _e('Hide All Product Images', 'hide-product-image-for-woocommerce');?></label></th>
                        <td><input name="hide_all_product_images" type="checkbox" id="hide_all_product_images" <?php if (isset($hide_all_product_images) && $hide_all_product_images == '1') echo ' checked="checked"'; ?> value="1">
                            <p class="description"><?php _e('Check this option to hide images from all WooCommerce product pages', 'hide-product-image-for-woocommerce');?></p></td>
                    </tr>

                    <?php
                    $settings_fields = '';
                    $settings_fields = apply_filters('hide_product_image_wc_general_settings_fields', $settings_fields);
                    if(!empty($settings_fields)){
                        echo $settings_fields;
                    }
                    ?>
                </tbody>

            </table>

            <p class="submit"><input type="submit" name="hide_product_image_wc_update_settings" id="hide_product_image_wc_update_settings" class="button button-primary" value="<?php _e('Save Changes', 'hide-product-image-for-woocommerce');?>"></p></form>

        <?php
    }
    
    function hide_product_image_wc_template($template, $template_name, $template_path) 
    {
        if ($template_name == 'single-product/product-image.php') {
            $hide_product_image = false;
            //
            $hide_all_product_images = get_option('hpifwc_hide_all_prod_img');
            if(isset($hide_all_product_images) && $hide_all_product_images == '1'){
                $hide_product_image = true;
            }
            //
            global $product;
            $product_id = $product->get_id();
            $hide_image_by_product = '';
            $hide_image_by_product = apply_filters('hpifwc_hide_image_by_product', $hide_image_by_product, $product_id);
            if(!empty($hide_image_by_product)){
                $hide_product_image = true;
            }
            //
            if($hide_product_image){
                $template = hide_product_image_wc_get_template($template_name);
            }
        }
        return $template;
    }

}

$GLOBALS['hide_product_image_wc'] = new HIDE_PRODUCT_IMAGE_WC();

function hide_product_image_wc_get_template($template_name) 
{
    $plugin_path = untrailingslashit(plugin_dir_path(__FILE__)) . '/woocommerce/';
    $template = $plugin_path.$template_name;
    return $template;
}

