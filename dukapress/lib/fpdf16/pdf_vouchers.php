<?php
include '../../../../../wp-load.php';	
get_currentuserinfo();
global $user_level;

if($user_level != 10) {
	$url = get_option('siteurl') . '/wp-login.php';
	header("Location: $url");exit();
}

$VOUCHER 		= new Voucher();
$VOUCHER->create_pdf(is_dbtable_there('vouchers'),get_option('wps_pdf_voucher_bg'),get_option('wps_pdfFormat'));					
?>