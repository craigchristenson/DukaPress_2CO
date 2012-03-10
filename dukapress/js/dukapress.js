jQuery(document).ready(function () {
    jQuery('.dpsc_error_msg').hide();

    jQuery("form[ID^=dpsc_product_form_]").submit(function() {
        jQuery('div.dpsc_update_icon', jQuery(this)).css('display', 'inline');
        file_upload_elements = jQuery.makeArray(jQuery("input[type=file]", jQuery(this)));
        if(file_upload_elements.length > 0) {
            return true;
        } else {
            var productId = jQuery(this).attr('id').split('_')[3];
            var buy_now_present = jQuery('#dpsc_buy_now_button_present_'+productId).val();
            var form_values = jQuery(this).serialize();
            if (buy_now_present == '1') {
                jQuery.ajax({
                            type: "POST",
                            url: dpsc_js.ajaxurl,
                            data: form_values,
                            success: function(response){
                                jQuery('div#dpsc_paypal_form_'+productId).html(response);
                                jQuery('#dpsc_payment_form').submit();
                            }
                });
            }
            else if (buy_now_present == '2') {
              jQuery.ajax({
                            type: "POST",
                            url: dpsc_js.ajaxurl,
                            data: form_values,
                            success: function(response){
                                window.location = response;
                            }
                });
            }
			else if (buy_now_present == '3') {
				var affiliate_url = jQuery('#dpsc_affiliate_url_'+productId).val();
				window.open(affiliate_url,'dp_product_'+productId);
				jQuery('div.dpsc_update_icon', jQuery(this)).css('display', 'none');
            }
            else {
                jQuery.post( dpsc_js.dpsc_url+"/index.php?ajax=true", form_values, function(returned_data) {
                    eval(returned_data);
                });
            }
            return false;
        }
    });

    jQuery("form[name=dpsc_inquiry_form]").submit(function() {
        jQuery('.dpsc_error_msg').hide();
        var dpsc_name = jQuery('#dpsc_inquiry_from_name').val();
        var dpsc_email = jQuery('#dpsc_inquiry_from').val();
        var dpsc_subject = jQuery('#dpsc_inquiry_subject').val();
        var dpsc_msg = jQuery('#dpsc_inquiry_custom_msg').val();

        var no_error = true;
        if (!dpsc_name) {
            jQuery('#NameError').show();
            no_error = false;
        }
        if (!dpsc_email) {
            jQuery('#emailError').show();
            no_error = false;
        }
        if (!dpsc_subject) {
            jQuery('#subjectError').show();
            no_error = false;
        }
        if (!dpsc_msg) {
            jQuery('#contentError').show();
            no_error = false;
        }

        if (no_error) {
            return true;
        }
        else {
            return false;
        }
    });

    jQuery('.dpsc_empty_your_cart').live('click', function() {
        jQuery.ajax({
                    type: "POST",
                    url: dpsc_js.ajaxurl,
                    data: 'action=dpsc_empty_your_cart',
                    success: function(response){
                        eval(response);
                    }
        });
        return false;
    });

    jQuery("form.product_update").livequery(function(){
        jQuery(this).submit(function() {
            form_values = "ajax=true&";
            form_values += jQuery(this).serialize();
            jQuery.post( dpsc_js.dpsc_url+'/index.php', form_values, function(returned_data) {
            eval(returned_data);
            });
            return false;
        });
    });

    var validateCode = jQuery('#dpsc_check_discount_code');
    jQuery("#dpsc_validate_discount_code").live('click', function(){
        var dpsc_code = jQuery("#dpsc_discount_code").val();
        validateCode.removeClass('dpsc_discount_code_invalid').css('display', 'block').html('Checking...');
        var dpsc_discount_code = "ajax=true&";
        dpsc_discount_code += "dpsc_ajax_action=validate_discount_code&dpsc_check_code=" + dpsc_code;
        jQuery.post(dpsc_js.dpsc_url+'/index.php', dpsc_discount_code, function(code_returned_data){
            eval(code_returned_data);
        });
        return false;
    });

    jQuery('#dpsc_make_payment').live('click', function(){
        jQuery('#firstNameError').hide();
        jQuery('#lastNameError').hide();
        jQuery('#addressError').hide();
        jQuery('#cityError').hide();
        jQuery('#stateError').hide();
        jQuery('#postelError').hide();
        jQuery('#countryError').hide();
        jQuery('#emailError').hide();
        jQuery('#phoneError').hide();
        jQuery('#shipCountryError').hide();
        jQuery('#shipPostalError').hide();
        jQuery('#shipStateError').hide();
        jQuery('#shipAddressError').hide();
        jQuery('#shipLNameError').hide();
        jQuery('#shipFNameError').hide();
        jQuery('#shipCityError').hide();

        var check_other_shipping = jQuery('input[name=other_shipping_present]').val();

        if (check_other_shipping == 'true') {
          var cstom_shipping_val = jQuery('input[name=custom_shipping_value]').val();
          if (cstom_shipping_val == 'no_val') {
            return;
          }
        }

        jQuery('#dpsc_po_error').css('display', 'none');
        var check = jQuery('input[name=dpsc_po]').is(':checked');
        var check_hidden = jQuery('#dpsc_po_hidden').length;
        if (check_hidden == 0) {
            if (check) {
                var payment_option = jQuery('input:radio[name=dpsc_po]:checked').val();

            }
            else {
                jQuery('#dpsc_po_error').css('display', 'block').html('Please select one of the Payment option.').addClass('dpsc_error_msg');
                return;
            }
        }
        else {
            var payment_option = jQuery('#dpsc_po_hidden').val();
        }
        var payment_discount_check = jQuery('input[name=dpsc_discount_code_payment]').length;
        var payment_discount_string = '';
        if (payment_discount_check != 0) {


            var payment_discount = jQuery('input[name=dpsc_discount_code_payment]').val();

            payment_discount_string = '&discount=' + payment_discount;
        }

        var dpsc_b_fname = null;
        dpsc_b_fname=jQuery('#b_firstname').val();
        var dpsc_b_lname = null;
        dpsc_b_lname=jQuery('#b_lastname').val();
        var dpsc_b_country = null;
        dpsc_b_country=jQuery('#b_country option:selected').val();
        var dpsc_b_address = null;
        dpsc_b_address=jQuery('#b_address').val();
        var dpsc_b_city =null;
        dpsc_b_city=jQuery('#b_city').val();
        var dpsc_b_state =null;
        dpsc_b_state=jQuery('#b_state').val();
        var dpsc_b_zipcode = null;
        dpsc_b_zipcode=jQuery('#b_zipcode').val();
        var dpsc_b_email = null;
        dpsc_b_email=jQuery('#b_email').val();
        var dpsc_b_phone =null;
        dpsc_b_phone=jQuery('#b_phone').val();
        var dpsc_s_fname =null;
        dpsc_s_fname=jQuery('#s_firstname').val();
        var dpsc_s_lname = null;
        dpsc_s_lname=jQuery('#s_lastname').val();
        var dpsc_s_country =null;
        dpsc_s_country=jQuery('#s_country option:selected').val();
        var dpsc_s_address = null;
        dpsc_s_address=jQuery('#s_address').val();
        var dpsc_s_city = null;
        dpsc_s_city=jQuery('#s_city').val();
        var dpsc_s_state =null;
        dpsc_s_state =jQuery('#s_state').val();
        var dpsc_s_zipcode =null;
        dpsc_s_zipcode =jQuery('#s_zipcode').val();

        var dpsc_diff_ship = jQuery('input[name=dpsc_contact_different_ship_address]').is(':checked');

        if (dpsc_diff_ship) {
            var diff_ship = 'true';
        }
        else {
            var diff_ship = 'false';
        }


        if (dpsc_diff_ship) {
            if(!dpsc_s_fname)
            {
                jQuery('#shipFNameError').show();
            }
            if(!dpsc_s_lname)
            {
                jQuery('#shipLNameError').show();
            }
            if(!dpsc_s_address)
            {
                jQuery('#shipAddressError').show();
            }
            if(!dpsc_s_city)
            {
                jQuery('#shipCityError').show();
            }
            if(!dpsc_s_state)
            {
                jQuery('#shipStateError').show();
            }
            if(!dpsc_s_zipcode)
            {
                jQuery('#shipPostalError').show();
            }
            if(!dpsc_s_country)
            {
                jQuery('#shipCountryError').show();
            }
        }

        var regexLetter = /[a-zA-z]/;
        var regexNum = /^[0-9]/;
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

        if(!regexLetter.test(dpsc_b_fname)){
            jQuery('#firstNameError').html("Please write only text here");
            dpsc_b_fname=null;
        }
        if(!regexLetter.test(dpsc_b_lname)){
            jQuery('#lastNameError').html("Please write only text here");
            dpsc_b_lname=null;
        }
         if (dpsc_diff_ship) {
            if(!regexLetter.test(dpsc_s_fname)){
                jQuery('#shipFNameError').show();
                jQuery('#shipFNameError').html("Please write only text here");
                dpsc_s_fname=null;
            }
            if(!regexLetter.test(dpsc_s_lname)){
                jQuery('#shipLNameError').show();
                jQuery('#shipLNameError').html("Please write only text here");
                  dpsc_s_lname=null;
            }
         }
        if(!regexNum.test(dpsc_b_phone)){
            jQuery('#phoneError').html("Please write only numbers here");
            dpsc_b_phone=null;
        }

        if(!emailReg.test(dpsc_b_email))
            {
                jQuery('#emailError').html("Please put the right email id");
                dpsc_b_email=null;
            }
        if(!dpsc_b_fname)
        {

            jQuery('#firstNameError').show();

        }
        if(!dpsc_b_lname)
        {
            jQuery('#lastNameError').show();

        }
        if(!dpsc_b_country)
        {

            jQuery('#countryError').show();

        }
        if(!dpsc_b_address)
        {
            jQuery('#addressError').show();

        }
        if(!dpsc_b_city)
        {

            jQuery('#cityError').show();

        }
        if(!dpsc_b_state)
        {
            jQuery('#stateError').show();

        }
        if(!dpsc_b_zipcode)

        {
            jQuery('#postelError').show();

        }
        if(!dpsc_b_email)
        {
            jQuery('#emailError').show();

        }
        if(!dpsc_b_phone)
        {
            jQuery('#phoneError').show();

        }

        if (dpsc_diff_ship) {
            if(!dpsc_b_phone || !dpsc_b_email || !dpsc_b_zipcode || !dpsc_b_state || !dpsc_b_city || !dpsc_b_address || !dpsc_b_country || !dpsc_b_lname || !dpsc_b_fname  || !dpsc_s_fname || !dpsc_s_lname || !dpsc_s_address || !dpsc_s_city || !dpsc_s_state || !dpsc_s_zipcode || !dpsc_s_country )
            {
                return;
            }
        }
        if(!dpsc_b_phone || !dpsc_b_email || !dpsc_b_zipcode || !dpsc_b_state || !dpsc_b_city || !dpsc_b_address || !dpsc_b_country || !dpsc_b_lname || !dpsc_b_fname)
        {
            return ;
        }
        else{
            if (dpsc_diff_ship) {
                var ship_details = '&s_fname=' + dpsc_s_fname + '&s_lname=' + dpsc_s_lname + '&s_country=' + dpsc_s_country + '&s_address=' + dpsc_s_address + '&s_city=' + dpsc_s_city + '&s_state=' + dpsc_s_state + '&s_zip=' + dpsc_s_zipcode;
            }
            else {
                var ship_details = '';
            }
            var shipping = jQuery('input[name=custom_shipping_value]').val();
            var ship_string = '';
            if (shipping != 'no_val') {
                ship_string = "&shipping="+shipping;
            }
            var dpsc_payment = "ajax=true&dpsc_ajax_action=dpsc_payment_option"+ship_string+"&payment_selected=" + payment_option + payment_discount_string + '&b_fname=' + dpsc_b_fname + '&b_lname=' + dpsc_b_lname + '&b_country=' + dpsc_b_country + '&b_address=' + dpsc_b_address + '&b_city=' + dpsc_b_city + '&b_state=' + dpsc_b_state + '&b_zip=' + dpsc_b_zipcode + '&b_email=' + dpsc_b_email + '&ship_present=' + diff_ship + ship_details + '&b_phone=' + dpsc_b_phone;
            jQuery.post(dpsc_js.dpsc_url+'/index.php', dpsc_payment, function(data) {
            eval(data);
        });
        }
    });

    jQuery('#dpsc_contact_different_ship_address').live('click', function(){
       var shipping_check =  jQuery('input[name=dpsc_contact_different_ship_address]').is(':checked');
       if (shipping_check) {
           jQuery('#dpsc_shipping_details').css('display', 'block');
       }
       else {
           jQuery('#dpsc_shipping_details').css('display', 'none');
       }
    });

    jQuery(".dpsc_image_section .dpsc_image_tab .dpsc_tabs li:first-child .dpsc_thumb_tab").addClass('current');
    jQuery(".dpsc_image_section .dpsc_image_tab .dpsc_tabs .dpsc_thumb_tab").mouseover(function() {
            jQuery(this).addClass('current').parent().siblings().children().removeClass('current');
            var prod_id = jQuery(this).attr('id');
            var href = jQuery(this).attr('href');
            var new_src = dpsc_js.tim_url + href + '&w=' + dpsc_js.width + '&h=' + dpsc_js.height + '&zc=1';
            var check_no_effect = jQuery('.dpsc_main_image a').length;
            if (check_no_effect > 0) {
                jQuery('.main_' + prod_id + ' a').attr('href', href).children().attr('src', new_src);
                jQuery('.MagicZoomBigImageCont img').attr('src', href);
            }
            else {
                jQuery('.main_' + prod_id + ' img').attr('src', new_src);
            }
    });

    if (jQuery().jqzoom) {
        var jqzoom_option = {
            zoomWidth: 350,
            zoomHeight: 350,
            xOffset: 20,
            title: false,
            lens:false,
            hideEffect:'fadeout'
        }
        jQuery('.dp_jqzoom').jqzoom(jqzoom_option);
    }

    jQuery('#order_log').accordion({active: false, autoHeight: false, collapsible: true});
});