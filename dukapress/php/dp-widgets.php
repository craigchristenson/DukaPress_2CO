<?php
/*
 * This file handles the functions related to widgets.
 */


/**
 * Widget to display Detailed Shopping Cart
 *
 */
add_action('widgets_init', create_function('', 'return register_widget("dpsc_detailed_shopping_cart_widget");'));
class dpsc_detailed_shopping_cart_widget extends WP_Widget {
    function dpsc_detailed_shopping_cart_widget() {
        $widget_ops = array('description' => __('Displays DukaPress Shopping Cart',"dp-lang"));
        $control_ops = array('width' => 100, 'height' => 300);
        parent::WP_Widget(false,$name= __('DukaPress Shopping Cart',"dp-lang"),$widget_ops,$control_ops);
    }

    function form($instance) {
        $instance = wp_parse_args( (array) $instance, array(  'title' => '') );
        $title = esc_attr( $instance['title'] );
        ?>
<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:',"dp-lang");?></label>
    <input type="text" value="<?php echo $title; ?>" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" class="widefat" />
</p>
<p><?php _e('To change settings',"dp-lang")?>, <a href="<?php bloginfo('url')?>/wp-admin/admin.php?page=dukapress-shopping-cart-settings"><?php _e('click here',"dp-lang");?></a>.</p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        return $instance;
    }

    function widget($args, $instance) {
        extract($args);
        $title = empty( $instance['title'] ) ? __('DukaPress Shopping Cart',"dp-lang") : __($instance['title'],"dp-lang");
        echo $before_widget;
        echo $before_title.$title.$after_title;
        ?>
<div class="dpsc-shopping-cart" id="dpsc-shopping-cart">
            <?php _e(dpsc_print_cart_html(),"dp-lang");?>
</div>
        <?php
        echo $after_widget;
    }
}

/**
 * Widget to display Go to Checkout Widget 
 *
 */
add_action('widgets_init', create_function('', 'return register_widget("dpsc_show_checkout_link_widget");'));
class dpsc_show_checkout_link_widget extends WP_Widget {
    function dpsc_show_checkout_link_widget() {
        $widget_ops = array('description' => __('Displays DukaPress Checkout Link',"dp-lang"));
        $control_ops = array('width' => 100, 'height' => 300);
        parent::WP_Widget(false,$name=__('DukaPress Checkout',"dp-lang"),$widget_ops,$control_ops);
    }

    function form($instance) {
        $instance = wp_parse_args( (array) $instance, array(  'title' => '') );
        $title = esc_attr( $instance['title'] );
        ?>
<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:',"dp-lang");?></label>
    <input type="text" value="<?php echo $title; ?>" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" class="widefat" />
</p>
<p><?php _e('To change checkout url',"dp-lang");?>, <a href="<?php bloginfo('url')?>/wp-admin/admin.php?page=dukapress-shopping-cart-settings"><?php _e('click here',"dp-lang");?></a>.</p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        return $instance;
    }

    function widget($args, $instance) {
        extract($args);
        $title = empty( $instance['title'] ) ? __('DukaPress Checkout',"dp-lang") : __($instance['title'],"dp-lang");
        echo $before_widget;
        echo $before_title.$title.$after_title;
        $dpsc_output = dpsc_go_to_checkout_link();
        ?>
<div class="dpsc-checkout_url-widget" id="dpsc-checkout_url-widget">
            <?php _e($dpsc_output,"dp-lang");?>
</div>
        <?php
        echo $after_widget;
    }
}

/**
 * Widget to display Mini Shopping Cart
 *
 */
add_action('widgets_init', create_function('', 'return register_widget("dpsc_mini_shopping_cart_widget");'));

class dpsc_mini_shopping_cart_widget extends WP_Widget {
    function dpsc_mini_shopping_cart_widget() {
        $widget_ops = array('description' => __('Displays Mini DukaPress Shopping Cart',"dp-lang"));
        $control_ops = array('width' => 100, 'height' => 300);
        parent::WP_Widget(false,$name=__('Mini DukaPress Shopping Cart',"dp-lang"),$widget_ops,$control_ops);
    }

    function form($instance) {
        //Nothing to do here.
    }

    function update($new_instance, $old_instance) {
        //Nothing to do here.
    }

    function widget($args, $instance) {
        extract($args);
        echo $before_widget;
        echo $before_title.$after_title;
        echo $before_widget;
        ?>
<div class="dpsc-mini-shopping-cart" id="dpsc-mini-shopping-cart">
            <?php echo dpsc_print_cart_html(TRUE);?>
</div>
        <?php
        echo $after_widget;
    }
}

/**
 * This function returns the checkout link.
 *
 */
function dpsc_go_to_checkout_link() {
    $dpsc_output = '';
    if (!dpsc_cart_full()) {
        $dpsc_output .= __('Your cart is empty.',"dp-lang");
    }
    else {
        $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
        $dpsc_output = '<a href="' . $dp_shopping_cart_settings['checkout'] . '">' . __('Go to Checkout',"dp-lang") . '</a>';
    }
    return $dpsc_output;
}

/**
 * This function returns the number of products in cart
 *
 */
function dpsc_cart_full() {
    $count = 0;
    if (isset($_SESSION['dpsc_products']) && is_array($_SESSION['dpsc_products'])) {
        foreach ($_SESSION['dpsc_products'] as $item) {
            $count++;
        }
        return $count;
    }
    else
        return 0;
}

/**
 * This function returns the HTML for cart
 *
 */
function dpsc_print_cart_html($mini=FALSE, $product_name = FALSE) {
    $dpsc_output = '';
    $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
    $dpsc_total_products = 0;
    if (!dpsc_cart_full()) {
        $dpsc_output .= __('Your cart is empty.',"dp-lang");
    }
    else {
        $dpsc_total = 0.00;
        $dpsc_products_in_cart = $_SESSION['dpsc_products'];
        $dpsc_total_discount = 0.00;
        if (is_array($dpsc_products_in_cart)) {
           if($dp_shopping_cart_settings['dp_shop_mode'] != 'inquiry') {
               $price_head = '<th id="price">' . __('Price',"dp-lang") . '</th>';
           }
           else {
               $price_head = '';
           }
           if ($product_name) {
               $dpsc_output .= '<div class="dpsc_update_notification">' . __('You have added',"dp-lang") . ' <strong>' . $product_name . '</strong> ' . __('to the cart',"dp-lang") . '.</div>';
           }
            $dpsc_output .= '<table class="shoppingcart">
                <tr><th id="product">' . __('Product',"dp-lang") . '</th>
                <th id="dpsc-cart-quantity">' . __('Qty',"dp-lang") . '</th>
                ' . $price_head . '</tr>';
            foreach ($dpsc_products_in_cart as $dpsc_product_in_cart) {
                $dpsc_discount_on_this_product = 0.00;
                $dpsc_discount_value = $_SESSION['dpsc_discount_value'];
                if($dp_shopping_cart_settings['dp_shop_mode'] != 'inquiry') {
                    $dpsc_at_checkout_to_be_displayed_price = $dp_shopping_cart_settings['dp_currency_symbol'] . ' '.number_format(floatval(($dpsc_product_in_cart['price']*$dpsc_product_in_cart['quantity'])),2);
                }
                else {
                    $dpsc_at_checkout_to_be_displayed_price = '';
                }
				$dpsc_var = '';
				if (!empty($dpsc_product_in_cart['var'])) {
					$dpsc_var = ' (' . $dpsc_product_in_cart['var'] . ')';
				}
                $dpsc_total_products += $dpsc_product_in_cart['quantity'];
                $dpsc_total += floatval($dpsc_product_in_cart['price']*$dpsc_product_in_cart['quantity']);
                $dpsc_output .= '<tr><td>'.$dpsc_product_in_cart['name']. __($dpsc_var, "dp-lang").'</td>
                    <td>'.$dpsc_product_in_cart['quantity'].'</td>
                        <td>'.$dpsc_at_checkout_to_be_displayed_price.'</td></tr>';
            }
            $dpsc_output .= '</table>';
            if($dp_shopping_cart_settings['dp_shop_mode'] != 'inquiry') {
                $dpsc_output .= '<strong>' . __('Total:',"dp-lang") . $dp_shopping_cart_settings['dp_currency_symbol'] . ' ' . number_format($dpsc_total,2) . '</strong>';
            }
            $dpsc_output .= '<div><span class="emptycart">
			<a href="#" class="dpsc_empty_your_cart">' . __('Empty your cart',"dp-lang") . '</a>
                </span></div>';
            $dpsc_output .= "<span class='gocheckout'>" . dpsc_go_to_checkout_link() . "</span>";
        }
    }
    if ($mini) {
        $dpsc_at_mini_price = '';
        if($dp_shopping_cart_settings['dp_shop_mode'] != 'inquiry') {
            $dpsc_at_mini_price = $dp_shopping_cart_settings['dp_currency_symbol'].number_format($dpsc_total,2);
        }
        return '<a href="' . $dp_shopping_cart_settings["checkout"] . '">'. $dpsc_total_products . ' ' . __('Products',"dp-lang") . ' ' . $dpsc_at_mini_price . '</a>';
    }
    else {
        return $dpsc_output;
    }
}


/**
 * Widget to display Products
 *
 */
add_action('widgets_init', create_function('', 'return register_widget("dpsc_show_product_widget");'));
class dpsc_show_product_widget extends WP_Widget {
    function dpsc_show_product_widget() {
        $widget_ops = array('description' => __('Product Display',"dp-lang"));
//        $control_ops = array('width' => 100, 'height' => 300);
        parent::WP_Widget(false,$name=__('DukaPress Product Display',"dp-lang"),$widget_ops);
    }

    function form($instance) {
        $instance = wp_parse_args( (array) $instance, array(  'title' => '', 'number' => '', 'type' => '', 'thumbnail' => '', 'atc' => '', 'post_category' => '') );
        $title = esc_attr( $instance['title'] );
        $number = esc_attr( $instance['number'] );
        $type = esc_attr( $instance['type'] );
        $thumbnail = esc_attr( $instance['thumbnail'] );
        $buy_now = esc_attr( $instance['buy_now'] );
        $width = esc_attr( $instance['width'] );
        $height = esc_attr( $instance['height'] );
        $atc = esc_attr( $instance['atc'] );
        $category = esc_attr($instance['category']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title","dp-lang");?>:</label>
            <input type="text" value="<?php echo $title; ?>" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" class="widefat" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e("Number of products to display","dp-lang");?>:</label>
            <input type="text" value="<?php echo $number; ?>" name="<?php echo $this->get_field_name('number'); ?>" id="<?php echo $this->get_field_id('number'); ?>" class="widefat" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e("Category ID","dp-lang");?> (<small><?php _e("comma separated. e.g.","dp-lang");?> <i>1,2,3</i></small>):</label>
            <input type="text" value="<?php echo $category; ?>" name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>" class="widefat" />
        </p>
        <p>
            <label><?php _e("Post Type","dp-lang");?>:</label>
            <select name="<?php echo $this->get_field_name('type'); ?>">
                <option value="post" <?php if ($type === 'post') { echo 'selected="selected"';}?>><?php _e("Normal Post","dp-lang");?></option>
                <option value="duka" <?php if ($type === 'duka') { echo 'selected="selected"';}?>><?php _e("DukaPress Product Post Type","dp-lang");?></option>
            </select>
        </p>
        <p>
            <label><?php _e("Show Thumbnail","dp-lang");?>:</label>
            <select name="<?php echo $this->get_field_name('thumbnail'); ?>">
                <option value="yes" <?php if ($thumbnail === 'yes') { echo 'selected="selected"';}?>><?php _e("Yes","dp-lang");?></option>
                <option value="no" <?php if ($thumbnail === 'no') { echo 'selected="selected"';}?>><?php _e("No","dp-lang");?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e("Thumbnail Width","dp-lang");?>:</label>
            <input type="text" value="<?php echo $width; ?>" name="<?php echo $this->get_field_name('width'); ?>" id="<?php echo $this->get_field_id('width'); ?>" class="widefat" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e("Thumbnail Height","dp-lang");?>:</label>
            <input type="text" value="<?php echo $height; ?>" name="<?php echo $this->get_field_name('height'); ?>" id="<?php echo $this->get_field_id('height'); ?>" class="widefat" />
        </p>
        <p>
            <label><?php _e("Show Add to Cart","dp-lang");?>:</label>
            <select name="<?php echo $this->get_field_name('atc'); ?>">
                <option value="yes" <?php if ($atc === 'yes') { echo 'selected="selected"';}?>><?php _e("Yes","dp-lang");?></option>
                <option value="no" <?php if ($atc === 'no') { echo 'selected="selected"';}?>><?php _e("No","dp-lang");?></option>
            </select>
        </p>
        <p>
            <label><?php _e("Buy Now","dp-lang");?>:</label>
            <select name="<?php echo $this->get_field_name('buy_now'); ?>">
                <option value="no" <?php if ($buy_now === 'no') { echo 'selected="selected"';}?>><?php _e("No","dp-lang");?></option>
                <option value="yes" <?php if ($buy_now === 'yes') { echo 'selected="selected"';}?>><?php _e("Yes","dp-lang");?></option>
            </select>
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['number'] = strip_tags( $new_instance['number'] );
        $instance['type'] = strip_tags( $new_instance['type'] );
        $instance['thumbnail'] = strip_tags( $new_instance['thumbnail'] );
        $instance['width'] = strip_tags( $new_instance['width'] );
        $instance['height'] = strip_tags( $new_instance['height'] );
        $instance['atc'] = strip_tags( $new_instance['atc'] );
        $instance['buy_now'] = strip_tags( $new_instance['buy_now'] );
        $instance['category'] = strip_tags( $new_instance['category'] );
        return $instance;
    }

    function widget($args, $instance) {
        extract($args);
        $title = $instance['title'];
        echo $before_widget;
        if ($title) {echo $before_title.$title.$after_title;}
        $direct_checkout = false;
        if ($instance['buy_now'] === 'yes') {
          $direct_checkout = true;
        }
        $widget_html = '';
        $widget_products = get_posts('numberposts=' . $instance['number'] . '&post_type=' . $instance['type'] . '&meta_key=price&category=' . $instance['category'] . 'orderby=post_date&order=DESC');
        if (is_array($widget_products)) {
            $widget_html .= '<div class="dp_products_widget">';
            foreach ($widget_products as $product) {
                $output = dpsc_get_product_details($product->ID, false, $direct_checkout);
                $widget_html .= '<div class="dp_widget_product">';
                $prod_permalink = get_permalink($product->ID);
                if ($instance['thumbnail'] === 'yes') {
                    $attachment_images =&get_children('post_type=attachment&post_status=inherit&post_mime_type=image&post_parent=' . $product->ID);
                    $main_image = '';
                    foreach ($attachment_images as $image) {
                        $main_image = $image->guid;
                        break;
                    }
                    if ($main_image != '') {
                        $widget_html .= '<div class="dp_widget_product_image">';
                        $widget_html .= '<a href="' . $prod_permalink . '" title="' .$product->post_title . '"><img src="' . DP_PLUGIN_URL . '/lib/timthumb.php?src=' . $main_image . '&w=' . $instance['width'] . '&h=' . $instance['height'] . '&zc=1" ></a>';
                        $widget_html .= '</div>';
                    }
                }
                $widget_html .= '<div class="dp_widget_product_detail">';
                $widget_html .= '<p class="title"><a href="' . $prod_permalink . '" title="' .$product->post_title . '">' . $product->post_title . '</a></p>';
                if ($instance['atc'] === 'yes') {
                    $widget_html .= $output['start'];
                    $widget_html .= $output['add_to_cart'];
                    $widget_html .= $output['end'];
                }
                $widget_html .= '</div>';
                $widget_html .= '</div>';
            }
            $widget_html .= '</div>';
        }
        echo $widget_html;
        echo $after_widget;
    }
}

add_action('widgets_init', create_function('', 'return register_widget("dpsc_custom_search_widget");'));
class dpsc_custom_search_widget extends WP_Widget {
    function dpsc_custom_search_widget() {
        $widget_ops = array('description' => __('Search Widget For DukaPress',"dp-lang"));
        $control_ops = array('width' => 100, 'height' => 300);
        parent::WP_Widget(false,$name= __('DukaPress Search Widget',"dp-lang"),$widget_ops,$control_ops);
    }

    function form($instance) {
        $instance = wp_parse_args( (array) $instance, array(  'title' => '', 'page_id' => '') );
        $title = esc_attr( $instance['title'] );
        $page_id = intval(esc_attr( $instance['page_id']));
        ?>
<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:',"dp-lang");?></label>
    <input type="text" value="<?php echo $title; ?>" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" class="widefat" />
</p>
<p>
    <label for="<?php echo $this->get_field_id('page_id'); ?>"><?php _e('Select Search Result Page:',"dp-lang");?></label>
<!--    <input type="text" value="<?php echo $title; ?>" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" class="widefat" />-->
    <?php
    $args = array('selected' => $page_id, 'name' => $this->get_field_name('page_id'));
    wp_dropdown_pages($args);
    ?>
</p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['page_id'] = strip_tags( $new_instance['page_id'] );
        return $instance;
    }

    function widget($args, $instance) {
        extract($args);
        $title = $instance['title'];
        $dp_search_page_id = $instance['page_id'];
        echo $before_widget;
        if (!empty($title)) {
            echo $before_title.$title.$after_title;
        }
        ?>
<form id="dp_searchform" action="<?php echo get_permalink($dp_search_page_id)?>" method="GET">
    <input id="dp_s" type="text" name="dp_s" value="" />
    <input id="dp_search_submit" type="submit" value="Search" />
</form>
        <?php
        echo $after_widget;
    }
}

?>