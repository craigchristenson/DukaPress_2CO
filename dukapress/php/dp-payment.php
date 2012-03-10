<?php

/*
 * This file handles the functions related to payment.
 */
/**
 * This function handles the payments
 *
 */
if ($_REQUEST['dpsc_ajax_action'] === 'dpsc_payment_option') {
    add_action('init', 'dpsc_payment_option');
}

function dpsc_payment_option() {
    $dpsc_payment_option = $_POST['payment_selected'];
    $dpsc_discount_value = isset($_POST['discount']) ? $_POST['discount'] : FALSE;
    $dpsc_shipping_value = isset($_POST['shipping']) ? $_POST['shipping'] : FALSE;
    list($dpsc_total, $dpsc_shipping_weight, $products, $number_of_items_in_cart) = dpsc_pnj_calculate_cart_price();
    if (!$dpsc_shipping_value) {
        $dpsc_shipping_value = dpsc_pnj_calculate_shipping_price($dpsc_shipping_weight, $dpsc_total, $number_of_items_in_cart);
    }
    if ($products) {
        list($invoice, $bfname, $blname, $bcity, $baddress, $bstate, $bzip, $bcountry, $bemail) = dpsc_on_payment_save($dpsc_total, $dpsc_shipping_value, $products, $dpsc_discount_value, $dpsc_payment_option);
        //Check if price is zero
		if(!dps_zero_price_check($dpsc_total,$dpsc_discount_value,$dpsc_shipping_value)){
			switch ($dpsc_payment_option) {
				case 'paypal':
					$output = dpsc_paypal_payment($dpsc_total, $dpsc_shipping_value, $dpsc_discount_value, $invoice);
					break;
				case 'authorize':
					$output = dpsc_authorize_payment($dpsc_total, $dpsc_shipping_value, $dpsc_discount_value, $invoice, $bfname, $blname, $bcity, $baddress, $bstate, $bzip, $bcountry, $bemail);
					break;
				case 'worldpay':
					$output = dpsc_worldpay_payment($dpsc_total, $dpsc_shipping_value, $dpsc_discount_value, $invoice, $bfname, $blname, $bcity, $baddress, $bstate, $bzip, $bcountry, $bemail);
					break;
				case 'alertpay':
					$output = dpsc_alertpay_payment($dpsc_total, $dpsc_shipping_value, $dpsc_discount_value, $invoice, $bfname, $blname, $bcity, $baddress, $bstate, $bzip, $bcountry, $bemail);
					break;
				case 'tco':
					$output = dpsc_tco_payment($dpsc_total, $dpsc_shipping_value, $dpsc_discount_value, $invoice, $bfname, $blname, $bcity, $baddress, $bstate, $bzip, $bcountry, $bemail);
					break;
				case 'bank':
					$output = dpsc_other_payment($invoice);
					break;
				case 'cash':
					$output = dpsc_other_payment($invoice);
					break;
				case 'mobile':
					$output = dpsc_other_payment($invoice);
					break;
				case 'delivery':
					$output = dpsc_other_payment($invoice);
					break;
				default:
					ob_start();
					do_action('dpsc_other_payment_form_' . $dpsc_payment_option, $dpsc_total, $dpsc_shipping_value, $dpsc_discount_value, $invoice, $bfname, $blname, $bcity, $baddress, $bstate, $bzip, $bcountry, $bemail);
					$output = ob_get_contents();
					ob_end_clean();
					break;
			}
		}else{
			dpsc_custom_payment_process($invoice,$bemail);
			exit();
		}
    } else {
        $output = __('There are no products in your cart.', "dp-lang");
        $output = str_replace(Array("\n", "\r"), Array("\\n", "\\r"), addslashes($output));
        echo "jQuery('div.dpsc-checkout').html('$output');";
        exit ();
    }
    $output = str_replace(Array("\n", "\r"), Array("\\n", "\\r"), addslashes($output));
    echo "jQuery('div#dpsc_hidden_payment_form').html('$output');";
    dpsc_pnj_calculate_cart_price(TRUE);
    $products = $_SESSION['dpsc_products'];
    foreach ($products as $key => $item) {
       unset($products[$key]);
    }
    $_SESSION['dpsc_products'] = $products;
    unset($_SESSION['dpsc_shiping_price']);
    echo "jQuery('#dpsc_payment_form').submit();";
    exit ();
}

//Validate if price is zero and take to thank you page directly
function dps_zero_price_check($total,$discount,$shipping){
	$total_discount = 0.00;
	$zero_price = true;
	$total_tax = 0.00;
	
	$dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
    $tax = $dp_shopping_cart_settings['tax'];
	if ($discount > 0) {
		$total_discount = $total*$discount/100;
	}
	else {
		$total_discount = 0;
	}
	if ($tax > 0) {
		$total_tax = ($total-$total_discount)*$tax/100;
	}
	else {
		$total_tax = 0;
	}
	$amount = number_format($total+$shipping+$total_tax-$total_discount,2);
	if($amount > 0){
		$zero_price = false;
	}
	return $zero_price;
}
//Process payment if price is zero
function dpsc_custom_payment_process($invoice,$payer_email){
	global $wpdb;
    $payment_status = 'Paid';
    
    $table_name = $wpdb->prefix . "dpsc_transactions";
    $update_query = "UPDATE {$table_name} SET `payer_email`='{$payer_email}', `payment_status`='{$payment_status}'WHERE `invoice`='{$invoice}'";
    $wpdb->query($update_query);
    $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
	$message = '';
	$digital_message = '';
	$check_query = "SELECT * FROM {$table_name} WHERE `invoice`='{$invoice}'";
	$result = $wpdb->get_row($check_query);
	$is_digital = dpsc_pnj_is_digital_present($result->products);
	if ($is_digital) {
		$file_names = dpsc_pnj_get_download_links($is_digital);
		if ($file_names) {
			if (is_array($file_names) && count($file_names) > 0) {
				$digital_message .= '<br/>Your download links:<br/><ul>';
				foreach ($file_names as $file_name) {
					$file_name = explode('@_@||@_@', $file_name);
					$temp_name = $file_name[0];
					$real_name = $file_name[1];
					$digital_message .= '<li><a href="' . DP_PLUGIN_URL . '/download.php?id=' . $temp_name . '">' . $real_name . '</a></li>';
				}
				$digital_message .= '</ul><br/>';
			}
		}
	}
	$email_fname = $result->billing_first_name ;
	$email_shop_name = $dp_shopping_cart_settings['shop_name'];
	$to = $result->billing_email;
	$from = get_option('admin_email');


	$nme_dp_mail_option = get_option('dp_usr_payment_mail', true);

	$message = $nme_dp_mail_option['dp_usr_payment_mail_body'];
	$subject = $nme_dp_mail_option['dp_usr_payment_mail_title'];

	$find_tag = array('%fname%', '%status%', '%email%', '%inv%', '%digi%', '%shop%');
	$rep_tag = array($email_fname, $updated_status, $to, $invoice, $digital_message, $email_shop_name);
	$message =str_replace($find_tag, $rep_tag, $message);
	//email to payer
	dpsc_pnj_send_mail($to, $from, $dp_shopping_cart_settings['shop_name'], $subject, $message);

	$nme_dp_mail_option = get_option('dp_admin_payment_mail', true);
	
	$message = $nme_dp_mail_option['dp_admin_payment_mail_body'];
	$message = str_replace("\r",'<br>', $message);
	$subject = $nme_dp_mail_option['dp_usr_admin_payment_mail_title'];
	
	$find_tag = array('%fname%', '%status%', '%email%', '%inv%', '%digi%', '%shop%');
	$rep_tag = array($email_fname, $updated_status, $to, $invoice, $digital_message, $email_shop_name);
	$message = str_replace($find_tag, $rep_tag, $message);
	//email to admin
	dpsc_pnj_send_mail($from, $to, $dp_shopping_cart_settings['shop_name'], $subject, $message);
    
    
    $return_path = $dp_shopping_cart_settings['thank_you'];
    $check_return_path = explode('?', $return_path);
    if (count($check_return_path) > 1) {
        $return_path .= '&id=' . $invoice;
    } else {
        $return_path .= '?id=' . $invoice;
    }
	
	$products = $_SESSION['dpsc_products'];
    foreach ($products as $key => $item) {
       unset($products[$key]);
    }
    $_SESSION['dpsc_products'] = $products;
    unset($_SESSION['dpsc_shiping_price']);
	$output = "<script type='text/javascript'> window.location.href='".$return_path."'; </script>";
	$output = str_replace(Array("\n", "\r"), Array("\\n", "\\r"), addslashes($output));
    echo "jQuery('div#dpsc_hidden_payment_form').html('$output');";
}

/**
 * This function generates 2Checkout form
 *
 */
function dpsc_tco_payment($dpsc_total = FALSE, $dpsc_shipping_value = FALSE, $dpsc_discount_value = FALSE, $invoice = FALSE) {
    $dpsc_products = $_SESSION['dpsc_products'];
	$dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
    $output = '';
	$total_tax = 0.00;
	$total_discount = 0.00;
	$total_shipping = 0.00;
	if ($dp_shopping_cart_settings['discount_enable'] === 'true' && $dpsc_discount_value) {
		$total_discount = $dpsc_total * $dpsc_discount_value / 100;
	}
	if ($dp_shopping_cart_settings['tax'] > 0) {
		$tax_rate = $dp_shopping_cart_settings['tax'];
		$total_tax = ($dpsc_total - $total_discount) * $tax_rate / 100;
	}
	if ($dpsc_shipping_value) {
		$total_shipping = $dpsc_shipping_value;
	}
	$conversion_rate = 1;
	$total_amount = ($dpsc_total + $total_tax + $total_shipping - $total_discount) * $conversion_rate;
	$dpsc_total = number_format($total_amount, 2, '.', '');
    if (is_array($dpsc_products) && count($dpsc_products) > 0) {
        $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
            $dpsc_form_action = 'https://www.2checkout.com/checkout/spurchase';
        
        $return_path = $dp_shopping_cart_settings['thank_you'];
        $check_return_path = explode('?', $return_path);
		$var_tco_field = '';
        if (count($check_return_path) > 1) {
            $return_path .= '&id=' . $invoice;
        } else {
            $return_path .= '?id=' . $invoice;
        }
        $output = '<form name="dpsc_tco_form" id="dpsc_payment_form" action="' . $dpsc_form_action . '" method="post">';
        $output .= '<input type="hidden" name="return_url" value="' . $return_path . '"/>
                     <input type="hidden" name="cmd" value="_ext-enter" />
                     <input type="hidden" name="x_receipt_link_url" value="' . $return_path . '"/>
                     <input type="hidden" name="redirect_cmd" value="_cart" />
                     <input type="hidden" name="sid" value="' . $dp_shopping_cart_settings['tco_id'] . '"/>
                     <input type="hidden" name="rm" value="2" />
                     <input type="hidden" name="upload" value="1" />
                     <input type="hidden" name="currency_code" value="' . $dp_shopping_cart_settings['tco_currency'] . '"/>
                     <input type="hidden" name="no_note" value="1" />
					 <input type="hidden" name="total" value="' . $dpsc_total . '"/>
                     <input type="hidden" name="cart_order_id" value="' . $invoice . '">';
        $dpsc_count_product = 1;
        $tax_rate = 0;
        $dpsc_shipping_total = 0.00;
        if ($dp_shopping_cart_settings['tax'] > 0) {
            $tax_rate = $dp_shopping_cart_settings['tax'];		
        }
        foreach ($dpsc_products as $dpsc_product) {
            $dpsc_var = '';
            $var_tco_field = '';
            $output .= '<input type="hidden" name="c_name_' . $dpsc_count_product . '" value="' . $dpsc_product['name'] . '"/>';
            $output .= '<input type="hidden" name="c_price_' . $dpsc_count_product . '" value="' . $dpsc_product['price'] . '"/>';
            $output .= '<input type="hidden" name="c_prod_' . $dpsc_count_product . '" value="' . $dpsc_product['name'] . ',' . $dpsc_product['quantity'] . '"/>';
            $output .= '<input type="hidden" name="c_description_' . $dpsc_count_product . '" value="' . $dpsc_product['name'] . '"/>';
            $dpsc_count_product++;
        }
        if ($dpsc_shipping_value > 0) {
            $dpsc_shipping_total = $conversion_rate * $dpsc_shipping_value;
        }
        $output .= '<input type="hidden" name="handling_cart" value="' . number_format($dpsc_shipping_total, 2) . '"/></form>';
    }
    return $output;
}

/**
 * This function generates PayPal form
 *
 */
function dpsc_paypal_payment($dpsc_total = FALSE, $dpsc_shipping_value = FALSE, $dpsc_discount_value = FALSE, $invoice = FALSE) {
    $dpsc_products = $_SESSION['dpsc_products'];
    $output = '';
    if (is_array($dpsc_products) && count($dpsc_products) > 0) {
        $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
        if ($dp_shopping_cart_settings['dp_shop_paypal_use_sandbox'] == "checked") {
            $dpsc_form_action = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $dpsc_form_action = 'https://www.paypal.com/cgi-bin/webscr';
        }
        $ipn_path = get_option('siteurl') . "/?paypal_ipn=true";
        $return_path = $dp_shopping_cart_settings['thank_you'];
        $check_return_path = explode('?', $return_path);
        if (count($check_return_path) > 1) {
            $return_path .= '&id=' . $invoice;
        } else {
            $return_path .= '?id=' . $invoice;
        }
        $conversion_rate = 1;
        if ($dp_shopping_cart_settings['paypal_currency'] != $dp_shopping_cart_settings['dp_shop_currency']) {
            $curr = new DP_CURRENCYCONVERTER();
            $conversion_rate = $curr->convert(1, $dp_shopping_cart_settings['paypal_currency'], $dp_shopping_cart_settings['dp_shop_currency']);
        }
        $output = '<form name="dpsc_paypal_form" id="dpsc_payment_form" action="' . $dpsc_form_action . '" method="post">';
        $output .= '<input type="hidden" name="return" value="' . $return_path . '"/>
                     <input type="hidden" name="cmd" value="_ext-enter" />
                     <input type="hidden" name="notify_url" value="' . $ipn_path . '"/>
                     <input type="hidden" name="redirect_cmd" value="_cart" />
                     <input type="hidden" name="business" value="' . $dp_shopping_cart_settings['dp_shop_paypal_id'] . '"/>
                     <input type="hidden" name="cancel_return" value="' . $return_path . '&status=cancel"/>
                     <input type="hidden" name="rm" value="2" />
                     <input type="hidden" name="upload" value="1" />
                     <input type="hidden" name="currency_code" value="' . $dp_shopping_cart_settings['paypal_currency'] . '"/>
                     <input type="hidden" name="no_note" value="1" />
                     <input type="hidden" name="invoice" value="' . $invoice . '">';
        $dpsc_count_product = 1;
        $tax_rate = 0;
        $dpsc_shipping_total = 0.00;
        if ($dp_shopping_cart_settings['tax'] > 0) {
            $tax_rate = $dp_shopping_cart_settings['tax'];
        }
        foreach ($dpsc_products as $dpsc_product) {
            $dpsc_var = '';
            $var_paypal_field = '';
            if (!empty($dpsc_product['var'])) {
                $dpsc_var = ' (' . $dpsc_product['var'] . ')';
                $var_paypal_field = '<input type="hidden" name="on0_' . $dpsc_count_product . '" value="Variation Selected" />
                                     <input type="hidden" name="os0_' . $dpsc_count_product . '" value="' . $dpsc_var . '"  />';
            }
            $output .= '<input type="hidden" name="item_name_' . $dpsc_count_product . '" value="' . $dpsc_product['name'] . $dpsc_var . '"/>
                             <input type="hidden" name="amount_' . $dpsc_count_product . '" value="' . number_format($conversion_rate * $dpsc_product['price'], 2) . '"/>
                             <input type="hidden" name="quantity_' . $dpsc_count_product . '" value="' . $dpsc_product['quantity'] . '"/>
                             <input type="hidden" name="item_number_' . $dpsc_count_product . '" value="' . $dpsc_product['item_number'] . '"/>
                             <input type="hidden" name="tax_rate_' . $dpsc_count_product . '" value="' . $tax_rate . '"/>'
                    . $var_paypal_field;
            if ($dp_shopping_cart_settings['discount_enable'] === 'true' && $dpsc_discount_value) {
                $output .= '<input type="hidden" name="discount_rate_' . $dpsc_count_product . '" value="' . $dpsc_discount_value . '">';
            }
            $dpsc_count_product++;
        }
        if ($dpsc_shipping_value > 0) {
            $dpsc_shipping_total = $conversion_rate * $dpsc_shipping_value;
        }
        $output .= '<input type="hidden" name="handling_cart" value="' . number_format($dpsc_shipping_total, 2) . '"/></form>';
    }
    return $output;
}

/**
 * This function generates authorize.net form
 *
 */
function dpsc_authorize_payment($dpsc_total = FALSE, $dpsc_shipping_value = FALSE, $dpsc_discount_value = FALSE, $invoice = FALSE, $bfname = FALSE, $blname = FALSE, $bcity = FALSE, $baddress = FALSE, $bstate = FALSE, $bzip = FALSE, $bcountry = FALSE, $bemail = FALSE) {
    $dpsc_products = $_SESSION['dpsc_products'];
    $output = '';
    if ($dpsc_total) {
        $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
        if ($dp_shopping_cart_settings['authorize_url'] == "live") {
            $dpsc_form_action = 'https://secure.authorize.net/gateway/transact.dll';
        } else {
            $dpsc_form_action = 'https://test.authorize.net/gateway/transact.dll';
        }
        $total_tax = 0.00;
        $total_discount = 0.00;
        $total_shipping = 0.00;
        if ($dp_shopping_cart_settings['discount_enable'] === 'true' && $dpsc_discount_value) {
            $total_discount = $dpsc_total * $dpsc_discount_value / 100;
        }
        if ($dp_shopping_cart_settings['tax'] > 0) {
            $tax_rate = $dp_shopping_cart_settings['tax'];
            $total_tax = ($dpsc_total - $total_discount) * $tax_rate / 100;
        }
        if ($dpsc_shipping_value) {
            $total_shipping = $dpsc_shipping_value;
        }
        $conversion_rate = 1;
        if ($dp_shopping_cart_settings['dp_shop_currency'] != 'USD') {
            $curr = new DP_CURRENCYCONVERTER();
            $conversion_rate = $curr->convert(1, 'USD', $dp_shopping_cart_settings['dp_shop_currency']);
        }
        $total_amount = ($dpsc_total + $total_tax + $total_shipping - $total_discount) * $conversion_rate;
         $dpsc_total = number_format($total_amount, 2, '.', '');
        $sequence = rand(1, 1000);
        $timeStamp = time();
        $return_path = $dp_shopping_cart_settings['thank_you'];
        $check_return_path = explode('?', $return_path);
        if (count($check_return_path) > 1) {
            $return_path .= '&id=' . $invoice;
        } else {
            $return_path .= '?id=' . $invoice;
        }
        if (phpversion() >= '5.1.2') {
            $fingerprint = hash_hmac("md5", $dp_shopping_cart_settings['authorize_api'] . "^" . $sequence . "^" . $timeStamp . "^" . $dpsc_total . "^", $dp_shopping_cart_settings['authorize_transaction_key']);
        } else {
            $fingerprint = bin2hex(mhash(MHASH_MD5, $dp_shopping_cart_settings['authorize_api'] . "^" . $sequence . "^" . $timeStamp . "^" . $dpsc_total . "^", $dp_shopping_cart_settings['authorize_transaction_key']));
        }
        $ipn_path = get_option('siteurl') . "/?auth_ipn=true";
        $output .= '<form name="dpsc_authorize_form" id="dpsc_payment_form" action="' . $dpsc_form_action . '" method="post">';
        $output .= '<input type="hidden" name="x_login" value="' . $dp_shopping_cart_settings['authorize_api'] . '" />';
        $output .= '<input type="hidden" name="x_version" value="3.1" />';
        $output .= '<input type="hidden" name="x_method" value="CC" />';
        $output .= '<input type="hidden" name="x_type" value="AUTH_CAPTURE" />';
        $output .= '<input type="hidden" name="x_amount" value="' . $dpsc_total . '" />';
        $output .= '<input type="hidden" name="x_description" value="Your Order No.: ' . $invoice . '" />';
        $output .= '<input type="hidden" name="x_invoice_num" value="' . $invoice . '" />';
        $output .= '<input type="hidden" name="x_fp_sequence" value="' . $sequence . '" />';
        $output .= '<input type="hidden" name="x_fp_timestamp" value="' . $timeStamp . '" />';
        $output .= '<input type="hidden" name="x_fp_hash" value="' . $fingerprint . '" />';
        $output .= '<input type="hidden" name="x_test_request" value="' . $dp_shopping_cart_settings['authorize_test_request'] . '" />';
        $output .= '<input type="hidden" name="x_show_form" value="PAYMENT_FORM" />';
        $output .= '<input type="hidden" name="x_relay_response" value="TRUE" />';
//      $output .= '<input type="hidden" name="x_relay_url" value="' . $ipn_path . '" />';
        $output .= '<input type="hidden" name="x_receipt_link_method" value="LINK" />';
        $output .= '<input type="hidden" name="x_receipt_link_text" value="Back to Shop" />';
        $output .= '<input type="hidden" name="x_receipt_link_URL" value="' . $return_path . '" />';
        $output .= '<input type="hidden" name="x_first_name" value="' . $bfname . '" />';
        $output .= '<input type="hidden" name="x_last_name" value="' . $blname . '" />';
        $output .= '<input type="hidden" name="x_address" value="' . $baddress . '" />';
        $output .= '<input type="hidden" name="x_zip" value="' . $bzip . '" />';
        $output .= '<input type="hidden" name="x_city" value="' . $bcity . '" />';
        $output .= '<input type="hidden" name="x_country" value="' . $bcountry . '" />';
        $output .= '<input type="hidden" name="x_email" value="' . $bemail . '" />';
        $output .= '</form>';
    }
    return $output;
}

/**
 * This function generates WorldPay form
 *
 */
function dpsc_worldpay_payment($dpsc_total = FALSE, $dpsc_shipping_value = FALSE, $dpsc_discount_value = FALSE, $invoice = FALSE, $bfname = FALSE, $blname = FALSE, $bcity = FALSE, $baddress = FALSE, $bstate = FALSE, $bzip = FALSE, $bcountry = FALSE, $bemail = FALSE) {
    $output = '';
    if ($dpsc_total) {
        $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
        if ($dp_shopping_cart_settings['worldpay_testmode'] === 'test') {
            $dpsc_form_action = 'https://select-test.worldpay.com/wcc/purchase';
            $testModeVal = '100';
            $name = 'AUTHORISED';
        } else {
            $dpsc_form_action = 'https://select.worldpay.com/wcc/purchase';
            $testModeVal = '0';
            $name = $bfname . ' ' . $blname;
        }
        $total_tax = 0.00;
        $total_discount = 0.00;
        $total_shipping = 0.00;
        if ($dp_shopping_cart_settings['discount_enable'] === 'true' && $dpsc_discount_value) {
            $total_discount = $dpsc_total * $dpsc_discount_value / 100;
        }
        if ($dp_shopping_cart_settings['tax'] > 0) {
            $tax_rate = $dp_shopping_cart_settings['tax'];
            $total_tax = ($dpsc_total - $total_discount) * $tax_rate / 100;
        }
        if ($dpsc_shipping_value) {
            $total_shipping = $dpsc_shipping_value;
        }
        $return_path = $dp_shopping_cart_settings['thank_you'];
        $check_return_path = explode('?', $return_path);
        if (count($check_return_path) > 1) {
            $return_path .= '&id=' . $invoice;
        } else {
            $return_path .= '?id=' . $invoice;
        }
        $conversion_rate = 1;
        if ($dp_shopping_cart_settings['worldpay_currency'] != $dp_shopping_cart_settings['dp_shop_currency']) {
            $curr = new DP_CURRENCYCONVERTER();
            $conversion_rate = $curr->convert(1, $dp_shopping_cart_settings['worldpay_currency'], $dp_shopping_cart_settings['dp_shop_currency']);
        }
        $total_amount = ($dpsc_total + $total_tax + $total_shipping - $total_discount) * $conversion_rate;
        $dpsc_total = number_format($total_amount, 2, '.', '');
        $lang = (strlen(WPLANG) > 0 ? substr(WPLANG, 0, 2) : 'en');
        $output = '<form name="dpsc_worldpay_form" id="dpsc_payment_form" action="' . $dpsc_form_action . '" method="post">
                        <input type="hidden" name="instId" value="' . $dp_shopping_cart_settings['worldpay_id'] . '" />
                        <input type="hidden" name="currency" value="' . $dp_shopping_cart_settings['worldpay_currency'] . '" />
                        <input type="hidden" name="desc" value="Your Order No.: ' . $invoice . '" />
                        <input type="hidden" name="cartId" value="101KT0098" />
                        <input type="hidden" name="amount" value="' . $dpsc_total . '" />
                        <input type="hidden" name="testMode" value="' . $testModeVal . '" />
                        <input type="hidden" name="name" value="' . $name . '" />
                        <input type="hidden" name="address" value="' . $baddress . ' ' . $bcity . ' ' . $bstate . '" />
                        <input type="hidden" name="postcode" value="' . $bzip . '" />
                        <input type="hidden" name="country" value="' . $bcountry . '" />
                        <input type="hidden" name="tel" value="" />
                        <input type="hidden" name="email" value="' . $bemail . '" />
                        <input type="hidden" name="lang" value="' . $lang . '" />
                        <input type="hidden" name="MC_invoice" value="' . $invoice . '" />
                        <input type="hidden" name="MC_callback" value="' . $return_path . '" />
                    </form>';
    }
    return $output;
}

/**
 * This function generates AlertPay form
 *
 */
function dpsc_alertpay_payment($dpsc_total = FALSE, $dpsc_shipping_value = FALSE, $dpsc_discount_value = FALSE, $invoice = FALSE, $bfname = FALSE, $blname = FALSE, $bcity = FALSE, $baddress = FALSE, $bstate = FALSE, $bzip = FALSE, $bcountry = FALSE, $bemail = FALSE) {
    $output = '';
    if ($dpsc_total) {
        $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
        $total_tax = 0.00;
        $total_discount = 0.00;
        $total_shipping = 0.00;
        if ($dp_shopping_cart_settings['discount_enable'] === 'true' && $dpsc_discount_value) {
            $total_discount = $dpsc_total * $dpsc_discount_value / 100;
        }
        if ($dp_shopping_cart_settings['tax'] > 0) {
            $tax_rate = $dp_shopping_cart_settings['tax'];
            $total_tax = ($dpsc_total - $total_discount) * $tax_rate / 100;
        }
        if ($dpsc_shipping_value) {
            $total_shipping = $dpsc_shipping_value;
        }
//        $dpsc_total = number_format($dpsc_total+$total_tax+$total_shipping-$total_discount,2);
        $dpsc_total = number_format($dpsc_total, 2, '.', '');
        $total_shipping = number_format($total_shipping, 2, '.', '');
        $total_tax = number_format($total_tax, 2, '.', '');
        $total_discount = number_format($total_discount, 2, '.', '');
        $return_path = $dp_shopping_cart_settings['thank_you'];
        $check_return_path = explode('?', $return_path);
        if (count($check_return_path) > 1) {
            $return_path .= '&id=' . $invoice;
        } else {
            $return_path .= '?id=' . $invoice;
        }
        $conversion_rate = 1;
        if ($dp_shopping_cart_settings['alertpay_currency'] != $dp_shopping_cart_settings['dp_shop_currency']) {
            $curr = new DP_CURRENCYCONVERTER();
            $conversion_rate = $curr->convert(1, $dp_shopping_cart_settings['alertpay_currency'], $dp_shopping_cart_settings['dp_shop_currency']);
        }
        $output = '<form name="dpsc_alertpay_form" id="dpsc_payment_form"  method="post" action="https://www.alertpay.com/PayProcess.aspx" >
                        <input type="hidden" name="ap_merchant" value="' . $dp_shopping_cart_settings['alertpay_id'] . '" />
                        <input type="hidden" name="ap_purchasetype" value="item-goods" />
                        <input type="hidden" name="ap_currency" value="' . $dp_shopping_cart_settings['alertpay_currency'] . '" />
                        <input type="hidden" name="ap_itemname" value="Your Order No.: ' . $invoice . '" />
                        <input type="hidden" name="ap_amount" value="' . number_format($conversion_rate * $dpsc_total, 2, '.', '') . '" />
                        <input type="hidden" name="ap_shippingcharges" value="' . number_format($conversion_rate * $total_shipping, 2, '.', '') . '" />
                        <input type="hidden" name="ap_taxamount" value="' . number_format($conversion_rate * $total_tax, 2, '.', '') . '" />
                        <input type="hidden" name="ap_discountamount" value="' . number_format($conversion_rate * $total_discount, 2, '.', '') . '" />
                        <input type="hidden" name="ap_returnurl" value="' . $return_path . '" />
                        <input type="hidden" name="ap_cancelurl" value="' . $return_path . '&status=cancel"/>
                        <input type="hidden" name="ap_fname" value="' . $bfname . '" />
                        <input type="hidden" name="ap_lname" value="' . $blname . '" />
                        <input type="hidden" name="ap_contactemail" value="' . $bemail . '" />
                        <input type="hidden" name="ap_addressline1" value="' . $baddress . '" />
                        <input type="hidden" name="ap_city" value="' . $bcity . '" />
                        <input type="hidden" name="ap_stateprovince" value="QC" />
                        <input type="hidden" name="ap_zippostalcode" value="' . $bzip . '" />
                        <input type="hidden" name="ap_country" value="' . $bcountry . '" />
                  </form>';
    }
    return $output;
}

/**
 * This function generates form for the other payment form
 *
 */
function dpsc_other_payment($invoice = FALSE) {
    $output = '';
    $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
    $return_path = $dp_shopping_cart_settings['thank_you'];
    $check_return_path = explode('?', $return_path);
    if (count($check_return_path) > 1) {
        $return_path .= '&id=' . $invoice;
    } else {
        $return_path .= '?id=' . $invoice;
    }
    $output = '<form name="dpsc_other_form" id="dpsc_payment_form" action="' . $return_path . '" method="post">
                    <input type="hidden" name="just_for_the_sake_of_it" value="hmm" />
                </form>';
    return $output;
}

/**
 * This function handles the IPN from PayPal
 *
 */
if ($_REQUEST['paypal_ipn'] === 'true') {
    add_action('init', 'dpsc_paypal_ipn');
}

function dpsc_paypal_ipn() {
    global $wpdb;
    if ($_REQUEST['paypal_ipn'] === 'true') {
        $req = 'cmd=_notify-validate';
        foreach ($_POST as $key => $value) {
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
        }
        $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
        if ($dp_shopping_cart_settings['dp_shop_paypal_use_sandbox'] == "checked") {
            $dpsc_form_action = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $dpsc_form_action = 'https://www.paypal.com/cgi-bin/webscr';
        }
        $ch = curl_init($dpsc_form_action);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        $result = curl_exec($ch);
        curl_close($ch);
        if (strcmp($result, "VERIFIED") == 0) {
            $invoice = $_POST['invoice'];
            $tx_id = $_POST['txn_id'];
            $payer_email = $_POST['payer_email'];
            $payment_status = $_POST['payment_status'];
            switch ($payment_status) {
                case 'Processed':
                    $updated_status = 'Paid';
                    break;
                case 'Completed':
                    $updated_status = 'Paid';
                    break;
                case 'Pending':
                    $updated_status = 'Pending';
                    break;
                default:
                    $updated_status = 'Canceled';
                    break;
            }
            $table_name = $wpdb->prefix . "dpsc_transactions";
            $update_query = "UPDATE {$table_name} SET `tx_id`='{$tx_id}', `payer_email`='{$payer_email}', `payment_status`='{$updated_status}' WHERE `invoice`='{$invoice}'";
            $wpdb->query($update_query);
            if ($payment_status === 'Processed' || $payment_status === 'Completed') {
                $message = '';
                $digital_message = '';
                $check_query = "SELECT * FROM {$table_name} WHERE `invoice`='{$invoice}'";
                $result = $wpdb->get_row($check_query);
                $pay_option = $result->payment_option;
                if ($dp_shopping_cart_settings['dp_shop_user_registration'] === 'checked') {
                    if ($pay_option == 'PayPal Buy Now') {
                        $user_id = email_exists($payer_email);
                        if (!$user_id) {
//                            require_once( ABSPATH . WPINC . '/registration.php');
                            $user_pass = wp_generate_password();
                            $user_id = wp_create_user($payer_email, $user_pass, $payer_email);
                            update_user_option($user_id, 'default_password_nag', true, true);
                            dp_new_user_notification($user_id, $user_pass);
                            update_user_meta($user_id, 'first_name', $_POST['first_name']);
                            update_user_meta($user_id, 'last_name', $_POST['last_name']);
                        }
                        $update_query = "UPDATE {$table_name} SET `billing_first_name`='{$_POST['first_name']}', `billing_last_name`='{$_POST['last_name']}', `billing_email`='{$payer_email}' WHERE `invoice`='{$invoice}'";
                        $wpdb->query($update_query);
                        $user_invoice = get_user_meta($user_id, 'dp_user_invoice_number', TRUE);
                        if ($user_invoice === '') {
                            $user_invoice = array();
                        }
                        $user_invoice[] = $invoice;
                        update_user_meta($user_id, 'dp_user_invoice_number', $user_invoice);
                    }
                }

                $result = $wpdb->get_row($check_query);
                $is_digital = dpsc_pnj_is_digital_present($result->products);
                if ($is_digital) {
                    $file_names = dpsc_pnj_get_download_links($is_digital);
                    if ($file_names) {
                        if (is_array($file_names) && count($file_names) > 0) {
                            $digital_message .= '<br/>Your download links:<br/><ul>';
                            foreach ($file_names as $file_name) {
                                $file_name = explode('@_@||@_@', $file_name);
                                $temp_name = $file_name[0];
                                $real_name = $file_name[1];
                                $digital_message .= '<li><a href="' . DP_PLUGIN_URL . '/download.php?id=' . $temp_name . '">' . $real_name . '</a></li>';
                            }
                            $digital_message .= '</ul><br/>';
                        }
                    }
                }
                $email_fname = $result->billing_first_name ;
                $email_shop_name = $dp_shopping_cart_settings['shop_name'];
                $to = $result->billing_email;
                $from = get_option('admin_email');


                $nme_dp_mail_option = get_option('dp_usr_payment_mail', true);

                $message = $nme_dp_mail_option['dp_usr_payment_mail_body'];
                $subject = $nme_dp_mail_option['dp_usr_payment_mail_title'];

                $find = array('%fname%', '%status%', '%email%', '%inv%', '%digi%', '%shop%');
                $replace = array($email_fname, $updated_status, $to, $invoice, $digital_message, $email_shop_name);
                $message = str_replace($find, $replace, $message);
//email to payer
                update_option('debug_digital_mail_user', $message);
                dpsc_pnj_send_mail($to, $from, $dp_shopping_cart_settings['shop_name'], $subject, $message);
                
                $nme_dp_mail_option = get_option('dp_admin_payment_mail', true);

                $message = $nme_dp_mail_option['dp_admin_payment_mail_body'];
                $message = str_replace("\r",'<br>', $message);
                $subject = $nme_dp_mail_option['dp_usr_admin_payment_mail_title'];

                $array1 = array('%fname%', '%status%', '%email%', '%inv%', '%digi%', '%shop%');
                $array2 = array($email_fname, $updated_status, $to, $invoice, $digital_message, $email_shop_name);
                $message = str_replace($array1, $array2, $message);
//email to admin
                update_option('debug_digital_mail_admin', $message);
                dpsc_pnj_send_mail($from, $to, $dp_shopping_cart_settings['shop_name'], $subject, $message);
            }
            
//          Mail for Cancelled Order
            
            if ($payment_status === 'Canceled') {
                $message = '';
                $digital_message = '';
                $check_query = "SELECT * FROM {$table_name} WHERE `invoice`='{$invoice}'";
                $result = $wpdb->get_row($check_query);
                $email_fname = $result->billing_first_name ;
                $email_lname =$result->billing_last_name;
                $email_shop_name = $dp_shopping_cart_settings['shop_name'];
                $to = $result->billing_email;
                
                $from = get_option('admin_email');
                $site_url = get_bloginfo('url');
                $transaction_log =$site_url.'/wp-admin/admin.php?page=dukapress-shopping-cart-order-log&id='.$invoice;


                $nme_dp_mail_option = get_option('dp_order_cancelled_mail_user_options', true);

                $message = $nme_dp_mail_option['dp_order_canncelled_send_mail_user_body'];
                $subject = $nme_dp_mail_option['dp_order_cancelled_send_mail_user_title'];

                $find = array('%fname%','%lname%','%inv%', '%status%', '%shop%');
                $replace = array($email_fname, $email_lname,$updated_status,$invoice, $payment_status,$email_shop_name);
                $message = str_replace($find, $replace, $message);
//email to payer
                update_option('debug_digital_mail_user', $message);
                dpsc_pnj_send_mail($to, $from, $dp_shopping_cart_settings['shop_name'], $subject, $message);
                
                $nme_dp_mail_option = get_option('dp_order_cancelled_mail_options', true);

                $message = $nme_dp_mail_option['dp_order_cancelled_send_mail_body'];
                $message = str_replace("\r",'<br>', $message);
                $subject = $nme_dp_mail_option['dp_order_send_mail_title'];

                $array1 = array('%fname%', '%status%', '%email%', '%inv%', '%digi%', '%shop%','%order-log-transaction%');
                $array2 = array($email_fname, $updated_status, $to, $invoice, $digital_message, $email_shop_name,$transaction_log);
                $message = str_replace($array1, $array2, $message);
//email to admin
                update_option('debug_digital_mail_admin', $message);
                dpsc_pnj_send_mail($from, $to, $dp_shopping_cart_settings['shop_name'], $subject, $message);
            }
        }
    }
}

/**
 * This function handles the IPN from Authorize.net
 *
 */
if ($_REQUEST['auth_ipn'] === 'true') {
    add_action('init', 'dpsc_auth_ipn');
}

function dpsc_auth_ipn() {
    global $wpdb;
    $payment_status = intval($_POST['x_response_code']);
    $invoice = $_POST['x_invoice_num'];
    $payer_email = $_POST['x_email'];
    switch ($payment_status) {
        case 1:
            $updated_status = 'Paid';
            break;
        case 2:
            $updated_status = 'Canceled';
            break;
        case 3:
            $updated_status = 'Canceled';
            break;
        case 4:
            $updated_status = 'Pending';
            break;
        default:
            $updated_status = 'Canceled';
            break;
    }
    $table_name = $wpdb->prefix . "dpsc_transactions";
    $update_query = "UPDATE {$table_name} SET `payer_email`='{$payer_email}', `payment_status`='{$updated_status}'WHERE `invoice`='{$invoice}'";
    $wpdb->query($update_query);
    $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
    if ($payment_status === 1) {
        $message = '';
        $digital_message = '';
        $check_query = "SELECT * FROM {$table_name} WHERE `invoice`='{$invoice}'";
        $result = $wpdb->get_row($check_query);
        $is_digital = dpsc_pnj_is_digital_present($result->products);
        if ($is_digital) {
            $file_names = dpsc_pnj_get_download_links($is_digital);
            if ($file_names) {
                if (is_array($file_names) && count($file_names) > 0) {
                    $digital_message .= '<br/>Your download links:<br/><ul>';
                    foreach ($file_names as $file_name) {
                        $file_name = explode('@_@||@_@', $file_name);
                        $temp_name = $file_name[0];
                        $real_name = $file_name[1];
                        $digital_message .= '<li><a href="' . DP_PLUGIN_URL . '/download.php?id=' . $temp_name . '">' . $real_name . '</a></li>';
                    }
                    $digital_message .= '</ul><br/>';
                }
            }
        }
//        $message = 'Hi ' . $result->billing_first_name . ',<br/>
//                    We have received the payment for Invoice No.: ' . $invoice . '.<br/>
//                    We will start processing your order soon.<br/>' . $digital_message . '
//					<br/><br/>
//					Thanks,<br/>
//                    ' . $dp_shopping_cart_settings['shop_name'];
//        $subject = 'Payment Received For Invoice No: ' . $invoice;
//        $to = $result->billing_email;
//        $from = get_option('admin_email');
//        dpsc_pnj_send_mail($to, $from, $dp_shopping_cart_settings['shop_name'], $subject, $message);
//        dpsc_pnj_send_mail($from, $to, $dp_shopping_cart_settings['shop_name'], $subject, $subject);

        $email_fname = $result->billing_first_name ;
        $email_shop_name = $dp_shopping_cart_settings['shop_name'];
        $to = $result->billing_email;
        $from = get_option('admin_email');


        $nme_dp_mail_option = get_option('dp_usr_payment_mail', true);

        $message = $nme_dp_mail_option['dp_usr_payment_mail_body'];
        $subject = $nme_dp_mail_option['dp_usr_payment_mail_title'];

        $find_tag = array('%fname%', '%status%', '%email%', '%inv%', '%digi%', '%shop%');
        $rep_tag = array($email_fname, $updated_status, $to, $invoice, $digital_message, $email_shop_name);
        $message =str_replace($find_tag, $rep_tag, $message);
        //email to payer
        dpsc_pnj_send_mail($to, $from, $dp_shopping_cart_settings['shop_name'], $subject, $message);

        $nme_dp_mail_option = get_option('dp_admin_payment_mail', true);
        $message = $nme_dp_mail_option['dp_admin_payment_mail_body'];
        $message = str_replace("\r",'<br>', $message);
        $subject = $nme_dp_mail_option['dp_usr_admin_payment_mail_title'];
        
        $find_tag = array('%fname%', '%status%', '%email%', '%inv%', '%digi%', '%shop%');
        $rep_tag = array($email_fname, $updated_status, $to, $invoice, $digital_message, $email_shop_name);
        $message = str_replace($find_tag, $rep_tag, $message);
        //email to admin
        dpsc_pnj_send_mail($from, $to, $dp_shopping_cart_settings['shop_name'], $subject, $message);
    }
    if ($updated_status === 'Canceled') {
        $message = '';
        $digital_message = '';
        $check_query = "SELECT * FROM {$table_name} WHERE `invoice`='{$invoice}'";
        $result = $wpdb->get_row($check_query);

        $email_fname = $result->billing_first_name ;
        $email_shop_name = $dp_shopping_cart_settings['shop_name'];
        $to = $result->billing_email;
        $from = get_option('admin_email');

//email to user on cancelled order
        $nme_dp_mail_option = get_option('dp_order_cancelled_mail_user_options', true);

        $message = $nme_dp_mail_option['dp_order_canncelled_send_mail_user_body'];
        $subject = $nme_dp_mail_option['dp_order_cancelled_send_mail_user_title'];

        $find_tag = array('%fname%', '%status%', '%email%', '%inv%', '%digi%', '%shop%');
        $rep_tag = array($email_fname, $updated_status, $to, $invoice, $digital_message, $email_shop_name);
        $message =str_replace($find_tag, $rep_tag, $message);
//email to admin on cannceled order        
        dpsc_pnj_send_mail($to, $from, $dp_shopping_cart_settings['shop_name'], $subject, $message);

        $nme_dp_mail_option = get_option('dp_order_cancelled_mail_options', true);
        $message = $nme_dp_mail_option['dp_order_cancelled_send_mail_body'];
        $message = str_replace("\r",'<br>', $message);
        $subject = $nme_dp_mail_option['dp_order_cancelled_send_mail_title'];
        
        $find_tag = array('%fname%', '%status%', '%email%', '%inv%', '%digi%', '%shop%','%order-log-transaction%');
        $rep_tag = array($email_fname, $updated_status, $to, $invoice, $digital_message, $email_shop_name,$transaction_log);
        $message = str_replace($find_tag, $rep_tag, $message);
        //email to admin
        dpsc_pnj_send_mail($from, $to, $dp_shopping_cart_settings['shop_name'], $subject, $message);
    }
    $return_path = $dp_shopping_cart_settings['thank_you'];
    $check_return_path = explode('?', $return_path);
    if (count($check_return_path) > 1) {
        $return_path .= '&id=' . $invoice;
    } else {
        $return_path .= '?id=' . $invoice;
    }
    header("Location: $return_path");
    // echo 'zzzzzzzzzz';
}

if ($_REQUEST['credit_card_processed'] === 'Y') {
    add_action('init', 'dpsc_tco_ipn');
}

function dpsc_tco_ipn() {
    global $wpdb;
	$dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
    $order_number = $_REQUEST['order_number'];
	$total = $_REQUEST['total'];
    $invoice = $_REQUEST['cart_order_id'];
    $payer_email = $_REQUEST['email'];
	$updated_status = 'Paid';
  
  // In Demo Mode the MD5 Hash is built using a "1"
  // Concat some variables for MD5 Hashing (like 2Checkout does online)
    if($_REQUEST['demo'] == "Y") {
        $order_number = 1;
	}
  $compare_string = $dp_shopping_cart_settings['tco_secret_word'] . $dp_shopping_cart_settings['tco_id'] . $order_number . $total;
  
  // make it md5
  $compare_hash1 = strtoupper(md5($compare_string));
  $compare_hash2 = $_REQUEST['key'];

  /* If both hashes are the same, the post should come from 2Checkout */
  if ($compare_hash1 == $compare_hash2) {
    $table_name = $wpdb->prefix . "dpsc_transactions";
    $update_query = "UPDATE {$table_name} SET `payer_email`='{$payer_email}', `payment_status`='{$updated_status}'WHERE `invoice`='{$invoice}'";
    $wpdb->query($update_query);
    $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
    if ($payment_status === 1) {
        $message = '';
        $digital_message = '';
        $check_query = "SELECT * FROM {$table_name} WHERE `invoice`='{$invoice}'";
        $result = $wpdb->get_row($check_query);
        $is_digital = dpsc_pnj_is_digital_present($result->products);
        if ($is_digital) {
            $file_names = dpsc_pnj_get_download_links($is_digital);
            if ($file_names) {
                if (is_array($file_names) && count($file_names) > 0) {
                    $digital_message .= '<br/>Your download links:<br/><ul>';
                    foreach ($file_names as $file_name) {
                        $file_name = explode('@_@||@_@', $file_name);
                        $temp_name = $file_name[0];
                        $real_name = $file_name[1];
                        $digital_message .= '<li><a href="' . DP_PLUGIN_URL . '/download.php?id=' . $temp_name . '">' . $real_name . '</a></li>';
                    }
                    $digital_message .= '</ul><br/>';
                }
            }
        }
//        $message = 'Hi ' . $result->billing_first_name . ',<br/>
//                    We have received the payment for Invoice No.: ' . $invoice . '.<br/>
//                    We will start processing your order soon.<br/>' . $digital_message . '
//					<br/><br/>
//					Thanks,<br/>
//                    ' . $dp_shopping_cart_settings['shop_name'];
//        $subject = 'Payment Received For Invoice No: ' . $invoice;
//        $to = $result->billing_email;
//        $from = get_option('admin_email');
//        dpsc_pnj_send_mail($to, $from, $dp_shopping_cart_settings['shop_name'], $subject, $message);
//        dpsc_pnj_send_mail($from, $to, $dp_shopping_cart_settings['shop_name'], $subject, $subject);

        $email_fname = $result->billing_first_name ;
        $email_shop_name = $dp_shopping_cart_settings['shop_name'];
        $to = $result->billing_email;
        $from = get_option('admin_email');


        $nme_dp_mail_option = get_option('dp_usr_payment_mail', true);

        $message = $nme_dp_mail_option['dp_usr_payment_mail_body'];
        $subject = $nme_dp_mail_option['dp_usr_payment_mail_title'];

        $find_tag = array('%fname%', '%status%', '%email%', '%inv%', '%digi%', '%shop%');
        $rep_tag = array($email_fname, $updated_status, $to, $invoice, $digital_message, $email_shop_name);
        $message =str_replace($find_tag, $rep_tag, $message);
        //email to payer
        dpsc_pnj_send_mail($to, $from, $dp_shopping_cart_settings['shop_name'], $subject, $message);

        $nme_dp_mail_option = get_option('dp_admin_payment_mail', true);
        $message = $nme_dp_mail_option['dp_admin_payment_mail_body'];
        $message = str_replace("\r",'<br>', $message);
        $subject = $nme_dp_mail_option['dp_usr_admin_payment_mail_title'];
        
        $find_tag = array('%fname%', '%status%', '%email%', '%inv%', '%digi%', '%shop%');
        $rep_tag = array($email_fname, $updated_status, $to, $invoice, $digital_message, $email_shop_name);
        $message = str_replace($find_tag, $rep_tag, $message);
        //email to admin
        dpsc_pnj_send_mail($from, $to, $dp_shopping_cart_settings['shop_name'], $subject, $message);
    }

    $return_path = $dp_shopping_cart_settings['thank_you'];
    $check_return_path = explode('?', $return_path);
    if (count($check_return_path) > 1) {
        $return_path .= '&id=' . $invoice;
    } else {
        $return_path .= '?id=' . $invoice;
    }
    header("Location: $return_path");
    // echo 'zzzzzzzzzz';
	}
}

?>