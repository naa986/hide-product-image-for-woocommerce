<?php

function hide_product_image_wc_display_addons()
{
    /*
    echo '<div class="wrap">';
    echo '<h2>' .__('Hide Product Image for WooCommerce Add-ons', 'hide-product-image-for-woocommerce') . '</h2>';
    */
    $addons_data = array();

    $addon_1 = array(
        'name' => 'Hide Images by Products',
        'thumbnail' => HIDE_PRODUCT_IMAGE_WC_URL.'/addons/images/hpifwc-hide-images-by-products.png',
        'description' => 'Select the products for which images will be hidden',
        'page_url' => 'https://noorsplugin.com/hide-product-image-for-woocommerce-plugin/',
    );
    array_push($addons_data, $addon_1);
    
    //Display the list
    foreach ($addons_data as $addon) {
        ?>
        <div class="hide_product_image_wc_addons_item_canvas">
        <div class="hide_product_image_wc_addons_item_thumb">
            <img src="<?php echo esc_url($addon['thumbnail']);?>" alt="<?php echo esc_attr($addon['name']);?>">
        </div>
        <div class="hide_product_image_wc_addons_item_body">
        <div class="hide_product_image_wc_addons_item_name">
            <a href="<?php echo esc_url($addon['page_url']);?>" target="_blank"><?php echo esc_html($addon['name']);?></a>
        </div>
        <div class="hide_product_image_wc_addons_item_description">
        <?php echo esc_html($addon['description']);?>
        </div>
        <div class="hide_product_image_wc_addons_item_details_link">
        <a href="<?php echo esc_url($addon['page_url']);?>" class="hide_product_image_wc_addons_view_details" target="_blank">View Details</a>
        </div>    
        </div>
        </div>
        <?php
    }
    echo '</div>';//end of wrap
}
