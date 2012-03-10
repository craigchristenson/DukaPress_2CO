jQuery(document).ready(function () {
    if (jQuery('#dp_settings').length) {
        jQuery('#dp_settings').accordion({active: false, autoHeight: false, collapsible: true});
        jQuery('#po').accordion({active: false, autoHeight: false, collapsible: true});
        jQuery('#product-management').accordion({active: false, autoHeight: false, collapsible: true});
        jQuery('.email-management').accordion({active: false, autoHeight: false, collapsible: true});
    
        jQuery('#dp_discount_submit').live('click', function(){
            var dpsc_discount_code = jQuery("#discount_code").val();
            var dpsc_discount_amount = jQuery("#discount_amount").val();
            var check_one_time = jQuery('input[name=discount_one_time]').is(':checked');
            if (check_one_time) {
                var dpsc_discount_one_time = jQuery("#discount_one_time").val();
            }
            else {
                var dpsc_discount_one_time = 'false';
            }
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: 'action=save_dpsc_discount_code&ajax=true&dpsc_discount_code=' + dpsc_discount_code + '&dpsc_discount_amount=' + dpsc_discount_amount + '&dpsc_discount_one_time=' + dpsc_discount_one_time,
                success: function(msg){
                    jQuery("#discount_code_confirmation").css('display','block').html('Discount Code Successfully Added.');
                    jQuery("#discount_code").val('');
                    jQuery("#discount_amount").val('');
                    jQuery("#discount_one_time").val('');
                    jQuery("div#discount_code_layout").html(msg);
                }
            });
            return false;
        });

        jQuery('span.dpsc_delete_discount_code').live('click', function(){
            var dpsc_delete_discount_code_id = jQuery(this).attr("id");
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: 'action=dpsc_delete_discount_code&id=' + dpsc_delete_discount_code_id + '&ajax=true',
                success:function(msg){
                    jQuery("div#discount_code_layout").html(msg);
                }
            });
        });

        dp_init();
    }

    jQuery('.dp_pagination').live('click', function() {
        var customer_id = jQuery(this).attr('rel');
        jQuery('#dp_action_search_pagi_'+customer_id).css('display', 'inline');
        var page_id = jQuery(this).attr('id');
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: 'action=dp_change_invoices_pagination&page='+page_id+'&customer_id='+customer_id,
            success: function(msg){
                jQuery('#dp_'+customer_id).html(msg);
                jQuery('#dp_action_search_pagi_'+customer_id).css('display', 'none');
            }
        });
    });

    if (jQuery('#dp_addVariation').length) {
        jQuery('#dp_addVariation').live('click', function(){
            jQuery(dp_addVariation);
            jQuery('input[name=varitaionnumber]').val(current);
        });

        jQuery('#dp_deletestring a').live('click', function(){
            var postid=jQuery("#post_ID").val();
            var currentId = jQuery(this).attr('id');
            var mix="#delete"+currentId;
            var substring=jQuery(mix).val();

             jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data:'action=delete_variationdata&name='+substring+'&postid='+postid,
                success:function(msg)
                {
                    jQuery("#result").html(msg);
                }
            });


      });

        jQuery('#dp_save').live('click', function(){
            var i;
            var actionstring='';

            var counter=jQuery("#varitaionnumber").val();
            var postid=jQuery("#post_ID").val();
            var oname=jQuery('#optionname').val();
             actionstring+='optionname='+oname+'&counter='+counter;
            for(i=1;i<=counter;i++)
            {
                var vname='';
                var vprice='';
                vname=jQuery('#vname'+i).val();
                vprice=jQuery('#vprice'+i).val();
                actionstring+=('&vname'+i+'='+vname+'&vprice'+i+'='+vprice);
            }

            //alert(actionstring);
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data:'action=save_variationdata&'+actionstring+'&postid='+postid,
                success:function(msg)
                {
                    jQuery('#dp_var_fields').html('');
                    jQuery('#optionname').val('');
                    jQuery('#vname1').val('');
                    jQuery('#vprice1').val('');
                    jQuery("#result").html(msg);
                    current = 1;
                    jQuery('input[name=varitaionnumber]').val(current);
                }
            });

        });
    }
    jQuery('.deletethis').live('click',function(){
        var action = confirm("Do You really want to perform this action ?");
        if(action != 0){
        jQuery(this).addClass('iwasdeleted');
        jQuery(this).parent('p').parent('td').parent('tr').css('backgroundColor','#D65C5E');
        jQuery(this).parent('p').parent('td').parent('tr').css('background-color','#D65C5E');
        var invoice = jQuery(this).attr('rel');
             var pp_delete_data = {action:'dp_delete_transaction',
                 'invoice':invoice
             };
             jQuery.post(ajaxurl, pp_delete_data, function(response){
                if(response == 'true'){
                    jQuery('.iwasdeleted').parent('p').parent('td').parent('tr').slideUp().remove();
                }
            });
        }
    });
});

var current = 1;

function dp_addVariation() {
    current++;
    var strToAdd = '<p><label for="vname'+current+'">Variation Name</label><input id="vname'+current+'" name="vname'+current+'" size="15" />';

    strToAdd += '<p><label for="vprice'+current+'">Variation price</label>\n\
    <input id="vprice'+current+'" name="vprice'+current+'" size="15" /></p>';
    jQuery('#dp_var_fields').append(strToAdd);
    return current;

}

function dp_init(){
    jQuery('.mobile_payment tr td').find('.remove_row').css('display', 'inline');
    jQuery('.mobile_payment tr td').find('.add_row').css('display', 'none');
    jQuery('.mobile_payment tr td:last').find('.add_row').css('display', 'inline');
    jQuery(".mobile_payment tr:first-child td:last").find('.remove_row').css('display', 'none');
}
function dp_m_rem(clickety){
	jQuery(clickety).parent().parent().remove();
	dp_init();
	return false;
}
function dp_m_add(clickety){
	jQuery('.row_block:last').after(
                jQuery('.row_block:last').clone()
        );
	jQuery('.row_block:last input').attr('value', '');

	dp_init();
	return false;
}