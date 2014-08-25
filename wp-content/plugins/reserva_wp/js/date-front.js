/**
 * Created by root on 25/08/14.
 */
setTimeout(function(){
    jQuery("#bookingdatepicker td").unbind("click");
    jQuery("#bookingdatepicker td a").unbind("click");
    jQuery("#bookingdatepicker td").each(function () {
        jQuery(this).addClass('ui-datepicker-unselectable');
    });
}, 1000);
jQuery('a').on('click', function () {
    jQuery("#bookingdatepicker td").unbind("click");
    jQuery("#bookingdatepicker td a").unbind("click");
    jQuery("#bookingdatepicker td").each(function () {
        jQuery(this).addClass('ui-datepicker-unselectable');
    });
});
//jQuery("#bookingdatepicker").datepicker('disable');