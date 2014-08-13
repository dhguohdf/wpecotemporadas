jQuery(document).ready(function($) {
	var tbody = $('.tblbody');
	$(".noshow").hide();
	$("#day, #theday, #notify_day").keydown(function(event) {
        // Allow: backspace, delete, tab, escape, and enter
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
             // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) || 
             // Allow: home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        else {
            // Ensure that it is a number and stop the keypress
            if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault(); 
            }   
        }
    });
	
$('.add-button').live('click',function() {
add_new_rule();
return false;
});

$('.meta_upadate').live('click',function() {
updata_meta();
return false;
});

$('.remove_rule').live('click',function(e) {
	e.preventDefault();
	$(this).parent().parent().css("background-color","#FF3700");
    $(this).parent().parent().fadeOut(400, function(){
            $(this).remove();
        });
    return false;
});
$(".edit_rule").live('click',function(e){
						e.preventDefault();
						curr_row = $(this).parent().parent()
						curr_row.css("background-color","#BEF781");
						curr_row.find(".show").hide();
						curr_row.find(".noshow").show();
});


 



function add_new_rule() {
	var res = {loader:$('<div />',{class:'loader'}),container : $('#rule_table')};
	var tbl = $('#rule_table tbody > tr:last > td');			
	counter = tbl.find('#counter').val();	
	 
	role = $('#role').val(); 
	cpt = $('#cpt').val();
	status = $('#status').val();
	day = $('#day').val(); 

	var error = 0;  
	$table = $('#rule_table');
	$rows = $table.find("tbody tr");
	    $rows.each(function() { 
			var oldrole = $(this).find("#therole").val();
			var oldcpt=$(this).find('#thecpt').val();
			if(oldrole === role && oldcpt===cpt ) {error=1;return error;}
			;})
	
	  
	if (error==1){alert('The rule already exist! User Role and Post type cannot identical to other rules.'); return false;}			
		
	
	 if(!role) {alert("choose a role first!"); return false;}
	 if(!cpt) {alert("choose a post type first!"); return false;}
	if(!status) {alert("choose a action first!"); return false;}
	if(!day || day == 0 ) {alert("Day cannot be empty!(numbers only)"); return false;}
				if(!counter) {counter = 0;}else{ counter++;}
				
				 jQuery.ajax({
				 type: 'POST',	 
				 url: ajaxurl,
				 data: ({action : 'adding_rule_ajax', role:role,cpt:cpt,status:status,day:day,counter:counter }),
				 beforeSend:function() {res.container.append(res.loader);},
				 success: function(html) {
				
				 $('#rule_table').last().append(html);
				role = $('#role').val(""); 
				cpt = $('#cpt').val("");
				status = $('#status').val("");
				day = $('#day').val(""); 
				res.container.find(res.loader).remove();
				
				 }
				 });
					 
				
   return false;
	}

function updata_meta() {

				var res = {loader:$('<div />',{class:'loader'}),container : $('#rule_table')};
				 jQuery.ajax({
				 type: 'POST',	 
				 url: ajaxurl,
				 data: ({action : 'updata_meta_ajax'}),
				 beforeSend:function() {res.container.append(res.loader);},
				 success: function(html) {
			
				setTimeout(function(){	$('.loader').append('<h1>It may take awhile, pleas wait.....</h1>');},3000);
				res.container.find(res.loader).remove();
				
				 }
				 });
			  return false;

}


});     

