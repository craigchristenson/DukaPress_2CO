<?php
/*
Plugin Name: DukaPress Shopping Cart
Description: DukaPress Shopping Cart
Version: 2.3.5
Author: NetMadeEz and Nickel Pro
Author URI: http://dukapress.org/
Plugin URI: http://dukapress.org/
*/

$dp_version = 2.35;

require_once('php/dp-products.php');
require_once('php/dp-cart.php');
require_once('php/dp-widgets.php');
require_once('php/dp-payment.php');
require_once('lib/currency_convertor.php');

session_start();
define('DP_PLUGIN_URL', WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)));
define('DP_PLUGIN_DIR', WP_PLUGIN_DIR.'/'.dirname(plugin_basename(__FILE__)));
define('DP_DOWNLOAD_FILES_DIR', WP_CONTENT_DIR. '/uploads/dpsc_download_files/' );
define('DP_DOWNLOAD_FILES_DIR_TEMP', WP_CONTENT_DIR. '/uploads/dpsc_temp_download_files/' );

$dpsc_user_invoice_number = 'dp_user_invoice_number';
$dpsc_country_code_name = array("AF" => "AFGHANISTAN", "AL" => "ALBANIA", "DZ" => "ALGERIA", "AS" => "AMERICAN SAMOA", "AD" => "ANDORRA", "AO" => "ANGOLA", "AI" => "ANGUILLA", "AQ" => "ANTARCTICA", "AG" => "ANTIGUA AND BARBUDA", "AR" => "ARGENTINA", "AM" => "ARMENIA", "AW" => "ARUBA", "AU" => "AUSTRALIA", "AT" => "AUSTRIA", "AZ" => "AZERBAIJAN", "BS" => "BAHAMAS", "BH" => "BAHRAIN", "BD" => "BANGLADESH", "BB" => "BARBADOS", "BY" => "BELARUS", "BE" => "BELGIUM", "BZ" => "BELIZE", "BJ" => "BENIN", "BM" => "BERMUDA", "BT" => "BHUTAN", "BO" => "BOLIVIA", "BA" => "BOSNIA AND HERZEGOVINA", "BW" => "BOTSWANA", "BV" => "BOUVET ISLAND", "BR" => "BRAZIL", "IO" => "BRITISH INDIAN OCEAN TERRITORY", "BN" => "BRUNEI DARUSSALAM", "BG" => "BULGARIA", "BF" => "BURKINA FASO", "BI" => "BURUNDI", "KH" => "CAMBODIA", "CM" => "CAMEROON", "CA" => "CANADA", "CV" => "CAPE VERDE", "KY" => "CAYMAN ISLANDS", "CF" => "CENTRAL AFRICAN REPUBLIC", "TD" => "CHAD", "CL" => "CHILE", "CN" => "CHINA", "CX" => "CHRISTMAS ISLAND", "CC" => "COCOS (KEELING) ISLANDS", "CO" => "COLOMBIA", "KM" => "COMOROS", "CG" => "CONGO", "CD" => "CONGO, THE DEMOCRATIC REPUBLIC OF THE", "CK" => "COOK ISLANDS", "CR" => "COSTA RICA", "CI" => "COTE D?IVOIRE", "HR" => "CROATIA", "CU" => "CUBA", "CY" => "CYPRUS", "CZ" => "CZECH REPUBLIC", "DK" => "DENMARK", "DJ" => "DJIBOUTI", "DM" => "DOMINICA", "DO" => "DOMINICAN REPUBLIC", "EC" => "ECUADOR", "EG" => "EGYPT", "SV" => "EL SALVADOR", "GQ" => "EQUATORIAL GUINEA", "ER" => "ERITREA", "EE" => "ESTONIA", "ET" => "ETHIOPIA", "FK" => "FALKLAND ISLANDS (MALVINAS)", "FO" => "FAROE ISLANDS", "FJ" => "FIJI", "FI" => "FINLAND", "FR" => "FRANCE", "GF" => "FRENCH GUIANA", "PF" => "FRENCH POLYNESIA", "TF" => "FRENCH SOUTHERN TERRITORIES", "GA" => "GABON", "GM" => "GAMBIA", "GE" => "GEORGIA", "DE" => "GERMANY", "GH" => "GHANA", "GI" => "GIBRALTAR", "GR" => "GREECE", "GL" => "GREENLAND", "GD" => "GRENADA", "GP" => "GUADELOUPE", "GU" => "GUAM", "GT" => "GUATEMALA", "GG" => "GUERNSEY", "GN" => "GUINEA", "GW" => "GUINEA-BISSAU", "GY" => "GUYANA", "HT" => "HAITI", "HM" => "HEARD ISLAND AND MCDONALD ISLANDS", "VA" => "HOLY SEE (VATICAN CITY STATE)", "HN" => "HONDURAS", "HK" => "HONG KONG", "HU" => "HUNGARY", "IS" => "ICELAND", "IN" => "INDIA", "ID" => "INDONESIA", "IR" => "IRAN, ISLAMIC REPUBLIC OF", "IQ" => "IRAQ", "IE" => "IRELAND", "IM" => "ISLE OF MAN", "IL" => "ISRAEL", "IT" => "ITALY", "JM" => "JAMAICA", "JP" => "JAPAN", "JE" => "JERSEY", "JO" => "JORDAN", "KZ" => "KAZAKHSTAN", "KE" => "KENYA", "KI" => "KIRIBATI", "KP" => "KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF", "KR" => "KOREA, REPUBLIC OF", "KW" => "KUWAIT", "KG" => "KYRGYZSTAN", "LA" => "LAO PEOPLE\'S DEMOCRATIC REPUBLIC", "LV" => "LATVIA", "LB" => "LEBANON", "LS" => "LESOTHO", "LR" => "LIBERIA", "LY" => "LIBYAN ARAB JAMAHIRIYA", "LI" => "LIECHTENSTEIN", "LT" => "LITHUANIA", "LU" => "LUXEMBOURG", "MO" => "MACAO", "MK" => "MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF", "MG" => "MADAGASCAR", "MW" => "MALAWI", "MY" => "MALAYSIA", "MV" => "MALDIVES", "ML" => "MALI", "MT" => "MALTA", "MH" => "MARSHALL ISLANDS", "MQ" => "MARTINIQUE", "MR" => "MAURITANIA", "MU" => "MAURITIUS", "YT" => "MAYOTTE", "MX" => "MEXICO", "FM" => "MICRONESIA, FEDERATED STATES OF", "MD" => "MOLDOVA, REPUBLIC OF", "MC" => "MONACO", "MN" => "MONGOLIA", "MS" => "MONTSERRAT", "MA" => "MOROCCO", "MZ" => "MOZAMBIQUE", "MM" => "MYANMAR", "NA" => "NAMIBIA", "NR" => "NAURU", "NP" => "NEPAL", "NL" => "NETHERLANDS", "AN" => "NETHERLANDS ANTILLES", "NC" => "NEW CALEDONIA", "NZ" => "NEW ZEALAND", "NI" => "NICARAGUA", "NE" => "NIGER", "NG" => "NIGERIA", "NU" => "NIUE", "NF" => "NORFOLK ISLAND", "MP" => "NORTHERN MARIANA ISLANDS", "NO" => "NORWAY", "OM" => "OMAN", "PK" => "PAKISTAN", "PW" => "PALAU", "PS" => "PALESTINIAN TERRITORY, OCCUPIED", "PA" => "PANAMA", "PG" => "PAPUA NEW GUINEA", "PY" => "PARAGUAY", "PE" => "PERU", "PH" => "PHILIPPINES", "PN" => "PITCAIRN", "PL" => "POLAND", "PT" => "PORTUGAL", "PR" => "PUERTO RICO", "QA" => "QATAR", "RE" => "REUNION", "RO" => "ROMANIA", "RU" => "RUSSIAN FEDERATION", "RW" => "RWANDA", "SH" => "SAINT HELENA", "KN" => "SAINT KITTS AND NEVIS", "LC" => "SAINT LUCIA", "PM" => "SAINT PIERRE AND MIQUELON", "VC" => "SAINT VINCENT AND THE GRENADINES", "WS" => "SAMOA", "SM" => "SAN MARINO", "ST" => "SAO TOME AND PRINCIPE", "SA" => "SAUDI ARABIA", "SN" => "SENEGAL", "CS" => "SERBIA AND MONTENEGRO", "SC" => "SEYCHELLES", "SL" => "SIERRA LEONE", "SG" => "SINGAPORE", "SK" => "SLOVAKIA", "SI" => "SLOVENIA", "SB" => "SOLOMON ISLANDS", "SO" => "SOMALIA", "ZA" => "SOUTH AFRICA", "GS" => "SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS", "ES" => "SPAIN", "LK" => "SRI LANKA", "SD" => "SUDAN", "SR" => "SURINAME", "SJ" => "SVALBARD AND JAN MAYEN", "SZ" => "SWAZILAND", "SE" => "SWEDEN", "CH" => "SWITZERLAND", "SY" => "SYRIAN ARAB REPUBLIC", "TW" => "TAIWAN, PROVINCE OF CHINA", "TJ" => "TAJIKISTAN", "TZ" => "TANZANIA, UNITED REPUBLIC OF", "TH" => "THAILAND", "TL" => "TIMOR-LESTE  ", "TG" => "TOGO", "TK" => "TOKELAU", "TO" => "TONGA", "TT" => "TRINIDAD AND TOBAGO", "TN" => "TUNISIA", "TR" => "TURKEY", "TM" => "TURKMENISTAN", "TC" => "TURKS AND CAICOS ISLANDS", "TV" => "TUVALU", "UG" => "UGANDA", "UA" => "UKRAINE", "AE" => "UNITED ARAB EMIRATES", "GB" => "UNITED KINGDOM", "UM" => "UNITED STATES MINOR OUTLYING ISLANDS", "US" => "UNITED STATES OF AMERICA", "UY" => "URUGUAY", "UZ" => "UZBEKISTAN", "VU" => "VANUATU", "VE" => "VENEZUELA", "VN" => "VIET NAM", "VG" => "VIRGIN ISLANDS, BRITISH", "VI" => "VIRGIN ISLANDS, U.S.", "WF" => "WALLIS AND FUTUNA", "EH" => "WESTERN SAHARA", "YE" => "YEMEN", "ZM" => "ZAMBIA", "ZW" => "ZIMBABWE");

/**
 * This function adds links to the plugin page
 */
add_filter('plugin_row_meta', 'dpsc_register_links',10,2);
function dpsc_register_links($links, $file) {

       $base = plugin_basename(__FILE__);
       if ($file == $base) {
               $links[] = '<a href="options-general.php?page=dukapress-shopping-cart-settings">'.__('Settings', 'dukapress').'</a>';
               $links[] = '<a href="http://wordpress.org/extend/plugins/dukapress/faq/" target="_blank">'.__('FAQ', 'dukapress').'</a>';
               $links[] = '<a href="http://dukapress.org/support/" target="_blank">'.__('Support', 'dukapress').'</a>';
       }
       return $links;
}

/**
 * This function removes the add new product link from admin bar
 */
add_action( 'wp_before_admin_bar_render', 'dpsc_remove_admin_bar_links' );

function dpsc_remove_admin_bar_links() {
       global $wp_admin_bar;
       $wp_admin_bar->remove_menu('new-duka');
}

/**
 * This function adds Dukapress to the adminbar in wp 3.1
 */
add_action( 'admin_bar_menu', 'dpsc_admin_bar_menu', 90 /* change this number to move it left(-) or right(+) */);

function dpsc_admin_bar_menu() {
       global $wp_admin_bar;
       if ( !is_super_admin() || !is_admin_bar_showing() ) {
       return;
       }
       $wp_admin_bar->add_menu( array( 'id' => 'dukapress', 'title' => 'DukaPress', 'href' => FALSE ) );
       $wp_admin_bar->add_menu( array( 'parent' => 'dukapress', 'title' => __('Order Log', 'dukapress'), 'href' => admin_url( 'admin.php?page=dukapress-shopping-cart-order-log' ) ) );
       $wp_admin_bar->add_menu( array( 'parent' => 'dukapress', 'title' => __('Customer Log', 'dukapress'), 'href' => admin_url( 'admin.php?page=dukapress-shopping-cart-customer-log' ) ) );
       $wp_admin_bar->add_menu( array( 'parent' => 'dukapress', 'title' => __('Settings', 'dukapress'), 'href' => admin_url( 'admin.php?page=dukapress-shopping-cart-settings' ) ) );
       $wp_admin_bar->add_menu( array( 'parent' => 'dukapress', 'title' => __('Products', 'dukapress'), 'href' => admin_url( 'edit.php?post_type=duka' ) ) );
       $wp_admin_bar->add_menu( array( 'parent' => 'dukapress', 'title' => __('Add New Product', 'dukapress'), 'href' => admin_url( 'post-new.php?post_type=duka' ) ) );
}

/**
 * This function shows Transaction Widget on Dashboard
 */
add_action('wp_dashboard_setup', 'dp_show_paid_transaction', 1);
function dp_show_paid_transaction() {
    wp_add_dashboard_widget( 'dp_dashboard_widget_test', __( 'DukaPress Transactions' ), 'dp_dashboard_transactions' );
}

function dp_dashboard_transactions() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dpsc_transactions";
    $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
    $query = "SELECT `total`, `shipping`, `tax`, `discount` FROM {$table_name} WHERE `payment_status`='Paid'";
    $results = $wpdb->get_results($query);
    $all_total = 0.00;
    $count = 0;
    foreach ($results as $result) {
        $total = $result->total;
        $shipping = $result->shipping;
        $discount = $result->discount;
        $tax= $result->tax;
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
        $amount = $total+$shipping+$total_tax-$total_discount;
        $all_total += $amount;
        $count++;
    }
    printf(__("Total %d orders sold with total amount of %s %d"),$count,$dp_shopping_cart_settings['dp_currency_symbol'],number_format($all_total,2));
//    echo 'Total ' . $count . ' orders sold with total amount of ' .$dp_shopping_cart_settings['dp_currency_symbol'] . number_format($all_total,2);
}

/**
 * This function creates Admin Menu.
 *
 */
add_action('admin_menu', 'dp_pnj_create_admin_menu');
function dp_pnj_create_admin_menu() {
    $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
    add_object_page('DukaPress', 'DukaPress', 'edit_others_posts', 'dukapress-shopping-cart-order-log', '', DP_PLUGIN_URL . '/images/dp_icon.png');
    add_submenu_page('dukapress-shopping-cart-order-log', 'DukaPress Order Log', 'Order Log', 'edit_others_posts', 'dukapress-shopping-cart-order-log', 'dukapress_shopping_cart_order_log');
    if ($dp_shopping_cart_settings['dp_shop_user_registration'] === 'checked') {
        add_submenu_page('dukapress-shopping-cart-order-log', 'DukaPress Customer Log', 'Customer Log', 'manage_options', 'dukapress-shopping-cart-customer-log', 'dukapress_shopping_cart_customer_log');
    }
    add_submenu_page('dukapress-shopping-cart-order-log', 'DukaPress Settings', 'Settings', 'edit_others_posts', 'dukapress-shopping-cart-settings', 'dukapress_shopping_cart_setting');
}

function dukapress_shopping_cart_customer_log() {
    global $dpsc_user_invoice_number, $wpdb;
    $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
    ?>
    <div class="wrap">
        <h2><?php _e("DukaPress Customer Log","dp-lang");?></h2>
        <?php
        if ($dp_shopping_cart_settings['dp_shop_user_registration'] === 'checked') {
            $sql = "SELECT `user_id` FROM {$wpdb->usermeta} WHERE `meta_key`='{$dpsc_user_invoice_number}'";
            $pagenum = isset($_GET['paged']) ? $_GET['paged'] : 1;
            $per_page = 20;
            $action_count = count($wpdb->get_results($sql));
            $total = ceil($action_count / $per_page);
            $action_offset = ($pagenum-1) * $per_page;
            $page_links = paginate_links( array(
                    'base' => add_query_arg( 'paged', '%#%' ),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'total' => ceil($action_count / $per_page),
                    'current' => $pagenum
            ));
            $sql .= " LIMIT {$action_offset}, {$per_page}";
            $customer_ids = $wpdb->get_col($sql);
            if (!empty($customer_ids)) {
                if ($page_links) {
                    ?>
                    <div class="tablenav">
                        <div class="tablenav-pages">
                            <?php
                            $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                                                                number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
                                                                number_format_i18n( min( $pagenum * $per_page, $action_count ) ),
                                                                number_format_i18n( $action_count ),
                                                                $page_links
                                                                );
                            echo $page_links_text;
                            ?>
                        </div>
                    </div>
                    <?php
                }
                echo '<div id="dp_settings" class="dukapress-settings">';
                foreach ($customer_ids as $customer_id) {
                    echo '<h3><a href="#">' . get_user_meta( intval($customer_id), 'first_name', TRUE ) . ' ' . get_user_meta( intval($customer_id), 'last_name', TRUE ) . '</a></h3>
                            <div>';
                    echo '<div id="dp_' . $customer_id . '">' . dp_get_invoices_for_customer($customer_id) . '</div>';
                    echo '</div>';
                }
                echo '</div>';
                if ($page_links) {
                    ?>
                    <div class="tablenav">
                        <div class="tablenav-pages">
                            <?php
                            $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                                                                number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
                                                                number_format_i18n( min( $pagenum * $per_page, $action_count ) ),
                                                                number_format_i18n( $action_count ),
                                                                $page_links
                                                                );
                            echo $page_links_text;
                            ?>
                        </div>
                    </div>
                    <?php
                }
            }
            else {
                echo 'No results found';
            }
        }
        else {
            echo 'User registration for DukaPress is not enabled.';
        }
        ?>
    </div>
        <?php
}

add_action('wp_ajax_dp_change_invoices_pagination', 'dp_change_page_invoice');
function dp_change_page_invoice() {
    $customer_id = intval($_POST['customer_id']);
    $page_number = intval($_POST['page']);
    die(dp_get_invoices_for_customer($customer_id, $page_number));
}

function dp_get_invoices_for_customer($customer_id, $page_number = 1) {
    global $dpsc_user_invoice_number;
    $invoice_numbers = get_user_meta( intval($customer_id), $dpsc_user_invoice_number, TRUE );
    $count = count($invoice_numbers);
    $pagenum = $page_number;
    $per_page = 10;
    $total = ceil($count / $per_page);
    $offset = ($pagenum-1) * $per_page;
    $output = '';
    if($total > 1) {
        $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>',
                                            number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
                                            number_format_i18n( min( $pagenum * $per_page, $count ) ),
                                            number_format_i18n( $count )
                                            );
        $output .= '<div class="tablenav"><div class="tablenav-pages" style="float: left!important;">' . $page_links_text . '</div></div>';
    }
    $invoice_numbers = array_splice($invoice_numbers, $offset, $per_page);
    $output .= '<ol>';
    foreach ($invoice_numbers as $invoice) {
        $output .= '<li><a href="?page=dukapress-shopping-cart-order-log&id=' . $invoice . '">' . $invoice . '</a></li>';
    }
    $output .= '</ol>';
    if ($total > 1) {
        $output .= '<p>';
        if ($page_number > 1) {
            $prev_id = $page_number - 1;
            $output .= '<span class="dp_pagination" rel="' . $customer_id . '" id="1">&laquo; First</span>';
            $output .= '<span class="dp_pagination" rel="' . $customer_id . '" id="' . $prev_id . '">&laquo; Previous</span>';
        }
        for ($i = 1; $i < $total+1; $i++) {
            if (($total <= 15) || (($i >= intval($page_number)) && ($i < (intval($page_number)+15))) || (($i > ($total-16)) && ($i < (intval($page_number)+15)) && $total > 15)) {
                if ($i === intval($page_number)) {
                    $output .= '<span class="dp_current" rel="' . $customer_id . '" id="' . $i . '">' . $i . '</span>';
                }
                else {
                    $output .= '<span class="dp_pagination" rel="' . $customer_id . '" id="' . $i . '">' . $i . '</span>';
                }
            }
        }
        if (intval($page_number) != intval($total)) {
            $next_id = $page_number+1;
            $output .= '<span class="dp_pagination" rel="' . $customer_id . '" id="' . $next_id . '">Next &raquo;</span>';
            $output .= '<span class="dp_pagination" rel="' . $customer_id . '" id="' . $total . '">Last &raquo;</span>';
        }
        $output .= '<img id="dp_action_search_pagi_' . $customer_id . '" src="' . DP_PLUGIN_URL . '/images/wpspin_light.gif" style="display: none;"></p>';
    }
    return $output;
}

/**
 * This part handles the CSS and JS
 *
 */
add_action('admin_init', 'dpsc_admin_register_style_js');

function dpsc_admin_register_style_js() {
    wp_register_script('dp_jquery_ui_js', DP_PLUGIN_URL . '/js/jquery-ui-1.8.4.custom.min.js', array('jquery'));
    wp_register_script('dpsc_admin_js', DP_PLUGIN_URL . '/js/dukapress-admin.js', array('jquery'));
    wp_register_style('dpsc_admin_css', DP_PLUGIN_URL.'/css/dp-admin.css');
    wp_register_style('dp_acc_style', DP_PLUGIN_URL.'/css/jquery-ui-1.8.5.custom.css');
    wp_enqueue_style('dpsc_admin_css');
    if ($_REQUEST['page'] === 'dukapress-shopping-cart-settings' || $_REQUEST['page'] === 'dukapress-shopping-cart-customer-log') {
        wp_enqueue_script('dp_jquery_ui_js');
    }
    wp_enqueue_style('dp_acc_style');
    wp_enqueue_script('dpsc_admin_js');
}

add_action('init', 'dpsc_register_style_js');
function dpsc_register_style_js () {
    if (!is_admin()) {
        wp_register_script('dp_jquery_ui_js', DP_PLUGIN_URL . '/js/jquery-ui-1.8.4.custom.min.js', array('jquery'));
        wp_register_style('dp_acc_style', DP_PLUGIN_URL . '/css/jquery-ui-1.8.5.custom.css');
        wp_register_style('dpsc_basic_css', DP_PLUGIN_URL.'/css/dpsc-basic.css');
        wp_register_script('dpsc_magiczoom', DP_PLUGIN_URL . '/js/magiczoom.js', array('jquery'));
        wp_register_script('dpsc_magiczoomplus', DP_PLUGIN_URL . '/js/magiczoomplus.js', array('jquery'));
        wp_register_style('jquery.fancybox', DP_PLUGIN_URL .'/js/jquery.fancybox/jquery.fancybox.css', false, '1.0', 'screen');
        wp_register_script('dpsc_lightbox', DP_PLUGIN_URL . '/js/jquery.fancybox/jquery.fancybox-1.2.1.pack.js', array('jquery'));
        wp_register_script('dpsc_lightbox_call', DP_PLUGIN_URL . '/js/lightbox.js', array('jquery', 'dpsc_lightbox'));
        wp_register_style('dpsc_jqzoom', DP_PLUGIN_URL .'/css/jqzoom.css', false, '1.0', 'screen');
        wp_register_script('dpsc_jqzoom', DP_PLUGIN_URL . '/js/jqzoom.pack.1.0.1.js', array('jquery'));
        wp_register_script('dpsc_js_file', DP_PLUGIN_URL . '/js/dukapress.js', array('jquery'));
        wp_register_script('dpsc_livequery',DP_PLUGIN_URL.'/js/jquery.livequery.js',array('jquery'));

        wp_enqueue_script('dp_jquery_ui_js');
        wp_enqueue_style('dp_acc_style');
        wp_enqueue_style('dpsc_basic_css');
        $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
        $image_effect = $dp_shopping_cart_settings['image_effect'];
        switch ($image_effect) {
            case 'mz_effect':
                wp_enqueue_script('dpsc_magiczoom');
                break;

            case 'mzp_effect':
                wp_enqueue_script('dpsc_magiczoomplus');
                break;

            case 'lightbox':
                wp_enqueue_style('jquery.fancybox');
                wp_enqueue_script('dpsc_lightbox');
                wp_enqueue_script('dpsc_lightbox_call');
                break;

           case 'no_effect':
                break;

           case 'jqzoom_effect':
                wp_enqueue_style('dpsc_jqzoom');
                wp_enqueue_script('dpsc_jqzoom');
                break;

           default:
                break;
       }
        $tim_url = DP_PLUGIN_URL . '/lib/timthumb.php?src=';
        $tim_end = '&w=310&h=383&zc=1';
        $dpsc_site_url = get_bloginfo('url');
        wp_enqueue_script('dpsc_js_file');
        wp_localize_script( 'dpsc_js_file', 'dpsc_js', array( 'tim_url' => $tim_url, 'tim_end' => $tim_end, 'dpsc_url' => $dpsc_site_url, 'width' => $dp_shopping_cart_settings['m_w'], 'height' => $dp_shopping_cart_settings['m_h'], 'ajaxurl' => admin_url('admin-ajax.php')) );
        wp_enqueue_script('dpsc_livequery');
    }
}

/////
//    //wp_enqueue_script('dp_jquery_ui_js', DP_PLUGIN_URL . '/js/jquery-ui-1.8.4.custom.min.js', array('jquery'));
//    //wp_enqueue_style('dp_acc_style', DP_PLUGIN_URL . '/css/jquery-ui-1.8.5.custom.css');
//    //wp_enqueue_style('dpsc_basic_css', DP_PLUGIN_URL.'/css/dpsc-basic.css');
//    $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
//    $image_effect = $dp_shopping_cart_settings['image_effect'];
//    switch ($image_effect) {
//        case 'mz_effect':
//            wp_enqueue_script('dpsc_magiczoom', DP_PLUGIN_URL . '/js/magiczoom.js', array('jquery'));
//            break;
//
//        case 'mzp_effect':
//            wp_enqueue_script('dpsc_magiczoomplus', DP_PLUGIN_URL . '/js/magiczoomplus.js', array('jquery'));
//            break;
//            break;
//
//        case 'lightbox':
//            wp_enqueue_style('jquery.fancybox', DP_PLUGIN_URL .'/js/jquery.fancybox/jquery.fancybox.css', false, '1.0', 'screen');
//            wp_enqueue_script('dpsc_lightbox', DP_PLUGIN_URL . '/js/jquery.fancybox/jquery.fancybox-1.2.1.pack.js', array('jquery'));
//            wp_enqueue_script('dpsc_lightbox_call', DP_PLUGIN_URL . '/js/lightbox.js', array('jquery', 'dpsc_lightbox'));
//            break;
//
//        case 'no_effect':
//            break;
//
//        case 'jqzoom_effect':
//            wp_enqueue_style('dpsc_jqzoom', DP_PLUGIN_URL .'/css/jqzoom.css', false, '1.0', 'screen');
//            wp_enqueue_script('dpsc_jqzoom', DP_PLUGIN_URL . '/js/jqzoom.pack.1.0.1.js', array('jquery'));
//            break;
//
//        default:
//            break;
//    }
//    $tim_url = DP_PLUGIN_URL . '/lib/timthumb.php?src=';
//    $tim_end = '&w=310&h=383&zc=1';
//    $dpsc_site_url = get_bloginfo('url');
//    wp_enqueue_script('dpsc_js_file', DP_PLUGIN_URL . '/js/dukapress.js', array('jquery'));
//    wp_localize_script( 'dpsc_js_file', 'dpsc_js', array( 'tim_url' => $tim_url, 'tim_end' => $tim_end, 'dpsc_url' => $dpsc_site_url, 'width' => $dp_shopping_cart_settings['m_w'], 'height' => $dp_shopping_cart_settings['m_h'], 'ajaxurl' => admin_url('admin-ajax.php')) );
//    wp_enqueue_script('dpsc_livequery',DP_PLUGIN_URL.'/js/jquery.livequery.js',array('jquery'));
///////

add_action('wp_ajax_dp_delete_transaction', 'dp_delete_transaction');
function dp_delete_transaction () {
    $invoice = $_POST['invoice'];
    global $wpdb;
    $table_name = $wpdb->prefix . "dpsc_transactions";
    $res = $wpdb->query("DELETE FROM {$table_name} WHERE `invoice` = '$invoice'");
    if($res)
        echo 'true';
    else
        echo 'false';
    die;
}


/**
 * This function displays Order Log
 *
 */
function dukapress_shopping_cart_order_log() {
    global $wpdb;
    echo '<h2>'.__("DukaPress Shop Order Log","dp-lang").'</h2>';
//    echo '<h2>DukaPress Shop Order Log</h2>';
    $table_name = $wpdb->prefix . "dpsc_transactions";
    if (!isset($_GET['id'])) {
        echo '<a class="button add-new-h2" href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=dukapress-shopping-cart-order-log&delete_all=true">Delete All</a>';
        if ($_GET['delete_all'] === 'true') {
            $wpdb->query("DELETE FROM `{$table_name}`");
        }
        $sql = "SELECT * FROM {$table_name} ORDER BY `id` DESC";
//        $get_by_page = $get_by_page ? $get_by_page : 1;
        $pagenum = isset($_GET['paged']) ? $_GET['paged'] : 1;
        $per_page = 15;
        $action_count = count($wpdb->get_results($sql));
        $total = ceil($action_count / $per_page);
        $action_offset = ($pagenum-1) * $per_page;
        $page_links = paginate_links( array(
                'base' => add_query_arg( 'paged', '%#%' ),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => ceil($action_count / $per_page),
                'current' => $pagenum
        ));
        $sql .= " LIMIT {$action_offset}, {$per_page}";
        $results = $wpdb->get_results($sql);
        if (is_array($results) && count($results) > 0) {
            if ($page_links) {
                ?>
                <div class="tablenav">
                    <div class="tablenav-pages">
                        <?php
                        $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                                                            number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
                                                            number_format_i18n( min( $pagenum * $per_page, $action_count ) ),
                                                            number_format_i18n( $action_count ),
                                                            $page_links
                                                            );
                        echo $page_links_text;
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>
            <table class="widefat post fixed">
                <thead>
                    <tr>
                        <th></th>
                        <th><?php _e("Invoice Number","dp-lang");?></th>
                        <th><?php _e("Name","dp-lang");?></th>
                        <th><?php _e("Date","dp-lang");?></th>
                        <th><?php _e("Amount","dp-lang");?></th>
                        <th><?php _e("Mode of Payment","dp-lang");?></th>
                        <th><?php _e("Status","dp-lang");?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $count = (($pagenum-1)*$per_page)+1;
                foreach ($results as $result) {
                    ?>
                    <tr>
                        <td><?php printf(__("%d"),$count);?></td>
                        <!--<td><?php// echo $count;?></td> -->
                        <td><a href="?page=dukapress-shopping-cart-order-log&id=<?php echo $result->invoice; ?>"><?php echo $result->invoice;?></a>
                            <p><a class="deletethis"style="cursor:pointer" rel="<?php echo $result->invoice ?>"><?php _e("Delete","dp-lang");?></a></p>
                        </td>
                        <!--<td><a href="?page=dukapress-shopping-cart-order-log&id=<?php// echo $result->id; ?>"><?php// echo $result->invoice;?></a></td> -->
                        <td><?php printf(__("%s %s"),$result->billing_first_name, $result->billing_last_name);?></td>
                        <td><?php printf(__("%s"),$result->date);?></td>
                        <!--<td><?php// echo $result->billing_first_name . ' ' . $result->billing_last_name;?></td>
                        <td><?php// echo $result->date;?></td>-->
                        <td><?php
                        $total = $result->total;
                        $shipping = $result->shipping;
                        $discount = $result->discount;
                        $tax= $result->tax;
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
                                printf(__("%01.2f"), $amount);
//                                echo $amount;
                        ?></td>
                        <td><?php printf(__("%s"),$result->payment_option);?></td>
                        <td id="dpsc_order_status_<?php echo $result->id; ?>"><input type="submit" value="<?php echo $result->payment_status;?>" onclick="dpsc_pnj_change_status('<?php echo $result->payment_status; ?>', <?php echo $result->id; ?>)" /></td>
                        <!--<td><?php// echo $result->payment_option;?></td>
                        <td id="dpsc_order_status_<?php// echo $result->id; ?>"><input type="submit" value="<?php echo $result->payment_status;?>" onclick="dpsc_pnj_change_status('<?php// echo $result->payment_status; ?>', <?php// echo $result->id; ?>)" /></td>-->
                    </tr>
                    <?php
                    $count++;
                }
                ?>
                </tbody>
            </table>
<?php
if ($page_links) {
    ?>
    <div class="tablenav">
        <div class="tablenav-pages">
            <?php
            $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                                                number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
                                                number_format_i18n( min( $pagenum * $per_page, $action_count ) ),
                                                number_format_i18n( $action_count ),
                                                $page_links
                                                );
            echo $page_links_text;
            ?>
        </div>
    </div>
    <?php
}
?>
    <script type="text/javascript">
    function dpsc_pnj_change_status(current_status, order_id) {
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: 'action=dpsc_change_order_status&id=' + order_id + '&current_status=' + current_status,
            success:function(msg){
                jQuery('td#dpsc_order_status_'+order_id).html(msg);
            }
        });
    }
    </script>
            <?php
        }
        else {
            _e("No records found !","dp-lang");
//            echo 'No records found!';
        }
    }
    else {
        $order_id = $_GET['id'];
        $query = "SELECT * FROM {$table_name} WHERE `invoice`='{$order_id}'";
        $result = $wpdb->get_row($query);
        $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
        if ($result) {
            if (isset($_GET['status']) && $_GET['status'] === 'send') {
                $message = '';
                if ($result->payment_status === 'Paid') {
                    $digital_message = '';
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
//                    $message = 'Hi ' . $result->billing_first_name .',<br/>
//                                We have received the payment for Invoice No.: '. $result->invoice . '.<br/>
//                                We will start processing your order soon.<br/>' . $digital_message . '
//                                Thanks,<br/>
//                                '. $dp_shopping_cart_settings['shop_name'];
//
//                    $subject = 'Payment Received For Invoice No: ' . $result->invoice;
//                }
//                elseif ($result->payment_status === 'Canceled') {
//                    $subject = 'Payment Canceled For Invoice No.:' . $result->invoice;
//                    $message = 'Hi ' . $result->billing_first_name .',<br/>
//                                The payment for Invoice No.: '. $result->invoice . ' was canceled. Kindly make the payment, so that we can proceed with the order.<br/>
//                                <br/>
//                                Thanks,<br/>
//                                '. $dp_shopping_cart_settings['shop_name'];
//                }
//                else {
//                    $subject = 'Payment Pending For Invoice No.:' . $result->invoice;
//                    $message = 'Hi ' . $result->billing_first_name .',<br/>
//                                The payment for Invoice No.: '. $result->invoice . ' is still pending. Kindly make the payment, so that we can proceed with the order.<br/>
//                                <br/>
//                                Thanks,<br/>
//                                '. $dp_shopping_cart_settings['shop_name'];
                }

                $to = $result->billing_email;
                $from = get_option('admin_email');

                $nme_dp_mail_option = get_option('dp_usr_payment_mail', true);

                $subject = $nme_dp_mail_option['dp_usr_payment_mail_title'];
                $message = $nme_dp_mail_option['dp_usr_payment_mail_body'];
                $message = str_replace("\r",'<br>', $message);

                $array1 = array('%fname%', '%status%', '%email%', '%inv%', '%digi%', '%shop%');
                $array2 = array($result->billing_first_name, $result->payment_status, $to, $result->invoice, $digital_message, $dp_shopping_cart_settings['shop_name']);
                $message = str_replace($array1, $array2, $message);

                dpsc_pnj_send_mail($to, $from, $dp_shopping_cart_settings['shop_name'], $subject, $message);

                $nme_dp_mail_option = get_option('dp_admin_payment_mail', true);

                $message = $nme_dp_mail_option['dp_admin_payment_mail_body'];
                $subject = $nme_dp_mail_option['dp_admin_payment_mail_title'];

                $find = array('%fname%', '%status%', '%email%', '%inv%', '%digi%', '%shop%');
                $replace = array($result->billing_first_name, $result->payment_status, $to, $result->invoice, $digital_message, $dp_shopping_cart_settings['shop_name']);
                $message = str_replace($find, $replace, $message);

                dpsc_pnj_send_mail($from, $to, $dp_shopping_cart_settings['shop_name'], $subject, $message);
            }
            ?>
<h3><?php printf(__("Transaction Details for Invoice No. ") . $result->invoice);?></h3>
<!--<h3>Transaction Details for Invoice No. <?php echo $result->invoice;?></h3>-->
<p><?php _e("Mode of Payment","dp-lang");?>: <?php printf(__("%s"),$result->payment_option);?></p>
<p><?php _e("Payment Status","dp-lang");?>: <?php printf(__("%s"),$result->payment_status);?></p>
            <table class="widefat post fixed">
                <thead>
                    <tr>
                        <th></th>
                        <th><?php _e("Product Name","dp-lang");?></th>
                        <th><?php _e("Price","dp-lang");?></th>
                        <th><?php _e("Quantity","dp-lang");?></th>
                        <th><?php _e("Total Amount","dp-lang");?></th>
                    </tr>
                </thead>
                <tbody>
<?php
$count = 1;
$products = $result->products;
$products = unserialize($products);
foreach ($products as $product) {
    ?>
    <tr>
        <td><?php printf(__("%d"),$count);?></td>
        <td><?php printf(__("%s"),$product['name']);?></td>
        <td><?php printf(__("%d"),$product['price']);?></td>
        <td><?php echo $product['quantity'];?></td>
        <td><?php echo $product['price']*$product['quantity'];?></td>
    </tr>
    <?php
    $count++;
}
?>
                </tbody>
            </table>
<?php
$total = $result->total;
$shipping = $result->shipping;
$discount = $result->discount;
$tax= $result->tax;
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
?>
<table class="order_log_info">
    <tr>
        <td><?php _e("Sub-Total:","dp-lang");?> </td><td><?php echo number_format($total,2);?></td>
    </tr>
    <tr>
        <td><?php _e("Shipping:","dp-lang");?> </td><td>+<?php echo number_format($shipping,2);?></td>
    </tr>
    <tr>
        <td><?php _e("Discount:","dp-lang");?> </td><td>-<?php echo number_format($total_discount,2);?></td>
    </tr>
    <tr>
        <td><?php _e("Tax:","dp-lang"); ?> </td><td>+<?php echo number_format($total_tax,2);?></td>
    </tr>
    <tr>
        <td><?php _e("Total:","dp-lang");?> </td><td>+<?php echo $amount;?></td>
    </tr>
</table>
<?php
if ($dp_shopping_cart_settings['dp_shop_pdf_generation'] === 'checked') {
?>
<p><a href="<?php echo DP_PLUGIN_URL .'/pdf/invoice_' . $result->invoice . '.pdf';?>"><?php _e("Click here to download your Invoice.","dp-lang");?></a></p>
<?php
}
?>
<p><a href="?page=dukapress-shopping-cart-order-log&id=<?php echo $result->invoice; ?>&status=send"><?php _e("Send Payment Notification.","dp-lang")?></a></p>

	<?php 
		$shipping = empty($result->shipping_first_name);
		global $dpsc_country_code_name;
	?>
		<h4><?php _e("Billing Address:","dp-lang");?></h4>
		<table class="order_log_info">
			<tr>
				<td><?php _e("First Name:","dp-lang");?> </td><td><?php _e($result->billing_first_name, "dp-lang") ;?></td>
			</tr>
			<tr>
				<td><?php _e("Last Name:","dp-lang");?> </td><td><?php _e($result->billing_last_name, "dp-lang") ;?></td>
			</tr>
			<tr>
				<td><?php _e("Address:","dp-lang");?> </td><td><?php _e($result->billing_address, "dp-lang") ;?></td>
			</tr>
			<tr>
				<td><?php _e("City:","dp-lang");?> </td><td><?php _e($result->billing_city, "dp-lang") ;?></td>
			</tr>
			<tr>
				<td><?php _e("Province / State:","dp-lang");?> </td><td><?php _e($result->billing_state, "dp-lang") ;?></td>
			</tr>
			<tr>
				<td><?php _e("Postal Code:","dp-lang");?> </td><td><?php _e($result->billing_zipcode, "dp-lang") ;?></td>
			</tr>
			<tr>
				<td><?php _e("Country:","dp-lang");?> </td><td><?php _e($dpsc_country_code_name[$result->billing_country], "dp-lang") ;?></td>
			</tr>
			<tr>
				<td><?php _e("Email:","dp-lang");?> </td><td><?php _e($result->billing_email, "dp-lang") ;?></td>
			</tr>
		</table>
		<?php  if(!$shipping) { ?>
			<h4><?php _e("Shipping Address:","dp-lang");?></h4>
			<table class="order_log_info">
				<tr>
					<td><?php _e("First Name:","dp-lang");?> </td><td><?php _e($result->shipping_first_name, "dp-lang") ;?></td>
				</tr>
				<tr>
					<td><?php _e("Last Name:","dp-lang");?> </td><td><?php _e($result->shipping_last_name, "dp-lang") ;?></td>
				</tr>
				<tr>
					<td><?php _e("Address:","dp-lang");?> </td><td><?php _e($result->shipping_address, "dp-lang") ;?></td>
				</tr>
				<tr>
					<td><?php _e("City:","dp-lang");?> </td><td><?php _e($result->shipping_city, "dp-lang") ;?></td>
				</tr>
				<tr>
					<td><?php _e("Province / State:","dp-lang");?> </td><td><?php _e($result->shipping_state, "dp-lang") ;?></td>
				</tr>
				<tr>
					<td><?php _e("Postal Code:","dp-lang");?> </td><td><?php _e($result->shipping_zipcode, "dp-lang") ;?></td>
				</tr>
				<tr>
					<td><?php _e("Country:","dp-lang");?> </td><td><?php _e($dpsc_country_code_name[$result->shipping_country], "dp-lang") ;?></td>
				</tr>
				
			</table>
		<?php } ?>
			
            <?php
        }
    }
}

/**
 * This function changes the order status
 *
 */
add_action('wp_ajax_dpsc_change_order_status', 'dpsc_change_order_status');
function dpsc_change_order_status() {
    global $wpdb,$table_name;
    $order_id = intval($_POST['id']);
    $current_status = $_POST['current_status'];
    if ($order_id > 0) {
        if ($current_status === "Pending") {
            $updated_status = "Paid";
        }
        elseif ($current_status === "Paid"){
            $updated_status = "Canceled";
        }
        else {
            $updated_status = "Pending";
        }
        global $wpdb;
        $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
        $table_name = $wpdb->prefix . "dpsc_transactions";
        $query = "UPDATE {$table_name} SET `payment_status`='{$updated_status}' WHERE `id`={$order_id}";
        $wpdb->query($query);
        if ($updated_status === 'Canceled') {
        $message = '';
        $digital_message = '';
        $check_query = "SELECT * FROM {$table_name} WHERE `id`={$order_id}";
        $result = $wpdb->get_row($check_query);
        $email_fname = $result->billing_first_name ;
        $email_lname = $result->billing_last_name ;
        $invoice =$result->invoice;
        
        $email_shop_name = $dp_shopping_cart_settings['shop_name'];
        $to = $result->billing_email;
        $from = get_option('admin_email');

//email to user on cancelled order
        $nme_dp_mail_option = get_option('dp_order_cancelled_mail_user_options', true);

        $message = $nme_dp_mail_option['dp_order_cancelled_send_mail_user_body'];
        $message = str_replace("\r",'<br>', $message);
        $subject = $nme_dp_mail_option['dp_order_cancelled_send_mail_user_title'];

        $find_tag = array('%fname%', '%lname%','%status%', '%email%', '%inv%', '%digi%', '%shop%');
        $rep_tag = array($email_fname, $email_lname,$updated_status, $to, $invoice, $digital_message, $email_shop_name);
        $message =str_replace($find_tag, $rep_tag, $message);
//email to admin on cannceled order        
        dpsc_pnj_send_mail($to, $from, $dp_shopping_cart_settings['shop_name'], $subject, $message);

        $nme_dp_mail_option = get_option('dp_order_cancelled_mail_options', true);
        $message = $nme_dp_mail_option['dp_order_cancelled_send_mail_body'];
        $message = str_replace("\r",'<br>', $message);
        $subject = $nme_dp_mail_option['dp_order_cancelled_send_mail_title'];
        
        $find_tag = array('%fname%','%lname%', '%status%', '%email%', '%inv%', '%digi%', '%shop%','%order-log-transaction%');
        $rep_tag = array($email_fname,$email_lname, $updated_status, $to, $invoice, $digital_message, $email_shop_name,$transaction_log);
        $message = str_replace($find_tag, $rep_tag, $message);
        //email to admin
        dpsc_pnj_send_mail($from, $to, $dp_shopping_cart_settings['shop_name'], $subject, $message);
      }
        $updated_status1 = "'" . $updated_status . "'";
        $button_html = '<input type="submit" value="' . $updated_status . '" onclick="dpsc_pnj_change_status(' . $updated_status1 . ', ' . $order_id . ')" />';
        die($button_html);
    }
}

/**
 * This function handles all the settings of  DukaPress
 *
 */
function dukapress_shopping_cart_setting() {
    global $dpsc_country_code_name;
    echo '<h2>DukaPress Shop Settings</h2>';
    if (isset($_POST['dp_submit'])) {
        $dp_mobile_name = $_POST['mobile_payment_name'];
        $dp_mobile_number = $_POST['mobile_payment_number'];
        $dp_shop_mode = $_POST['dp_shop_mode'];
        $dp_shop_country = $_POST['dp_shop_country'];
        $dp_shop_currency = $_POST['dp_shop_currency'];
        $dp_currency_code_enable = $_POST['dp_currency_code_enable'];
        $dp_currency_symbol = $_POST['dp_currency_symbol'];
        $dp_checkout_url = $_POST['dp_checkout_url'];
        $dp_thank_you_url = $_POST['dp_thank_you_url'];
		$dp_affiliate_url = $_POST['dp_affiliate_url'];
        $dp_tax = $_POST['dp_tax'];
        $dp_shop_paypal_id = $_POST['dp_shop_paypal_id'];
        $dp_shop_paypal_pdt = $_POST['dp_shop_paypal_pdt'];
        $dp_shop_paypal_use_sandbox = $_POST['dp_shop_paypal_use_sandbox'];
        $dp_shop_dl_duration = $_POST['dp_shop_dl_duration'];
        $dp_shop_inventory_active = $_POST['dp_shop_inventory_active'];
        $dp_shop_inventory_stocks = $_POST['dp_shop_inventory_stocks'];
        $dp_shop_inventory_soldout = $_POST['dp_shop_inventory_soldout'];
        $dp_shop_inventory_warning = $_POST['dp_shop_inventory_warning'];
        $dp_shop_inventory_email = $_POST['dp_shop_inventory_email'];
        $dp_shop_inventory_stock_warning = $_POST['dp_shop_inventory_stock_warning'];
        $dp_po = $_POST['dp_po'];
        $dp_shipping_flat_rate = $_POST['dp_shipping_flat_rate'];
        $dp_shipping_flat_limit_rate = $_POST['dp_shipping_flat_limit_rate'];
        $dp_shipping_weight_flat_rate = $_POST['dp_shipping_weight_flat_rate'];
        $dp_shipping_weight_class_rate = $_POST['dp_shipping_weight_class_rate'];
        $dp_shipping_per_item_rate = $_POST['dp_shipping_per_item_rate'];
        $dp_shipping_calc_method = $_POST['dp_shipping_calc_method'];
        $authorize_api = $_POST['authorize_api'];
        $authorize_transaction_key = $_POST['authorize_transaction_key'];
        $authorize_url = $_POST['authorize_url'];
        $authorize_test_request = $_POST['authorize_test_request'];
        $alertpay_id = $_POST['alertpay_id'];
        $tco_id = $_POST['tco_id'];
        $tco_secret_word = $_POST['tco_secret_word'];		
        $worldpay_id = $_POST['worldpay_id'];
        $worldpay_testmode = $_POST['worldpay_testmode'];
        $discount_enable = $_POST['discount_enable'];
        $bank_name = $_POST['bank_name'];
        $bank_routing = $_POST['bank_routing'];
        $bank_account = $_POST['bank_account'];
        $bank_account_owner = $_POST['bank_account_owner'];
        $bank_IBAN = $_POST['bank_IBAN'];
        $bank_bic = $_POST['bank_bic'];
        $safaricom_number = $_POST['safaricom_number'];
        $yu_number = $_POST['yu_number'];
        $zain_number = $_POST['zain_number'];
        $shop_name = $_POST['shop_name'];
        $shop_address = $_POST['shop_address'];
        $shop_state = $_POST['shop_state'];
        $shop_zip = $_POST['shop_zip'];
        $shop_city = $_POST['shop_city'];
        $image_effect = $_POST['image_effect'];
        $pp_c_code = $_POST['dp_paypal_currency'];
        $ap_c_code = $_POST['dp_alertpay_currency'];
		$tco_c_code = $_POST['dp_tco_currency'];
        $wp_c_code = $_POST['dp_worldpay_currency'];
        $dp_shop_user_registration = $_POST['dp_shop_user_registration'];
        $dp_shop_pdf_generation = $_POST['dp_shop_pdf_generation'];
        $dp_main_image_width = !empty($_POST['dp_main_image_width']) ? $_POST['dp_main_image_width'] : '310';
        $dp_main_image_height = !empty($_POST['dp_main_image_height']) ? $_POST['dp_main_image_height'] : '383';
        $dp_thumb_image_width = !empty($_POST['dp_thumb_image_width']) ? $_POST['dp_thumb_image_width'] : '50';
        $dp_thumb_image_height = !empty($_POST['dp_thumb_image_height']) ? $_POST['dp_thumb_image_height'] : '63';
        $dp_thumb_grid_width = !empty($_POST['dp_thumb_grid_width']) ? $_POST['dp_thumb_grid_width'] : '160';
        $dp_thumb_grid_height = !empty($_POST['dp_thumb_grid_height']) ? $_POST['dp_thumb_grid_height'] : '120';
        do_action('dp_on_settings_saved');
        $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
        if (!is_array($dp_shopping_cart_settings)) {
            $dp_shopping_cart_settings = array();
        }
        $dp_shopping_cart_settings['dp_shop_pdf_generation'] = $dp_shop_pdf_generation;
        $dp_shopping_cart_settings['dp_shop_user_registration'] = $dp_shop_user_registration;
        $dp_shopping_cart_settings['mobile_names'] = $dp_mobile_name;
        $dp_shopping_cart_settings['mobile_number'] = $dp_mobile_number;
        $dp_shopping_cart_settings['g_h'] = $dp_thumb_grid_height;
        $dp_shopping_cart_settings['g_w'] = $dp_thumb_grid_width;
        $dp_shopping_cart_settings['t_h'] = $dp_thumb_image_height;
        $dp_shopping_cart_settings['t_w'] = $dp_thumb_image_width;
        $dp_shopping_cart_settings['m_h'] = $dp_main_image_height;
        $dp_shopping_cart_settings['m_w'] = $dp_main_image_width;
        $dp_shopping_cart_settings['worldpay_currency'] = $wp_c_code;
        $dp_shopping_cart_settings['alertpay_currency'] = $ap_c_code;
		$dp_shopping_cart_settings['tco_currency'] = $tco_c_code;
        $dp_shopping_cart_settings['paypal_currency'] = $pp_c_code;
        $dp_shopping_cart_settings['image_effect'] = $image_effect;
        $dp_shopping_cart_settings['shop_city'] = $shop_city;
        $dp_shopping_cart_settings['shop_zip'] = $shop_zip;
        $dp_shopping_cart_settings['shop_state'] = $shop_state;
        $dp_shopping_cart_settings['shop_address'] = $shop_address;
        $dp_shopping_cart_settings['shop_name'] = $shop_name;
        $dp_shopping_cart_settings['bank_bic'] = $bank_bic;
        $dp_shopping_cart_settings['bank_IBAN'] = $bank_IBAN;
        $dp_shopping_cart_settings['bank_account_owner'] = $bank_account_owner;
        $dp_shopping_cart_settings['bank_account'] = $bank_account;
        $dp_shopping_cart_settings['bank_name'] = $bank_name;
        $dp_shopping_cart_settings['bank_routing'] = $bank_routing;
        $dp_shopping_cart_settings['safaricom_number'] = $safaricom_number;
        $dp_shopping_cart_settings['yu_number'] = $yu_number;
        $dp_shopping_cart_settings['zain_number'] = $zain_number;
        $dp_shopping_cart_settings['discount_enable'] = $discount_enable;
        $dp_shopping_cart_settings['alertpay_id'] = $alertpay_id;
		$dp_shopping_cart_settings['tco_id'] = $tco_id;
        $dp_shopping_cart_settings['tco_secret_word'] = $tco_secret_word;
		$dp_shopping_cart_settings['worldpay_id'] = $worldpay_id;
        $dp_shopping_cart_settings['worldpay_testmode'] = $worldpay_testmode;
        $dp_shopping_cart_settings['authorize_api'] = $authorize_api;
        $dp_shopping_cart_settings['authorize_transaction_key'] = $authorize_transaction_key;
        $dp_shopping_cart_settings['authorize_url'] = $authorize_url;
        $dp_shopping_cart_settings['authorize_test_request'] = $authorize_test_request;
        $dp_shopping_cart_settings['dp_shipping_per_item_rate'] = $dp_shipping_per_item_rate;
        $dp_shopping_cart_settings['dp_shipping_weight_class_rate'] = $dp_shipping_weight_class_rate;
        $dp_shopping_cart_settings['dp_shipping_weight_flat_rate'] = $dp_shipping_weight_flat_rate;
        $dp_shopping_cart_settings['dp_shipping_flat_limit_rate'] = $dp_shipping_flat_limit_rate;
        $dp_shopping_cart_settings['dp_shipping_flat_rate'] = $dp_shipping_flat_rate;
        $dp_shopping_cart_settings['dp_shipping_calc_method'] = $dp_shipping_calc_method;
        $dp_shopping_cart_settings['dp_po'] = $dp_po;
        $dp_shopping_cart_settings['dp_shop_mode'] = $dp_shop_mode;
        $dp_shopping_cart_settings['checkout'] = $dp_checkout_url;
        $dp_shopping_cart_settings['thank_you'] = $dp_thank_you_url;
		$dp_shopping_cart_settings['affiliate_url'] = $dp_affiliate_url;
        $dp_shopping_cart_settings['tax'] = $dp_tax;
        $dp_shopping_cart_settings['dp_shop_country'] = $dp_shop_country;
        $dp_shopping_cart_settings['dp_shop_currency'] = $dp_shop_currency;
        $dp_shopping_cart_settings['dp_currency_code_enable'] = $dp_currency_code_enable;
        $dp_shopping_cart_settings['dp_currency_symbol'] = $dp_currency_symbol;
        $dp_shopping_cart_settings['dp_shop_paypal_id'] = $dp_shop_paypal_id;
        $dp_shopping_cart_settings['dp_shop_paypal_pdt'] = $dp_shop_paypal_pdt;
        $dp_shopping_cart_settings['dp_shop_paypal_use_sandbox'] = $dp_shop_paypal_use_sandbox;
        $dp_shopping_cart_settings['dp_shop_inventory_active'] = $dp_shop_inventory_active;
        $dp_shopping_cart_settings['dp_shop_inventory_stocks'] = $dp_shop_inventory_stocks;
        $dp_shopping_cart_settings['dp_shop_inventory_soldout'] = $dp_shop_inventory_soldout;
        $dp_shopping_cart_settings['dp_shop_inventory_warning'] = $dp_shop_inventory_warning;
        $dp_shopping_cart_settings['dp_shop_inventory_email'] = $dp_shop_inventory_email;
        $dp_shopping_cart_settings['dp_shop_inventory_stock_warning'] = $dp_shop_inventory_stock_warning;
        update_option('dp_shopping_cart_settings', $dp_shopping_cart_settings);
        update_option('dp_dl_link_expiration_time', $dp_shop_dl_duration);
        $dp_oder_placed_mail_save = array('dp_order_send_mail_title' => $_POST['dp_order_send_mail_title'], 'dp_order_send_mail_body' => $_POST['dp_order_send_mail_body']);
        update_option('dp_order_mail_options', $dp_oder_placed_mail_save);
        $dp_oder_cancelled_mail_save = array('dp_order_cancelled_send_mail_title' => $_POST['dp_order_cancelled_send_mail_title'], 'dp_order_cancelled_send_mail_body' => $_POST['dp_order_cancelled_send_mail_body']);
        update_option('dp_order_cancelled_mail_options', $dp_oder_cancelled_mail_save);
        $dp_order_mail_user_options = array('dp_order_send_mail_user_title' => $_POST['dp_order_send_mail_user_title'], 'dp_order_send_mail_user_body' => $_POST['dp_order_send_mail_user_body']);
        $dp_oder_cancelled_mail_user_save = array('dp_order_cancelled_send_mail_user_title' => $_POST['dp_order_cancelled_send_mail_user_title'], 'dp_order_cancelled_send_mail_user_body' => $_POST['dp_order_cancelled_send_mail_user_body']);
        update_option('dp_order_cancelled_mail_user_options', $dp_oder_cancelled_mail_user_save);
        $dp_order_mail_user_options = array('dp_order_send_mail_user_title' => $_POST['dp_order_send_mail_user_title'], 'dp_order_send_mail_user_body' => $_POST['dp_order_send_mail_user_body']);
        update_option('dp_order_mail_user_options', $dp_order_mail_user_options);
        $dp_reg_admin_mail = array('dp_reg_admin_mail_title' => $_POST['dp_reg_admin_mail_title'], 'dp_reg_admin_mail_body' => $_POST['dp_reg_admin_mail_body']);
        update_option('dp_reg_admin_mail', $dp_reg_admin_mail);
        $dp_usr_reg_mail_options = array('dp_usr_reg_mail_title' => $_POST['dp_usr_reg_mail_title'], 'dp_usr_reg_mail_body' => $_POST['dp_usr_reg_mail_body']);
        update_option('dp_usr_reg_mail_options', $dp_usr_reg_mail_options);
        $dp_usr_inventory_mail = array('dp_usr_inventory_mail_title' => $_POST['dp_usr_inventory_mail_title'], 'dp_usr_inventory_mail_body' => $_POST['dp_usr_inventory_mail_body']);
        update_option('dp_usr_inventory_mail', $dp_usr_inventory_mail);
        $dp_usr_enquiry_mail = array('dp_usr_enquiry_mail_title' => $_POST['dp_usr_enquiry_mail_title'], 'dp_usr_enquiry_mail_body' => $_POST['dp_usr_enquiry_mail_body']);
        update_option('dp_usr_enquiry_mail', $dp_usr_enquiry_mail);
        $dp_admin_payment_mail = array('dp_usr_admin_payment_mail_title' => $_POST['dp_usr_admin_payment_mail_title'], 'dp_admin_payment_mail_body' => $_POST['dp_admin_payment_mail_body']);
        update_option('dp_admin_payment_mail', $dp_admin_payment_mail);
        $dp_usr_payment_mail = array('dp_usr_payment_mail_title' => $_POST['dp_usr_payment_mail_title'], 'dp_usr_payment_mail_body' => $_POST['dp_usr_payment_mail_body']);
        update_option('dp_usr_payment_mail', $dp_usr_payment_mail);
        ?>
        <h4><?php _e('Settings Saved',"dp-lang");?></h4>
        <?php
    }
    $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
    if (!is_array($dp_shopping_cart_settings['dp_po'])) {
        $dp_shopping_cart_settings['dp_po'] = array();
    }
    $dp_digital_time = get_option('dp_dl_link_expiration_time');
    if (!is_numeric($dp_digital_time)) {
        $dp_digital_time = 48;
    }
    $paypal_supported_currency = array('AUD', 'BRL', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'ILS', 'JPY', 'MXN', 'MYR', 'NOK', 'NZD', 'PHP', 'PLN', 'SEK', 'SGD', 'THB', 'TWD', 'USD');
    $alertpay_supported_currency = array('AUD', 'BGN', 'CAD', 'CHF', 'CZK', 'DKK', 'EKK', 'EUR', 'GBP', 'HKD', 'HUF', 'INR', 'LTL', 'MYR', 'MKD', 'NOK', 'NZD', 'PLN', 'RON', 'SEK', 'SGD', 'USD', 'ZAR');
	$tco_supported_currency = array('AUD', 'BGN', 'CAD', 'CHF', 'CZK', 'DKK', 'EKK', 'EUR', 'GBP', 'HKD', 'HUF', 'INR', 'LTL', 'MYR', 'MKD', 'NOK', 'NZD', 'PLN', 'RON', 'SEK', 'SGD', 'USD', 'ZAR');
    $worldpay_supported_currency = array('ARS', 'AUD', 'BRL', 'CAD', 'CHF', 'CLP', 'CNY', 'COP', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'IDR', 'ISK', 'JPY', 'KES', 'KRW', 'MXP', 'MYR', 'NOK', 'NZD', 'PLN', 'PTE', 'SEK', 'SGD', 'SKK', 'THB', 'TWD', 'USD', 'VND', 'ZAR');
    $authorize_supported_currency = array('USD');
    ?>
<div class="wrap">
<form action="" method="post" enctype="multipart/form-data">
<div id="dp_settings" class="dukapress-settings">

        <h3><a href="#"><?php _e("Basic Shop Settings","dp-lang");?></a></h3>
        <div>
            <div id="basic" class="tabdiv">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e("Name of Shop","dp-lang")?></th>
                        <td>
                            <input type="text" value="<?php if(isset($dp_shopping_cart_settings['shop_name'])) {echo $dp_shopping_cart_settings['shop_name'];}?>" name="shop_name">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Address","dp-lang")?></th>
                        <td>
                            <input type="text" value="<?php if(isset($dp_shopping_cart_settings['shop_address'])) {echo $dp_shopping_cart_settings['shop_address'];}?>" name="shop_address">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("State / Province","dp-lang");?></th>
                        <td>
                            <input type="text" value="<?php if(isset($dp_shopping_cart_settings['shop_state'])) {echo $dp_shopping_cart_settings['shop_state'];}?>" name="shop_state">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Postal Code","dp-lang");?></th>
                        <td>
                            <input type="text" value="<?php if(isset($dp_shopping_cart_settings['shop_zip'])) {echo $dp_shopping_cart_settings['shop_zip'];}?>" name="shop_zip">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("City / Town","dp-lang");?></th>
                        <td>
                            <input type="text" value="<?php if(isset($dp_shopping_cart_settings['shop_city'])) {echo $dp_shopping_cart_settings['shop_city'];}?>" name="shop_city">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Shop Mode","dp-lang");?></th>
                        <td>
                            <select name="dp_shop_mode">
                                <option value="regular" <?php if($dp_shopping_cart_settings['dp_shop_mode'] === 'regular') {echo 'selected';}?>>Regular Shop Mode</option>
                                <option value="inquiry" <?php if($dp_shopping_cart_settings['dp_shop_mode'] === 'inquiry') {echo 'selected';}?>>Inquiry Email Mode</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Country of your shop","dp-lang");?></th>
                        <td>
                            <select name="dp_shop_country" style="width: 240px;">
                                <?php
                                foreach ($dpsc_country_code_name as $country_code => $country_name) {
                                    $cont_selected = '';
                                    if ($dp_shopping_cart_settings['dp_shop_country'] === $country_code) {
                                        $cont_selected = 'selected="selected"';
                                    }
                                    echo '<option value="' . $country_code . '" ' . $cont_selected . '>' . $country_name . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Currency","dp-lang");?></th>
                        <td>
                            <select name="dp_shop_currency">
                                <?php
                                $dpsc_currency_codes = array('AED','AFN','ALL','AMD','ANG','AOA','ARS','AUD','AWG','AZN','BAM','BBD','BDT','BGN','BHD','BIF','BMD','BND','BOB','BRL','BSD',
                                            'BTN','BWP','BYR','BZD','CAD','CDF','CHF','CLP','CNY','COP','CRC','CUP','CVE','CZK','DJF','DKK','DOP','DZD','EEK','EGP','ERN','ETB','EUR',
                                            'FJD','FKP','GBP','GEL','GGP','GHS','GIP','GMD','GNF','GTQ','GYD','HKD','HNL','HRK','HTG','HUF','IDR','ILS','IMP','INR','IQD','IRR','ISK',
                                            'JEP','JMD','JOD','JPY','KES','KGS','KHR','KMF','KPW','KRW','KWD','KYD','KZT','LAK','LBP','LKR','LRD','LSL','LTL','LVL','LYD','MAD','MDL',
                                            'MGA','MKD','MMK','MNT','MOP','MRO','MTL','MUR','MVR','MWK','MXN','MYR','MZN','NAD','NGN','NIO','NOK','NPR','NZD','OMR','PAB','PEN','PGK',
                                            'PHP','PKR','PLN','PYG','QAR','RON','RSD','RUB','RWF','SAR','SBD','SCR','SDG','SEK','SGD','SHP','SLL','SOS','SPL','SRD','STD','SVC','SYP',
                                            'SZL','THB','TJS','TMM','TND','TOP','TRY','TTD','TVD','TWD','TZS','UAH','UGX','USD','UYU','UZS','VEF','VND','VUV','WST','XAF','XAG','XAU',
                                            'XCD','XDR','XOF','XPD','XPF','XPT','YER','ZAR','ZMK','ZWD');
                                foreach ($dpsc_currency_codes as $dpsc_currency_code) {
                                    ?>
                                <option value="<?php echo $dpsc_currency_code;?>" <?php if ($dp_shopping_cart_settings['dp_shop_currency'] === $dpsc_currency_code) {echo 'selected="selected"';}?>><?php echo $dpsc_currency_code;?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Enable User Registration:","dp-lang");?></th>
                        <td>
                            <input type="checkbox" value="checked" name="dp_shop_user_registration" <?php echo $dp_shopping_cart_settings['dp_shop_user_registration']; ?>/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Enable Invoice PDF Generation:","dp-lang");?></th>
                        <td>
                            <input type="checkbox" value="checked" name="dp_shop_pdf_generation" <?php echo $dp_shopping_cart_settings['dp_shop_pdf_generation']; ?>/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Product Page Image Effect","dp-lang"); ?></th>
                        <td>
                            <select name="image_effect">
                                <option value="mz_effect" <?php if($dp_shopping_cart_settings['image_effect'] === 'mz_effect') {echo 'selected';}?>>Magic Zoom</option>
                                <option value="mzp_effect" <?php if($dp_shopping_cart_settings['image_effect'] === 'mzp_effect') {echo 'selected';}?>>Magic Zoom Plus</option>
                                <option value="jqzoom_effect" <?php if($dp_shopping_cart_settings['image_effect'] === 'jqzoom_effect') {echo 'selected';}?>>JQZoom</option>
                                <option value="lightbox" <?php if($dp_shopping_cart_settings['image_effect'] === 'lightbox') {echo 'selected';}?>>Lightbox</option>
                                <option value="no_effect" <?php if($dp_shopping_cart_settings['image_effect'] === 'no_effect') {echo 'selected';}?>><?php _e("No Effect","dp-lang");?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Main Product Image Size","dp-lang");?></th>
                        <td>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="dp_main_image_width"><?php _e("Width","dp-lang");?></label></th><td><input type="text" id="dp_main_image_width" name="dp_main_image_width" size="5" value="<?php echo $dp_shopping_cart_settings['m_w'];?>" /><i>px</i></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="dp_main_image_height"><?php _e("Height","dp-lang");?></label></th><td><input type="text" id="dp_main_image_height" name="dp_main_image_height" size="5" value="<?php echo $dp_shopping_cart_settings['m_h'];?>" /><i>px</i></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Product Thumbnail Size","dp-lang");?></th>
                        <td>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="dp_thumb_image_width"><?php _e("Width","dp-lang");?></label></th><td><input type="text" id="dp_thumb_image_width" name="dp_thumb_image_width" size="5" value="<?php echo $dp_shopping_cart_settings['t_w'];?>" /><i>px</i></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="dp_thumb_image_height"><?php _e("Height","dp-lang");?></label></th><td><input type="text" id="dp_thumb_image_height" name="dp_thumb_image_height" size="5" value="<?php echo $dp_shopping_cart_settings['t_h'];?>" /><i>px</i></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Grid Product Thumbnail Size","dp-lang");?></th>
                        <td>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="dp_thumb_grid_width"><?php _e("Width","dp-lang");?></label></th><td><input type="text" id="dp_thumb_grid_width" name="dp_thumb_grid_width" size="5" value="<?php echo $dp_shopping_cart_settings['g_w'];?>" /><i>px</i></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="dp_thumb_grid_height"><?php _e("Height","dp-lang");?></label></th><td><input type="text" id="dp_thumb_grid_height" name="dp_thumb_grid_height" size="5" value="<?php echo $dp_shopping_cart_settings['g_h'];?>" /><i>px</i></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Currency Symbol","dp-lang")?></th>
                        <td>
                            <input type="text" value="<?php if(isset($dp_shopping_cart_settings['dp_currency_symbol'])) {echo $dp_shopping_cart_settings['dp_currency_symbol'];}?>" name="dp_currency_symbol">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Checkout Page URL","dp-lang")?></th>
                        <td>
                            <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['checkout'])) {echo $dp_shopping_cart_settings['checkout'];}?>" name="dp_checkout_url">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Thank You Page URL","dp-lang");?></th>
                        <td>
                            <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['thank_you'])) {echo $dp_shopping_cart_settings['thank_you'];}?>" name="dp_thank_you_url">
                        </td>
                    </tr>
					<tr>
                        <th scope="row"><?php _e("Affiliate URL","dp-lang");?></th>
                        <td>
                            <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['affiliate_url'])) {echo $dp_shopping_cart_settings['affiliate_url'];}?>" name="dp_affiliate_url">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Tax","dp-lang");?></th>
                        <td>
                            <input type="text" value="<?php if(isset($dp_shopping_cart_settings['tax'])) {echo $dp_shopping_cart_settings['tax'];}?>" name="dp_tax">%
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Payment Options","dp-lang");?></th>
                        <td>
                            <input type="checkbox" name="dp_po[]" value="paypal" <?php if (in_array('paypal', $dp_shopping_cart_settings['dp_po'])) {echo "checked";} ?>/> PayPal <br />
                            <input type="checkbox" name="dp_po[]" value="authorize" <?php if (in_array('authorize', $dp_shopping_cart_settings['dp_po'])) {echo "checked";} ?>/> Authorize.net <br />
                            <input type="checkbox" name="dp_po[]" value="worldpay" <?php if (in_array('worldpay', $dp_shopping_cart_settings['dp_po'])) {echo "checked";} ?>/> WorldPay <br />
							<input type="checkbox" name="dp_po[]" value="tco" <?php if (in_array('tco', $dp_shopping_cart_settings['dp_po'])) {echo "checked";} ?>/> 2Checkout <br />
                            <input type="checkbox" name="dp_po[]" value="alertpay" <?php if (in_array('alertpay', $dp_shopping_cart_settings['dp_po'])) {echo "checked";} ?>/> AlertPay <br />
                            <input type="checkbox" name="dp_po[]" value="bank" <?php if (in_array('bank', $dp_shopping_cart_settings['dp_po'])) {echo "checked";} ?>/> <?php _e("Bank transfer in advance","dp-lang");?> <br />
                            <input type="checkbox" name="dp_po[]" value="cash" <?php if (in_array('cash', $dp_shopping_cart_settings['dp_po'])) {echo "checked";} ?>/> <?php _e("Cash at store","dp-lang");?> <br />
                            <input type="checkbox" name="dp_po[]" value="mobile" <?php if (in_array('mobile', $dp_shopping_cart_settings['dp_po'])) {echo "checked";} ?>/> <?php _e("Mobile Payment","dp-lang");?> <br />
                            <input type="checkbox" name="dp_po[]" value="delivery" <?php if (in_array('delivery', $dp_shopping_cart_settings['dp_po'])) {echo "checked";} ?>/> <?php _e("Cash on delivery","dp-lang");?> <br />
                            <?php do_action('dp_more_payment_option'); ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <h3><a href="#"><?php _e("Product Management","dp-lang");?></a></h3>
        <div>
            <div id="product-management" class="tabdiv dukapress-settings">
                    <h3><a href="#"><?php _e("Inventory Settings","dp-lang");?></a></h3>
                    <div>
                        <div id="inventory" class="tabdiv">
                            <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php _e("Active","dp-lang");?></th>
                                        <td>
                                            <select name="dp_shop_inventory_active">
                                                <option value="no" <?php if($dp_shopping_cart_settings['dp_shop_inventory_active'] === 'no') {echo 'selected';}?>><?php _e("No","dp-lang");?></option>
                                                <option value="yes" <?php if($dp_shopping_cart_settings['dp_shop_inventory_active'] === 'yes') {echo 'selected';}?>><?php _e("Yes","dp-lang");?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php _e("Display Stocks Amounts","dp-lang");?></th>
                                        <td>
                                            <select name="dp_shop_inventory_stocks">
                                                <option value="no" <?php if($dp_shopping_cart_settings['dp_shop_inventory_stocks'] === 'no') {echo 'selected';}?>><?php _e("No","dp-lang");?></option>
                                                <option value="yes" <?php if($dp_shopping_cart_settings['dp_shop_inventory_stocks'] === 'yes') {echo 'selected';}?>><?php _e("Yes","dp-lang");?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php _e("Sold Out Notice","dp-lang");?></th>
                                        <td>
                                            <select name="dp_shop_inventory_soldout">
                                                <option value="no" <?php if($dp_shopping_cart_settings['dp_shop_inventory_soldout'] === 'no') {echo 'selected';}?>><?php _e("No","dp-lang");?></option>
                                                <option value="yes" <?php if($dp_shopping_cart_settings['dp_shop_inventory_soldout'] === 'yes') {echo 'selected';}?>><?php _e("Yes","dp-lang");?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php _e("Warning Threshold","dp-lang");?></th>
                                        <td>
                                            <select name="dp_shop_inventory_warning">
                                                <option value="no" <?php if($dp_shopping_cart_settings['dp_shop_inventory_warning'] === 'no') {echo 'selected';}?>><?php _e("No","dp-lang");?></option>
                                                <option value="yes" <?php if($dp_shopping_cart_settings['dp_shop_inventory_warning'] === 'yes') {echo 'selected';}?>><?php _e("Yes","dp-lang");?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php _e("Inventory warning email","dp-lang");?></th>
                                        <td>
                                            <input type="text" value="<?php if (isset($dp_shopping_cart_settings['dp_shop_inventory_email'])) {echo $dp_shopping_cart_settings['dp_shop_inventory_email'];}?>" name="dp_shop_inventory_email"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php _e("Inventory Stock for Warning","dp-lang");?></th>
                                        <td>
                                            <input type="text" value="<?php if (isset($dp_shopping_cart_settings['dp_shop_inventory_stock_warning'])) {echo $dp_shopping_cart_settings['dp_shop_inventory_stock_warning'];}?>" name="dp_shop_inventory_stock_warning"/>
                                        </td>
                                    </tr>
                                </table>
                        </div>
                    </div>
                    <h3><a href="#"><?php _e("Shipping Options","dp-lang");?></a></h3>
                    <div>
                        <div id="shipping" class="tabdiv">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php _e("Shipping calculation method","dp-lang");?></th>
                                    <td>
                                        <select name="dp_shipping_calc_method">
                                            <option value="free" <?php if ($dp_shopping_cart_settings['dp_shipping_calc_method'] === "free") {echo 'selected="selected"';}?>><?php _e("Free","dp-lang");?></option>
                                            <option value="flat" <?php if ($dp_shopping_cart_settings['dp_shipping_calc_method'] === "flat") {echo 'selected="selected"';}?>><?php _e("Flat","dp-lang");?></option>
                                            <option value="flat_limit" <?php if ($dp_shopping_cart_settings['dp_shipping_calc_method'] === "flat_limit") {echo 'selected="selected"';}?>><?php _e("Flat Limit","dp-lang");?></option>
                                            <option value="weight_flat" <?php if ($dp_shopping_cart_settings['dp_shipping_calc_method'] === "weight_flat") {echo 'selected="selected"';}?>><?php _e("Weight Flat","dp-lang");?></option>
                                            <option value="weight_class" <?php if ($dp_shopping_cart_settings['dp_shipping_calc_method'] === "weight_class") {echo 'selected="selected"';}?>><?php _e("Weight Class","dp-lang");?></option>
                                            <option value="per_item" <?php if ($dp_shopping_cart_settings['dp_shipping_calc_method'] === "per_item") {echo 'selected="selected"';}?>><?php _e("Per Item","dp-lang");?></option>
                                            <?php do_action('dp_shipping_dropdown_option');?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e("Flat Rate","dp-lang");?></th>
                                    <td>
                                        <input name="dp_shipping_flat_rate" value="<?php if(isset($dp_shopping_cart_settings['dp_shipping_flat_rate'])) {echo $dp_shopping_cart_settings['dp_shipping_flat_rate'];}?>"/>
                                    </td>

                                </tr>
                                <tr>
                                    <th scope="row"><?php _e("Flat Limit Rate","dp-lang");?></th>
                                    <td>
                                        <input name="dp_shipping_flat_limit_rate" value="<?php if(isset($dp_shopping_cart_settings['dp_shipping_flat_limit_rate'])) {echo $dp_shopping_cart_settings['dp_shipping_flat_limit_rate'];}?>"/>
                                    </td>

                                </tr>
                                <tr>
                                    <th scope="row"><?php _e("Weight Flat Rate","dp-lang");?></th>
                                    <td>
                                        <input name="dp_shipping_weight_flat_rate" value="<?php if(isset($dp_shopping_cart_settings['dp_shipping_weight_flat_rate'])) {echo $dp_shopping_cart_settings['dp_shipping_weight_flat_rate'];}?>"/>
                                    </td>

                                </tr>
                                <tr>
                                    <th scope="row"><?php _e("Weight Class Rate","dp-lang");?></th>
                                    <td>
                                        <input name="dp_shipping_weight_class_rate" value="<?php if(isset($dp_shopping_cart_settings['dp_shipping_weight_class_rate'])) {echo $dp_shopping_cart_settings['dp_shipping_weight_class_rate'];}?>"/>
                                    </td>

                                </tr>
                                <tr>
                                    <th scope="row"><?php _e("Per Item Rate","dp-lang");?></th>
                                    <td>
                                        <input name="dp_shipping_per_item_rate" value="<?php if(isset($dp_shopping_cart_settings['dp_shipping_per_item_rate'])) {echo $dp_shopping_cart_settings['dp_shipping_per_item_rate'];}?>"/>
                                    </td>

                                </tr>
                                <?php do_action('dp_shipping_field');?>
                            </table>
                        </div>
                    </div>
                    <h3><a href="#"><?php _e("Digital Products","dp-lang");?></a></h3>
                    <div>
                        <div id="digital" class="tabdiv">
                            <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php _e("Download Links Duration:","dp-lang");?></th>
                                        <td>
                                            <input type="text" name="dp_shop_dl_duration" value="<?php echo $dp_digital_time;?>"/>
                                        </td>
                                    </tr>
                                </table>
                        </div>
                    </div>
            </div>
        </div>
        <h3><a href="#"><?php _e("Payment Options","dp-lang")?></a></h3>
        <div>
            <div id="po" class="tabdiv dukapress-settings">
                    <h3><a href="#"><?php _e("PayPal","dp-lang");?></a></h3>
                    <div>
                        <div id="paypal" class="tabdiv">
                            <table class="form-table">
                                    <tr>
                                        <th scope="row"><?php _e("Use PayPal Sandbox:","dp-lang");?></th>
                                        <td>
                                            <input type="checkbox" value="checked" name="dp_shop_paypal_use_sandbox" <?php echo $dp_shopping_cart_settings['dp_shop_paypal_use_sandbox']; ?>/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php _e("PayPal ID","dp-lang");?></th>
                                        <td>
                                            <input size="50" type="text" name="dp_shop_paypal_id" value="<?php if(isset($dp_shopping_cart_settings['dp_shop_paypal_id'])) {echo $dp_shopping_cart_settings['dp_shop_paypal_id'];}?>"/>
                                        </td>

                                    </tr>
                                    <?php
                                    $paypal_currency_code = $dp_shopping_cart_settings['paypal_currency'];
                                    if($dp_shopping_cart_settings['dp_shop_currency']) {
                                        if (in_array($dp_shopping_cart_settings['dp_shop_currency'], $paypal_supported_currency)) {
                                            ?>
                                            <input type="hidden" name="dp_paypal_currency" value="<?php echo $dp_shopping_cart_settings['dp_shop_currency']; ?>" />
                                            <?php
                                            $paypal_currency_code = $dp_shopping_cart_settings['dp_shop_currency'];
                                        }
                                        else {
                                        ?>
                                            <tr><td colspan="2"><?php _e("Your Shop's Currency Code is not compatible with PayPal. Please choose a Currency Code from the below list. Payments will be converted to the selected Currency Code, when Payments are sent to PayPal.","dp-lang");?></td></tr>
                                            <tr><th scope="row"><?php _e("PayPal Currency Code","dp-lang");?></th>
                                            <td>
                                                <select name="dp_paypal_currency">
                                                    <?php
                                                    foreach ($paypal_supported_currency as $p_code) {
                                                        $p_selected = '';
                                                        if ($paypal_currency_code === $p_code) {
                                                            $p_selected = 'selected="selected"';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $p_code;?>" <?php echo $p_selected;?>><?php echo $p_code; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            </tr>
                                        <?php
                                    }
                                    }
                                    ?>
                                </table>
                        </div>
                    </div>
                    <h3><a href="#"><?php _e("Authorize.net");?></a></h3>
                    <div>
                        <div id="authorize" class="tabdiv">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php _e("API Login","dp-lang");?></th>
                                    <td>
                                        <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['authorize_api'])) {echo $dp_shopping_cart_settings['authorize_api'];}?>" name="authorize_api">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e("Transaction Key","dp-lang");?></th>
                                    <td>
                                        <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['authorize_transaction_key'])) {echo $dp_shopping_cart_settings['authorize_transaction_key'];}?>" name="authorize_transaction_key">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e("URL","dp-lang");?></th>
                                    <td>
                                        <select name="authorize_url">
                                            <option value="live" <?php if($dp_shopping_cart_settings['authorize_url'] === "live") { echo 'selected="selected"';}?>>https://secure.authorize.net/gateway/transact.dll</option>
                                            <option value="test" <?php if($dp_shopping_cart_settings['authorize_url'] === "test") { echo 'selected="selected"';}?>>https://test.authorize.net/gateway/transact.dll</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e("Test-Request","dp-lang");?></th>
                                    <td>
                                        <select name="authorize_test_request">
                                            <option value="live" <?php if($dp_shopping_cart_settings['authorize_test_request'] === "live") { echo 'selected="selected"';}?>>False</option>
                                            <option value="test" <?php if($dp_shopping_cart_settings['authorize_test_request'] === "test") { echo 'selected="selected"';}?>>True</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <h3><a href="#"><?php _e("WorldPay","dp-lang");?></a></h3>
                    <div>
                        <div id="worldpay" class="tabdiv">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php _e("Installation-ID","dp-lang");?></th>
                                    <td>
                                        <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['worldpay_id'])) {echo $dp_shopping_cart_settings['worldpay_id'];}?>" name="worldpay_id">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e("Testmode","dp-lang");?></th>
                                    <td>
                                        <select name="worldpay_testmode">
                                            <option value="live" <?php if($dp_shopping_cart_settings['worldpay_testmode'] === "live") { echo 'selected="selected"';}?>>False</option>
                                            <option value="test" <?php if($dp_shopping_cart_settings['worldpay_testmode'] === "test") { echo 'selected="selected"';}?>>True</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php
                                    $worldpay_currency_code = $dp_shopping_cart_settings['worldpay_currency'];
                                    if($dp_shopping_cart_settings['dp_shop_currency']) {
                                        if (in_array($dp_shopping_cart_settings['dp_shop_currency'], $worldpay_supported_currency)) {
                                            ?>
                                            <input type="hidden" name="dp_worldpay_currency" value="<?php echo $dp_shopping_cart_settings['dp_shop_currency']; ?>" />
                                            <?php
                                            $worldpay_currency_code = $dp_shopping_cart_settings['dp_shop_currency'];
                                        }
                                        else {
                                        ?>
                                        <tr><td colspan="2"><?php _e("Your Shop's Currency Code is not compatible with WorldPay. Please choose a Currency Code from the below list. Payments will be converted to the selected Currency Code, when Payments are sent to WorldPay.","dp-lang");?></td></tr>
                                        <tr><th scope="row"><?php _e("WorldPay Currency Code","dp-lang");?></th>
                                            <td>
                                                <select name="dp_worldpay_currency">
                                                    <?php
                                                    foreach ($worldpay_supported_currency as $w_code) {
                                                        $w_selected = '';
                                                        if ($worldpay_currency_code === $w_code) {
                                                            $w_selected = 'selected="selected"';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $w_code;?>" <?php echo $w_selected;?>><?php echo $w_code; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        <?php
                                        }
                                    }
                                    ?>
                            </table>
                        </div>
                    </div>
                    <h3><a href="#"><?php _e("AlertPay");?></a></h3>
                    <div>
                        <div id="alertpay" class="tabdiv">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php _e("AlertPay ID","dp-lang");?></th>
                                    <td>
                                        <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['alertpay_id'])) {echo $dp_shopping_cart_settings['alertpay_id'];}?>" name="alertpay_id">
                                    </td>
                                </tr>
                                <?php
                                    $alertpay_currency_code = $dp_shopping_cart_settings['alertpay_currency'];
                                    if($dp_shopping_cart_settings['dp_shop_currency']) {
                                        if (in_array($dp_shopping_cart_settings['dp_shop_currency'], $alertpay_supported_currency)) {
                                            ?>
                                            <input type="hidden" name="dp_alertpay_currency" value="<?php echo $dp_shopping_cart_settings['dp_shop_currency']; ?>" />
                                            <?php
                                            $alertpay_currency_code = $dp_shopping_cart_settings['dp_shop_currency'];
                                        }
                                        else {
                                        ?>
                                        <tr><td colspan="2"><?php _e("Your Shop's Currency Code is not compatible with AlertPay. Please choose a Currency Code from the below list. Payments will be converted to the selected Currency Code, when Payments are sent to AlertPay.","dp-lang");?></td></tr>
                                        <tr><th scope="row"><?php _e("AlertPay Currency Code","dp-lang");?></th>
                                            <td>
                                                <select name="dp_alertpay_currency">
                                                    <?php
                                                    foreach ($alertpay_supported_currency as $a_code) {
                                                        $a_selected = '';
                                                        if ($alertpay_currency_code === $a_code) {
                                                            $a_selected = 'selected="selected"';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $a_code;?>" <?php echo $a_selected;?>><?php echo $a_code; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        <?php
                                        }
                                    }
                                    ?>
                            </table>
                        </div>
                    </div>
					<h3><a href="#"><?php _e("2Checkout");?></a></h3>
                    <div>
                        <div id="tco" class="tabdiv">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php _e("2Checkout Account Number","dp-lang");?></th>
                                    <td>
                                        <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['tco_id'])) {echo $dp_shopping_cart_settings['tco_id'];}?>" name="tco_id">
                                    </td>
                                </tr>
								<tr>
                                    <th scope="row"><?php _e("2Checkout Secret Word","dp-lang");?></th>
                                    <td>
                                        <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['tco_secret_word'])) {echo $dp_shopping_cart_settings['tco_secret_word'];}?>" name="tco_secret_word">
                                    </td>
                                </tr>
                                <?php
                                    $tco_currency_code = $dp_shopping_cart_settings['tco_currency'];
                                    if($dp_shopping_cart_settings['dp_shop_currency']) {
                                        if (in_array($dp_shopping_cart_settings['dp_shop_currency'], $tco_supported_currency)) {
                                            ?>
                                            <input type="hidden" name="dp_tco_currency" value="<?php echo $dp_shopping_cart_settings['dp_shop_currency']; ?>" />
                                            <?php
                                            $tco_currency_code = $dp_shopping_cart_settings['dp_shop_currency'];
                                        }
                                        else {
                                        ?>
                                        <tr><td colspan="2"><?php _e("Your Shop's Currency Code is not compatible with 2Checkout. Please choose a Currency Code from the below list. Payments will be converted to the selected Currency Code, when Payments are sent to 2Checkout.","dp-lang");?></td></tr>
                                        <tr><th scope="row"><?php _e("2Checkout Currency Code","dp-lang");?></th>
                                            <td>
                                                <select name="dp_tco_currency">
                                                    <?php
                                                    foreach ($tco_supported_currency as $t_code) {
                                                        $t_selected = '';
                                                        if ($tco_currency_code === $t_code) {
                                                            $t_selected = 'selected="selected"';
                                                        }
                                                        ?>
                                                        <option value="<?php echo $t_code;?>" <?php echo $t_selected;?>><?php echo $t_code; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        <?php
                                        }
                                    }
                                    ?>
                            </table>
                        </div>
                    </div>
                    <h3><a href="#"><?php _e("Mobile","dp-lang");?></a></h3>
                    <div>
                        <div id="mobile" class="tabdiv">
                            <table class="form-table">
                                <thead>
                                    <tr>
                                        <th><?php _e("Name","dp-lang");?></th><th><?php _e("Number","dp-lang");?></th>
                                    </tr>
                                </thead>
                                <tbody class="mobile_payment">
                                    <?php
                                    if (is_array($dp_shopping_cart_settings['mobile_names'])) {
                                        $count_mp = count($dp_shopping_cart_settings['mobile_names']);
                                        for($mp_i = 0; $mp_i < $count_mp; $mp_i++) {
                                            ?>
                                            <tr class="row_block">
                                                <td>
                                                    <input type="text" value="<?php echo $dp_shopping_cart_settings['mobile_names'][$mp_i];?>" name="mobile_payment_name[]"/>
                                                </td>
                                                <td>
                                                    <input type="text" value="<?php echo $dp_shopping_cart_settings['mobile_number'][$mp_i];?>" name="mobile_payment_number[]"/>
                                                    <a style="cursor:pointer" onClick="return dp_m_rem(this);" class="remove_row">[-]</a>&nbsp;
                                                    <a style="cursor:pointer" onClick="return dp_m_add(this);" class="add_row">[+]</a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    else {
                                    ?>
                                    <tr class="row_block">
                                        <td>
                                            <input type="text" value="" name="mobile_payment_name[]"/>
                                        </td>
                                        <td>
                                            <input type="text" value="" name="mobile_payment_number[]"/>
                                            <a style="cursor:pointer" onClick="return dp_m_rem(this);" class="remove_row">[-]</a>&nbsp;
                                            <a style="cursor:pointer" onClick="return dp_m_add(this);" class="add_row">[+]</a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <h3><a href="#"><?php _e("Bank Details","dp-lang");?></a></h3>
                    <div>
                        <div id="bank" class="tabdiv">
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><?php _e("Name of Bank","dp-lang")?></th>
                                    <td>
                                        <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['bank_name'])) {echo $dp_shopping_cart_settings['bank_name'];}?>" name="bank_name">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e("Routing Number","dp-lang");?></th>
                                    <td>
                                        <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['bank_routing'])) {echo $dp_shopping_cart_settings['bank_routing'];}?>" name="bank_routing">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e("Account Number","dp-lang");?></th>
                                    <td>
                                        <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['bank_account'])) {echo $dp_shopping_cart_settings['bank_account'];}?>" name="bank_account">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e("Bank Account Owner","dp-lang");?></th>
                                    <td>
                                        <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['bank_account_owner'])) {echo $dp_shopping_cart_settings['bank_account_owner'];}?>" name="bank_account_owner">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e("IBAN","dp-lang");?></th>
                                    <td>
                                        <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['bank_IBAN'])) {echo $dp_shopping_cart_settings['bank_IBAN'];}?>" name="bank_IBAN">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e("BIC/SWIFT","dp-lang");?></th>
                                    <td>
                                        <input size="50" type="text" value="<?php if(isset($dp_shopping_cart_settings['bank_bic'])) {echo $dp_shopping_cart_settings['bank_bic'];}?>" name="bank_bic">
                                    </td>
                                </tr>                                
                            </table>
                        </div>
                    </div>
                <?php do_action('dp_other_payment_option_details');?>
            </div>
        </div>
        <h3><a href="#"><?php _e("Discount Management","dp-lang");?></a></h3>
        <div>
            <div id="discount" class="tabdiv">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e("Enable Discount","dp-lang");?></th>
                        <td>
                            <input type="checkbox" value="true" name="discount_enable" <?php if ($dp_shopping_cart_settings['discount_enable'] === 'true') { echo 'checked';}?> />
                        </td>
                    </tr>
                </table>
                <div id="discount_code_confirmation"></div>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e("Enter Discount Code","dp-lang");?></th>
                        <td>
                            <input type="text" value="" name="discount_code" id="discount_code" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("Discount","dp-lang");?></th>
                        <td>
                            <input type="text" value="" name="discount_amount" id="discount_amount" />%
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e("One Time Discount","dp-lang");?></th>
                        <td>
                            <input type="checkbox" value="true" name="discount_one_time" id="discount_one_time" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"></th>
                        <td>
                            <input type="submit" id="dp_discount_submit" name="dp_discount_submit" value="Add Code"/>
                        </td>
                    </tr>
                </table>
                <div id="discount_code_layout">
                    <?php _e(dpsc_get_discount_code_table(),"dp-lang");?>
                    <?php// echo dpsc_get_discount_code_table();?>
                </div>
            </div>
        </div>
       <h3><a href="#"><?php _e("Email Management","dp-lang");?></a></h3>
        <div>
        <div class="email-management">
            <h3><a href="#"><?php _e("Order Placed","dp-lang");?></a></h3>
              <div>
                <div>
                     <div class="email-management">
                         <h3><a href="#"><?php _e("To Admin","dp-lang");?></a></h3>
                         <div>
                             <?php
                                $nme_dp_mail_option = get_option('dp_order_mail_options', true);
                                ?>
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row">
                                                <label for="dp_order_send_mail_title"><?php _e("Subject","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <input size="80" type="text" id="dp_order_send_mail_title" name="dp_order_send_mail_title" value="<?php echo stripslashes($nme_dp_mail_option['dp_order_send_mail_title']) ?>" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <th valign="top">
                                                <label for="dp_order_send_mail_messege"><?php _e("Message","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <span class="description"><?php _e("Use","dp-lang");?> <strong>%baddress%</strong>, <strong>%saddress%</strong>, <strong>%inv%</strong>, <strong>%siteurl%</strong>, <strong>%shop%</strong> ,<strong>%order-log-transaction%</strong> <?php _e("as Billing Address, Shipping Address, Invoice, Site URL, Shop Name, Order Log Transaction","dp-lang");?></span><br/>
                                                <textarea rows="15" cols="78" id="dp_order_send_mail_messege" name="dp_order_send_mail_body"><?php echo stripslashes($nme_dp_mail_option['dp_order_send_mail_body']) ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                         </div>
                         <h3><a href="#"><?php _e("To User","dp-lang");?></a></h3>
                         <div>
                             <?php
                                $nme_dp_mail_option = get_option('dp_order_mail_user_options', true);
                                ?>
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row">
                                                <label for="dp_order_send_mail_user_title"><?php _e("Subject","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <input size="80" type="text" id="dp_order_send_mail_user_title" name="dp_order_send_mail_user_title" value="<?php echo stripslashes($nme_dp_mail_option['dp_order_send_mail_user_title']) ?>" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <th valign="top">
                                                <label for="dp_order_send_mail_user_messege"><?php _e("Message","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <span class="description"><?php _e("Use","dp-lang");?> <strong>%fname%</strong>, <strong>%lname%</strong>, <strong>%inv%</strong>, <strong>%shop%</strong>, <strong>%siteurl%</strong> <?php _e("As Billing First Name, Billing Last Name, Invoice, Shop Name and site URL","dp-lang");?></span><br/>
                                                <textarea rows="15" cols="78" id="dp_order_send_mail_messege" name="dp_order_send_mail_user_body"><?php echo stripslashes($nme_dp_mail_option['dp_order_send_mail_user_body']) ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                         </div>
                      </div>
                 </div>
              </div>
            <h3><a href="#"><?php  _e("Order Canceled","dp-lang");?></a></h3>
              <div>
                <div>
                     <div class="email-management">
                         <h3><a href="#"><?php _e("To Admin","dp-lang");?></a></h3>
                         <div>
                             <?php
                                $nme_dp_mail_option = get_option('dp_order_cancelled_mail_options', true);
                                ?>
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row">
                                                <label for="dp_order_cancelled_send_mail_title"><?php _e("Subject","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <input size="80" type="text" id="dp_order_cancelled_send_mail_title" name="dp_order_cancelled_send_mail_title" value="<?php echo stripslashes($nme_dp_mail_option['dp_order_cancelled_send_mail_title']) ?>" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <th valign="top">
                                                <label for="dp_order_cancelled_send_mail_messege"><?php _e("Message","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <span class="description"><?php _e("Use","dp-lang");?> <strong>%fname%</strong>, <strong>%lname%</strong>, <strong>%saddress%</strong>, <strong>%inv%</strong>, <strong>%status%</strong>, <strong>%digi%</strong>,<strong>%siteurl%</strong>, <strong>%shop%</strong> ,<strong>%order-log-transaction%</strong> <?php _e("FirstName,Last Name, Invoice, Status,Digi,Site URL, Shop Name, Order Log Transaction","dp-lang");?></span><br/>
                                                <textarea rows="15" cols="78" id="dp_order_cancelled_send_mail_messege" name="dp_order_cancelled_send_mail_body"><?php echo stripslashes($nme_dp_mail_option['dp_order_cancelled_send_mail_body']) ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                         </div>
                         <h3><a href="#"><?php _e("To User","dp-lang");?></a></h3>
                         <div>
                             <?php
                                $nme_dp_mail_option = get_option('dp_order_cancelled_mail_user_options', true);
                                ?>
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row">
                                                <label for="dp_order_cancelled_send_mail_user_title"><?php _e("Subject","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <input size="80" type="text" id="dp_order_cancelled_send_mail_user_title" name="dp_order_cancelled_send_mail_user_title" value="<?php echo stripslashes($nme_dp_mail_option['dp_order_cancelled_send_mail_user_title']) ?>" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <th valign="top">
                                                <label for="dp_order_cancelled_send_mail_user_messege"><?php _e("Message","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <span class="description"><?php _e("Use","dp-lang");?> <strong>%fname%</strong>, <strong>%lanme%</strong>, <strong>%inv%</strong>,<strong>%status%</strong>, <strong>%shop%</strong>, <strong>%siteurl%</strong> <?php _e("As Billing First Name, Billing Last Name, Invoice,Status, Shop Name and site URL","dp-lang");?></span><br/>
                                                <textarea rows="15" cols="78" id="dp_order_cancelled_send_mail_messege" name="dp_order_cancelled_send_mail_user_body"><?php echo stripslashes($nme_dp_mail_option['dp_order_cancelled_send_mail_user_body']) ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                         </div>
                      </div>
                 </div>
              </div>
            <h3><a href="#"><?php _e("User Registration","dp-lang");?></a></h3>
               <div>
                   <div class="email-management">
                      <h3><a href="#"><?php _e("To Admin","dp-lang");?></a></h3>
                        <div>
                          <div>
                            <?php
                            $nme_dp_mail_option = get_option('dp_reg_admin_mail', true);
                            ?>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">
                                            <label for="dp_reg_admin_mail_title"><?php _e("Subject","dp-lang");?></label>
                                        </th>
                                        <td>
                                            <input size="80" type="text" id="dp_reg_admin_mail_title" name="dp_reg_admin_mail_title" value="<?php echo stripslashes($nme_dp_mail_option['dp_reg_admin_mail_title']) ?>" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <th valign="top">
                                            <label for="dp_reg_admin_mail_messege"><?php _e("Message","dp-lang");?></label>
                                        </th>
                                        <td>
                                            <span class="description"><?php _e("Use","dp-lang");?> <strong>%uname%</strong>, <strong>%pass%</strong>, <strong>%email%</strong>, <strong>%shop%</strong>  <?php _e("as User Name, Password, email, and ShopName","dp-lang");?></span><br/>
                                            <textarea rows="15" cols="78" id="dp_order_send_mail_messege" name="dp_reg_admin_mail_body"><?php echo stripslashes($nme_dp_mail_option['dp_reg_admin_mail_body']) ?></textarea>
                                        </td>
                                    </tr>
                                </table>
                        </div>
                      </div>
                         <h3><a href="#"><?php _e("To User","dp-lang");?></a></h3>
                         <div>
                           <div>
                                <?php
                                $nme_dp_mail_option = get_option('dp_usr_reg_mail_options', true);
                                ?>
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row">
                                                <label for="dp_usr_reg_mail_title"><?php _e("Subject","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <input size="80" type="text" id="dp_order_send_mail_title" name="dp_usr_reg_mail_title" value="<?php echo stripslashes($nme_dp_mail_option['dp_usr_reg_mail_title']) ?>" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <th valign="top">
                                                <label for="dp_usr_reg_mail_messege"><?php _e("Message","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <span class="description"><?php _e("Use","dp-lang");?> <strong>%uname%</strong>, <strong>%pass%</strong>, <strong>%email%</strong>, <strong>%login%</strong>, <strong>%shop%</strong>  <?php _e("as User Name, Password, email, Login URL, and ShopName","dp-lang");?></span><br/>
                                                <textarea rows="15" cols="78" id="dp_order_send_mail_messege" name="dp_usr_reg_mail_body"><?php echo stripslashes($nme_dp_mail_option['dp_usr_reg_mail_body']) ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                            </div>
                         </div>
                     </div>
                 </div>
            <h3><a href="#"><?php _e("Inventory","dp-lang");?></a></h3>
                <div>
                     <div class="email-management">
<!--                         <h3><a href="#">To Admin</a></h3>
                         <div>
                             A
                         </div>-->
                         <h3><a href="#"><?php _e("To Admin","dp-lang");?></a></h3>
                         <div>
                           <div>
                                <?php
                                $nme_dp_mail_option = get_option('dp_usr_inventory_mail', true);
                                ?>
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row">
                                                <label for="dp_usr_inventory_mail_title"><?php _e("Subject","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <input size="80" type="text" id="dp_usr_inventory_mail_title" name="dp_usr_inventory_mail_title" value="<?php echo stripslashes($nme_dp_mail_option['dp_usr_inventory_mail_title']) ?>" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <th valign="top">
                                                <label for="dp_usr_inventory_mail_messege"><?php _e("Message","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <span class="description"><?php _e("Use","dp-lang");?> <strong>%pno%</strong>, <strong>%pname%</strong>, <strong>%stock%</strong>, <strong>%footer%</strong>, <?php _e("as Product No., Product Name, Currently in Stock Quantity, and footer","dp-lang");?> </span><br/>
                                                <textarea rows="15" cols="78" id="dp_usr_inventory_mail_messege" name="dp_usr_inventory_mail_body"><?php echo stripslashes($nme_dp_mail_option['dp_usr_inventory_mail_body']) ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                            </div>
                         </div>
                     </div>
                 </div>
            <h3><a href="#"><?php _e("Enquiry","dp-lang");?></a></h3>
                <div>
                     <div class="email-management">
<!--                         <h3><a href="#">To User</a></h3>
                         <div>
                             
                         </div>-->
                         <h3><a href="#"><?php _e("To Admin","dp-lang");?></a></h3>
                         <div>
                             <div>
                                <?php
                                $nme_dp_mail_option = get_option('dp_usr_enquiry_mail', true);
                                ?>
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row">
                                                <label for="dp_usr_enquiry_mail_title"><?php _e("Subject","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <input size="80" type="text" id="dp_usr_inventory_mail_title" name="dp_usr_enquiry_mail_title" value="<?php echo stripslashes($nme_dp_mail_option['dp_usr_enquiry_mail_title']) ?>" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <th valign="top">
                                                <label for="dp_usr_enquiry_mail_messege"><?php _e("Message","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <span class="description"><?php _e("Use","dp-lang");?> <strong>%from%</strong>, <strong>%from_email%</strong>, <strong>%enq_subject%</strong>, <strong>%details%</strong>, <strong>%custom_message%</strong>, <?php _e("as Enquirers Name, Enquirers email, Enquiry Subject, Enquiry Details, and Enquiry custom message","dp-lang");?> </span><br/>
                                                <textarea rows="15" cols="78" id="dp_usr_enquiry_mail_messege" name="dp_usr_enquiry_mail_body"><?php echo stripslashes($nme_dp_mail_option['dp_usr_enquiry_mail_body']) ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                            </div>
                         </div>
                     </div>
                 </div>

               <h3><a href="#"><?php _e("Payments","dp-lang");?></a></h3>
               <div>
                    <div class="email-management">
                        <h3><a href="#"><?php _e("To Admin","dp-lang");?></a></h3>
                         <div>
                              <div>
                                <?php
                                $nme_dp_mail_option = get_option('dp_admin_payment_mail', true);
                                ?>
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row">
                                                <label for="dp_admin_mail_title"><?php _e("Subject","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <input size="80" type="text" id="dp_admin_payment_mail_title" name="dp_usr_admin_payment_mail_title" value="<?php echo stripslashes($nme_dp_mail_option['dp_usr_admin_payment_mail_title']) ?>" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <th valign="top">
                                                <label for="dp_admin_payment_mail_messege"><?php _e("Message","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <span class="description"><?php _e("Use","dp-lang");?> <strong>%fname%</strong>, <strong>%email%</strong>, <strong>%inv%</strong>, <strong>%status%</strong>, <strong>%digi%</strong>, <strong>%shop%</strong>, <?php _e("as Payers First Name, Payers email, Invoice, Payment status, Digital Products, and Shop Name","dp-lang");?> </span><br/>
                                                <textarea rows="15" cols="78" id="dp_admin_payment_mail_messege" name="dp_admin_payment_mail_body"><?php echo stripslashes($nme_dp_mail_option['dp_admin_payment_mail_body']) ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                            </div>
                         </div>
                         <h3><a href="#"><?php _e("To User","dp-lang");?></a></h3>
                         <div>
                             <div>
                                <?php
                                $nme_dp_mail_option = get_option('dp_usr_payment_mail', true);
                                ?>
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row">
                                                <label for="dp_usr_payment_mail_title"><?php _e("Subject","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <input size="80" type="text" id="dp_usr_payment_mail_title" name="dp_usr_payment_mail_title" value="<?php echo stripslashes($nme_dp_mail_option['dp_usr_payment_mail_title']) ?>" >
                                            </td>
                                        </tr>
                                        <tr>
                                            <th valign="top">
                                                <label for="dp_usr_enquiry_mail_messege"><?php _e("Message","dp-lang");?></label>
                                            </th>
                                            <td>
                                                <span class="description"><?php _e("Use","dp-lang");?> <strong>%fname%</strong>, <strong>%email%</strong>, <strong>%inv%</strong>, <strong>%status%</strong>, <strong>%digi%</strong>, <strong>%shop%</strong>, <?php _e("as Payers First Name, Payers email, Invoice, Payment Status, Digital Products, and Shop Name","dp-lang");?> </span><br/>
                                                <textarea rows="15" cols="78" id="dp_usr_payment_mail_messege" name="dp_usr_payment_mail_body"><?php echo stripslashes($nme_dp_mail_option['dp_usr_payment_mail_body']) ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                            </div>
                         </div>
                    </div>
              </div>
        </div>
    </div>
</div>
    <input type="submit" name="dp_submit" value="Save Settings" />
</form>
</div>
        <?php
}/**
 * This function prints the table of Discount codes
 *
 */
function dpsc_get_discount_code_table() {
    $dpsc_discount_codes = get_option('dpsc_discount_codes');
    $output = '';
    if ($dpsc_discount_codes && count($dpsc_discount_codes) > 0 ) {
        $output .= '<table class="form-table">
                        <thead>
                            <tr>
                                <th style="width: 20%;">Discount Code</th>
                                <th style="width: 32%;">Discount Amount (%)</th>
                                <th style="width: 22%;">Number of Times Used</th>
                                <th style="width: 22%;"></th>
                            </tr>
                        </thead>';
        foreach ($dpsc_discount_codes as $dpsc_discount_code) {
            $output .= '<tr>
                            <td>' . $dpsc_discount_code['code'] . '</td>
                            <td>' . $dpsc_discount_code['amount'] . '</td>
                            <td>' . $dpsc_discount_code['count'] . '</td>
                            <td><a><span class="dpsc_delete_discount_code" id="' . $dpsc_discount_code['id'] . '">Delete</span></a></td>
                        </tr>';
        }
        $output .= '</table>';
        if ($_REQUEST['ajax'] === 'true') {
            $output .= '<script type="text/javascript">jQuery("span.dpsc_delete_discount_code").live("click", function(){
                            var dpsc_delete_discount_code_id = jQuery(this).attr("id");
                            jQuery.ajax({
                                type: "POST",
                                url: ajaxurl,
                                data: "action=dpsc_delete_discount_code&id=" + dpsc_delete_discount_code_id + "&ajax=true",
                                success:function(msg){
                                    jQuery("div#discount_code_layout").html(msg);
                                }
                            });
                        });</script>';
        }
    }
    else {
        $output = 'No Discount Code added!';
    }
    return $output;
}

/**
 * This function saves Discount code
 *
 */
add_action('wp_ajax_save_dpsc_discount_code', 'dpsc_save_discount_code');
function dpsc_save_discount_code() {
    $discount_code = $_POST['dpsc_discount_code'];
    $discount_amount = $_POST['dpsc_discount_amount'];
    $discount_one_time = $_POST['dpsc_discount_one_time'];
    $output = '';
    $unique_discount = TRUE;
    $discount = get_option('dpsc_discount_codes') ? get_option('dpsc_discount_codes') : array();
    foreach ($discount as $check_code) {
        if ($check_code['code'] === $discount_code) {
            $unique_discount = FALSE;
        }
    }
    if ($unique_discount) {
        if (!empty($discount_code) && !empty($discount_amount)) {
            $dpsc_discount['code'] = $discount_code;
            $dpsc_discount['amount'] = $discount_amount;
            $dpsc_discount['count'] = 0;
            $dpsc_discount['one_time'] = $discount_one_time;
            $dpsc_discount['id'] = time();
            $discount[] = $dpsc_discount;
            update_option('dpsc_discount_codes', $discount);
        }
    }
    else {
        $output = '<span id="dpsc-same-discount-code-present">Please add another discount code as same discount code already exists.</span>';
    }
    if ($_REQUEST['ajax'] == 'true') {
        ob_start();
        _e(dpsc_get_discount_code_table(),"dp-lang");
//        echo dpsc_get_discount_code_table();
        $output .= ob_get_contents();
        ob_end_clean();
        die($output);
    }
}

/**
 * This function deletes the Discount code
 *
 */
add_action('wp_ajax_dpsc_delete_discount_code', 'dpsc_delete_discount_code');
function dpsc_delete_discount_code() {
    $dpsc_discount_code_id = intval($_POST['id']);
    $dpsc_discount_codes = get_option('dpsc_discount_codes');
    $dpsc_discount_codes_new = array();
    if (is_array($dpsc_discount_codes)) {
        foreach ($dpsc_discount_codes as $check_code) {
            if ($check_code['id'] === $dpsc_discount_code_id) {
                unset ($check_code);
            }
            else {
                $dpsc_discount_codes_new[] = $check_code;
            }
        }
    }
    update_option('dpsc_discount_codes', $dpsc_discount_codes_new);
    if ($_REQUEST['ajax'] == 'true') {
        ob_start();
        _e(dpsc_get_discount_code_table(),"dp-lang");
        $output .= ob_get_contents();
        ob_end_clean();
        die($output);
    }
}

/**
 * This function returns the download links
 *
 */
function dpsc_pnj_get_download_links($products = FALSE) {
    if ($products) {
        if (is_array($products) && count($products) >0 ) {
            $temp_names = array();
            foreach ($products as $product) {
                $file_name = get_post_meta(intval($product),'digital_file', true);
                if ($file_name != '') {
                    $file_path = DP_DOWNLOAD_FILES_DIR . $file_name;

                    $temp_name = md5($file_name.time());
                    $newfile_path = DP_DOWNLOAD_FILES_DIR_TEMP . $temp_name ;

                    if (!copy($file_path, $newfile_path)) {
                        die("failed to copy $file...\n");
                    }else {
                        $temp_names[] = $temp_name.'@_@||@_@'.$file_name;
                        dpsc_pnj_update_download_table($temp_name, $file_name);
                    }
                }
            }
            return $temp_names;
        }
    }
    return FALSE;
}

/**
 * This function saves the download temp file in database
 *
 */
function dpsc_pnj_update_download_table($temp_name, $file_name) {
    global $wpdb;
    $table_name2 = $wpdb->prefix . "dpsc_temp_file_log";
    $sql = "INSERT INTO {$table_name2} (`real_name`, `saved_name`, `sent_time`) VALUES ('{$file_name}', '{$temp_name}', NOW())";
    $wpdb->query($sql);
}

/**
 * This function sends mail
 *
 */
function dpsc_pnj_send_mail($to, $from, $name, $subject, $msg, $attachment = FALSE) {
    global $wpdb;
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: ' . $name . ' <' . $from . '>' . "\r\n";
    if ($attachment) {
        if ($dp_shopping_cart_settings['dp_shop_pdf_generation'] === 'checked') {
            $mail_attachment = array(DP_PLUGIN_DIR. '/pdf/invoice_' . $attachment . '.pdf');
            @wp_mail($to, $subject, $msg, $headers,$mail_attachment);
        }
        else {
            @wp_mail($to, $subject, $msg, $headers);
        }
    }
    else {
        @wp_mail($to, $subject, $msg, $headers);
    }
}

/**
 * This function generates PDF
 *
 */
function make_pdf($invoice, $dpsc_discount_value, $tax, $dpsc_shipping_value, $dpsc_total, $bfname, $blname, $bcity, $baddress, $bstate, $bzip, $bcountry, $phone, $option='bill', $test=0) {
    global $dpsc_country_code_name;
    $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
    define('FPDF_FONTPATH', 'font/');
    require_once('lib/fpdf16/fpdf.php');


    if ($option == 'bill') {

        class PDF extends FPDF {

            //Page header
            function Header() {
                $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
                $ad = array();
                $ad[f_name] = $dp_shopping_cart_settings['shop_name'];
                $ad[street] = $dp_shopping_cart_settings['shop_address'];
                $ad[zip] = $dp_shopping_cart_settings['shop_zip'];
                $ad[town] = $dp_shopping_cart_settings['shop_city'];
                $ad[state] = $dp_shopping_cart_settings['shop_state'];


                $biz_ad = implode("<br/>", $ad);
                $biz = str_replace("<br/>", "\n", $biz_ad);
                $biz = pdf_encode($biz);

                $this->SetFont('Arial', 'B', 12);

                //$url  = get_option('siteurl');
                $path = DP_PLUGIN_DIR . '/images/pdf-logo-1.jpg';
                $this->Image($path);
                $this->SetXY(90, 7);
                $this->MultiCell(0, 7, "$biz", 0, 'L');
            }

            //Page footer
            function Footer() {
                $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
                //Position at xy mm from bottom
                $this->SetY(-25);
                //Arial italic 6
                $this->SetFont('Arial', '', 6);

                if (FALSE) {
                    $vat_id = ' - ' . get_option('wps_vat_id_label') . ': ' . get_option('wps_vat_id');
                } else {$vat_id = NULL;        }

                $footer_text = $dp_shopping_cart_settings['shop_name'] . $vat_id;
                $this->Cell(0, 10, "$footer_text", 1, 0, 'C');
            }

        }

        //Instanciation of inherited class
        $pdf = new PDF;
        $pdf->SetLeftMargin(10);
        $pdf->SetRightMargin(10);
        $pdf->SetTopMargin(5);

        // widths of columns
        $w1 = 20;
        $w2 = 64;
        $w3 = 30;
        $w4 = 38;
        $w5 = 38;

        $h2 = 3;


        $pdf->AddPage();
        $pdf->Ln(20);
        $pdf->SetFont('Arial', '', 10);
        // data for address
        $order = array();
        $order[f_name] = $bfname . ' ' . $blname;
//        $order[l_name] = $blname;
        $order[street] = $baddress;
        $order[town] = $bcity;
        $order[state] = $bstate;
        $order[zip] = $bzip;

        $order[country] = $dpsc_country_code_name[$bcountry];

        address_format($order, 'pdf_cust_address', $pdf);


        $pdf->Ln(20);
        $pdf->SetFont('Arial', 'B', 10);
        $phone_no = pdf_encode('Contact No. : ' . $phone);
        $pdf->Cell(0, 6, $phone_no, 0, 1);
        $bill_no = pdf_encode('Bill No. : ' . $invoice);
        $pdf->Cell(0, 6, $bill_no, 0, 1);

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 4, date(get_option('date_format')), 0, 1, 'R');

        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($w1, 6, pdf_encode('Sr. No.'), 1, 0);
        $pdf->Cell($w2, 6, pdf_encode('Product Name'), 1, 0);
        $pdf->Cell($w3, 6, pdf_encode('Quantity'), 1, 0);
        $pdf->Cell($w4, 6, pdf_encode('Product Price'), 1, 0);
        $pdf->Cell($w5, 6, pdf_encode('Total'), 1, 1);
        $pdf->SetFont('Arial', '', 9);


        // get the cart content again
        $dpsc_products = $_SESSION['dpsc_products'];
        $dpsc_total = 0.00;
        $count = 1;
        foreach ($dpsc_products as $dpsc_product) {
            $dpsc_var = '';
            if (!empty($dpsc_product['var'])) {
                $dpsc_var = ' (' . $dpsc_product['var'] . ')';
            }
            $dpsc_total += floatval($dpsc_product['price'] * $dpsc_product['quantity']);
            $dis_price = number_format($dpsc_product['price'], 2);
            $dis_price_total = number_format($dpsc_product['price'] * $dpsc_product['quantity'], 2);
            $details = explode("|", $v);

            $pdf->SetFont('Arial', 'B', 9);

            $pdf->Cell($w1, 6, pdf_encode("$details[5]"), 'LTR', 0); // Art-no
            $pdf->Cell($w2, 6, pdf_encode("$details[2]"), 'LTR', 0); // Art-name
            $pdf->Cell($w3, 6, pdf_encode("$details[1]"), 'LTR', 0); // Amount
            $pdf->Cell($w4, 6, pdf_encode("$details[3]"), 'LTR', 0); // U - Price
            $pdf->Cell($w5, 6, pdf_encode("$details[4]"), 'LTR', 1); // Total price
            // any attributes?
            $pdf->SetFont('Arial', '', 7);

        //				foreach($ad as $v){
        //
        //					if(WPLANG == 'de_DE'){$v = utf8_decode($v);}
        //					pdf_encode($v);

            $pdf->Cell($w1, $h2, $count, 'LR', 0); // Art-no
            $pdf->Cell($w2, $h2, $dpsc_product['name'] . $dpsc_var, 'LR', 0); // Art-name
            $pdf->Cell($w3, $h2, $dpsc_product['quantity'], 'LR', 0); // Amount
            $pdf->Cell($w4, $h2, pdf_encode($dp_shopping_cart_settings['dp_currency_symbol'] . $dis_price), 'LR', 0); // U - Price
            $pdf->Cell($w5, $h2, pdf_encode($dp_shopping_cart_settings['dp_currency_symbol'] . $dis_price_total), 'LR', 1); // Total price
            //}
            // ending line of article row
            $pdf->Cell($w1, 1, "", 'LBR', 0); // Art-no
            $pdf->Cell($w2, 1, "", 'LBR', 0); // Art-name
            $pdf->Cell($w3, 1, "", 'LBR', 0); // Amount
            $pdf->Cell($w4, 1, "", 'LBR', 0); // U - Price
            $pdf->Cell($w5, 1, "", 'LBR', 1); // Total price
        }
        $pdf->SetFont('Arial', '', 9);

        // cart net sum
//        if ($discount > 0) {
//    $total_discount = $total*$discount/100;
//}
//else {
//    $total_discount = 0;
//}
//if ($tax > 0) {
//    $total_tax = ($total-$total_discount)*$tax/100;
//}
//else {
//    $total_tax = 0;
//}
//$amount = number_format($total+$shipping+$total_tax-$total_discount,2);
        $total = $dpsc_total;

        if ($dpsc_discount_value > 0) {
            $total_discount = $total * $dpsc_discount_value / 100;
        } else {
            $total_discount = 0.00;
        }
        if ($tax > 0) {
            $total_tax = ($total - $total_discount) * $tax / 100;
        } else {
            $total_tax = 0.00;
        }
        $shipping = $dpsc_shipping_value;
        $amount = number_format($total + $shipping + $total_tax - $total_discount, 2);
        $netsum_str = 'Subtotal:' . ' ' . $dp_shopping_cart_settings['dp_currency_symbol'] . number_format($total,2) . ' ' . $dp_shopping_cart_settings['dp_shop_currency'];
        $pdf->Cell(0, 6, pdf_encode($netsum_str), 0, 1, 'R');

        // discount
        $disf_str = pdf_encode('- Discount:') . ' ' . $dp_shopping_cart_settings['dp_currency_symbol'] . number_format($total_discount,2) . ' ' . $dp_shopping_cart_settings['dp_shop_currency'];
        $pdf->Cell(0, 6, pdf_encode($disf_str), 0, 1, 'R');
        // discount
        $taxf_str = pdf_encode('+ Tax:') . ' ' . $dp_shopping_cart_settings['dp_currency_symbol'] . number_format($total_tax,2) . ' ' . $dp_shopping_cart_settings['dp_shop_currency'];
        $pdf->Cell(0, 6, pdf_encode($taxf_str), 0, 1, 'R');
        // shipping fee
        $shipf_str = pdf_encode('+ Shipping fee:') . ' ' . $dp_shopping_cart_settings['dp_currency_symbol'] . number_format($shipping,2) . ' ' . $dp_shopping_cart_settings['dp_shop_currency'];
        $pdf->Cell(0, 6, pdf_encode($shipf_str), 0, 1, 'R');


        $pdf->SetFont('Arial', 'B', 9);
        $totf_str = pdf_encode('Total:') . ' ' . $dp_shopping_cart_settings['dp_currency_symbol'] . $amount . ' ' . $dp_shopping_cart_settings['dp_shop_currency'];
        $pdf->Cell(00, 6, pdf_encode($totf_str), 0, 1, 'R');
    } else {

    }

    $file_name = 'invoice_' . $invoice . '.pdf';
    $pdf->SetDisplayMode(100);
    $output_path = DP_PLUGIN_DIR . '/pdf/' . $file_name;
    //$output_path_test	= PDF_PLUGIN_URL.'pdfinner/bills/test.pdf';

    if ($test == 0) {
        $pdf->Output($output_path, 'F');
    } else {
        $pdf->Output($output_path_test, 'F');
    }
}

function pdf_encode($data) {

$data = mb_convert_encoding($data, "iso-8859-1", "auto");
// utf8_decode() might be also interesting...

return $data;
}

function address_format($ad, $option='html', $pdf=0) {

$address = NULL;
$name = $ad[f_name];
if (strpos($address, 'NAME') !== false) {
$address = str_replace("NAME", strtoupper($name), $address);
}
if (strpos($address, 'name') !== false) {
$address = str_replace("name", $name, $address);
}

$address = address_token_replacer($address, 'STREET', $ad);
$address = address_token_replacer($address, 'HSNO', $ad);
$address = address_token_replacer($address, 'STRNO', $ad);
$address = address_token_replacer($address, 'STRNAM', $ad);
$address = address_token_replacer($address, 'PB', $ad);
$address = address_token_replacer($address, 'PO', $ad);
$address = address_token_replacer($address, 'PZONE', $ad);
$address = address_token_replacer($address, 'CROSSSTR', $ad);
$address = address_token_replacer($address, 'COLONYN', $ad);
$address = address_token_replacer($address, 'DISTRICT', $ad);
$address = address_token_replacer($address, 'REGION', $ad);
$address = address_token_replacer($address, 'PLACE', $ad);
$address = address_token_replacer($address, 'STATE', $ad);
$address = address_token_replacer($address, 'ZIP', $ad);
$address = address_token_replacer($address, 'COUNTRY', $ad);

foreach ($ad as $p) {
$pdf->Cell(0, 6, utf8_decode($p), 0, 1);
}
return $address;
}

function address_token_replacer($address, $needle, $replace) {
$needle_lower = strtolower($needle);
$key = $needle_lower;
if (($needle == 'PLACE') || ( $needle == 'place')) {
$key = 'town';
}

if (stripos($address, $needle) !== false) {
if (strpos($address, $needle) !== false) {
    $address = str_replace($needle, mb_strtoupper($replace["$key"]), $address);
} else {
    $address = str_replace($needle_lower, $replace["$key"], $address);
}
}
return $address;
}

/**
 * This function creates database table, schedules tasks for wp-cron and creates folder for download file and temporary files
 *
 */
register_activation_hook(__FILE__,'dpsc_install');
function dpsc_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . "dpsc_transactions";
    $old_version = get_option('dpsc_version_info');
    if (!$old_version) {
        if($wpdb->get_var("show tables like '$table_name'") === $table_name) {
            $alter_sql = "ALTER TABLE `$table_name` ADD `phone` VARCHAR( 20 ) NOT NULL AFTER `billing_email`";
            $wpdb->query($alter_sql);
        }
    }
    $dp_shopping_cart_settings = get_option('dp_shopping_cart_settings');
    if ($dp_shopping_cart_settings && (!isset($dp_shopping_cart_settings['mobile_names']) && !isset($dp_shopping_cart_settings['mobile_number'])) && (isset($dp_shopping_cart_settings['safaricom_number']) || isset($dp_shopping_cart_settings['yu_number']) || isset($dp_shopping_cart_settings['zain_number']))) {
        $mobile_name = array();
        $mobile_number = array();
        $mobile_name[] = 'Safaricom M-PESA Number';
        $mobile_number[] = $dp_shopping_cart_settings['safaricom_number'];
        $mobile_name[] = 'YU yuCash Number';
        $mobile_number[] = $dp_shopping_cart_settings['yu_number'];
        $mobile_name[] = 'Zain ZAP Number';
        $mobile_number[] = $dp_shopping_cart_settings['zain_number'];
        $dp_shopping_cart_settings['mobile_names'] = $mobile_name;
        $dp_shopping_cart_settings['mobile_number'] = $mobile_number;
        unset($dp_shopping_cart_settings['safaricom_number']);
        unset($dp_shopping_cart_settings['yu_number']);
        unset($dp_shopping_cart_settings['zain_number']);
        update_option('dp_shopping_cart_settings', $dp_shopping_cart_settings);
    }
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql =  "CREATE TABLE `$table_name` (
                `id` INT( 5 ) NOT NULL AUTO_INCREMENT,
                `invoice` VARCHAR(50) NOT NULL,
                `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `order_time` FLOAT NOT NULL,
                `billing_first_name` VARCHAR( 100 ) NOT NULL,
                `billing_last_name` VARCHAR( 100 ) NOT NULL,
                `billing_country` VARCHAR( 10 ) NOT NULL,
                `billing_address` LONGTEXT NOT NULL,
                `billing_city` VARCHAR(100) NOT NULL,
                `billing_state` VARCHAR(200) NOT NULL,
                `billing_zipcode` VARCHAR(20) NOT NULL,
                `billing_email` VARCHAR(200) NOT NULL,
                `phone` VARCHAR( 20 ) NOT NULL,
                `shipping_first_name` VARCHAR( 100 ) NOT NULL,
                `shipping_last_name` VARCHAR( 100 ) NOT NULL,
                `shipping_country` VARCHAR( 10 ) NOT NULL,
                `shipping_address` LONGTEXT NOT NULL,
                `shipping_city` VARCHAR(100) NOT NULL,
                `shipping_state` VARCHAR(200) NOT NULL,
                `shipping_zipcode` VARCHAR(20) NOT NULL,
                `products` LONGTEXT NOT NULL,
                `payment_option` VARCHAR(100) NOT NULL,
                `discount` FLOAT NOT NULL,
                `tax` FLOAT NOT NULL,
                `shipping` FLOAT NOT NULL,
                `total` FLOAT NOT NULL,
                `tx_id` VARCHAR(50) NOT NULL,
                `payer_email` VARCHAR(200) NOT NULL,
                `payment_status` ENUM ('Pending', 'Paid', 'Canceled'),
                UNIQUE (`invoice`),
                PRIMARY KEY  (id)
                )";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    $table_name2 = $wpdb->prefix . "dpsc_temp_file_log";
    if($wpdb->get_var("show tables like '$table_name2'") != $table_name2) {

        $sql = "CREATE TABLE " . $table_name2 . " (
	  id int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          real_name VARCHAR(250) NOT NULL,
	  saved_name VARCHAR(250) NOT NULL,
          sent_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
          count int(10) DEFAULT 0
	);";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    $dp_dl_expiration_time = get_option('dp_dl_link_expiration_time');
    if (!$dp_dl_expiration_time) {
        $dp_expiration_time = '48';
        update_option('dp_dl_link_expiration_time', $dp_expiration_time);
    }
    if (!is_dir(DP_DOWNLOAD_FILES_DIR)) {
        mkdir(DP_DOWNLOAD_FILES_DIR);
        chmod(DP_DOWNLOAD_FILES_DIR, 0777);
    }
    if (!is_dir(DP_DOWNLOAD_FILES_DIR_TEMP)) {
        mkdir(DP_DOWNLOAD_FILES_DIR_TEMP);
        chmod(DP_DOWNLOAD_FILES_DIR_TEMP, 0777);
    }
    if(is_dir(DP_PLUGIN_DIR.'/cache')) {
        chmod(DP_PLUGIN_DIR.'/cache', 0777);
    }
    else {
        mkdir(DP_PLUGIN_DIR.'/cache');
        chmod(DP_PLUGIN_DIR.'/cache', 0777);
    }
    if(is_dir(DP_PLUGIN_DIR.'/temp')) {
        chmod(DP_PLUGIN_DIR.'/temp', 0777);
    }
    else {
        mkdir(DP_PLUGIN_DIR.'/temp');
        chmod(DP_PLUGIN_DIR.'/temp', 0777);
    }
    if(is_dir(DP_PLUGIN_DIR.'/pdf')) {
        chmod(DP_PLUGIN_DIR.'/pdf', 0777);
    }
    else {
        mkdir(DP_PLUGIN_DIR.'/pdf');
        chmod(DP_PLUGIN_DIR.'/pdf', 0777);
    }
    $date = date('M-d-Y', strtotime("+1 days"));
    $next_time_stamp = strtotime($date) + 18000;
    wp_schedule_event($next_time_stamp, 'daily', 'dp_delete_files_daily');
    update_option('dpsc_version_info', 1.22);
}

function dp_delete_files_daily() {
    $files = glob(DP_DOWNLOAD_FILES_DIR.'/*', GLOB_BRACE);

    if (count($files) > 0) {
        $delete_time = floatval(get_option('dp_dl_link_expiration_time'));
        $yesterday = time() - ($delete_time * 60 * 60);

        usort($files, 'filemtime_compare');

        foreach ($files as $file) {

            if (@filemtime($file) > $yesterday) {
                return;
            }

            unlink($file);

        }

    }
}

register_deactivation_hook(__FILE__, 'dp_deactivate_delete_files');
function dp_deactivate_delete_files() {
    wp_clear_scheduled_hook('dp_delete_files_daily');
}

function dp_system_load_textdomain() {
	$locale = apply_filters( 'wordpress_locale', get_locale() );
	$mofile = DP_PLUGIN_DIR . "/languages/dukapress-$locale.mo";

	if ( file_exists( $mofile ) )
		load_textdomain( 'dp-lang', $mofile );
}
add_action ( 'plugins_loaded', 'dp_system_load_textdomain', 7 );

add_action('wp_dashboard_setup', 'dukapress_dashboard_widget' );
function dukapress_dashboard_widget() {
    global $wp_meta_boxes;
    wp_add_dashboard_widget('dukapress_dashboard_widget', __('DukaPress News'), 'dukapress_rss_dashboard_widget');
}

function dukapress_rss_dashboard_widget() {
    echo '<div class="rss-widget">';
    wp_widget_rss_output('http://dukapress.org/feed/', array('show_author' => 0, 'show_date' => 0, 'items' => 5, 'show_summary' => 0));
    echo "</div>";
}
?>