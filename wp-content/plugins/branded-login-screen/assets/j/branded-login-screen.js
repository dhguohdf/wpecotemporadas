jQuery(document).ready(function( $ ) {



	$("input#wp-submit").removeClass().addClass("shiny-button");



	$('#backtoblog a').prop('title','Voltar à página principal');



	$('form#loginform').prepend('<h2>Faça seu login</h2><br class="clear">');

	$('form#lostpasswordform').prepend('<h2>insira seu email para receber sua nova senha</h2><br class="clear">');

	$('form#resetpassform').prepend('<h2>Insira sua nova senha abaixo</h2><br class="clear">');



	$('form#registerform').prepend('<h2>Create your own personalized account. A password will be<br\>e-mailed to you.</h2><br class="clear">');

	$('form').prepend('<p class="ver">Branded Login Screen 3.0</p>');



	//TODO: make the alert boxes look prettier. :)



	$("p.reset-pass:contains('Enter your new password below')").hide();



	$("p.reset-pass:contains('Your password has been reset')").show().addClass('backtologin').removeClass('message').removeClass('reset-pass');

});