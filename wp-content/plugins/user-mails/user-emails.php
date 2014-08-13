<?php
/*
Plugin Name: User Emails
Description: Changes the default user emails
Version: 1.0
Author: Joe Sexton
Author URI: http://www.josephmsexton.com
*/

/**
 * redefine new user notification function
 *
 * emails new users their login info
 *
 * @author    Joe Sexton <joe@webtipblog.com>
 * @param     integer $user_id user id
 * @param     string $plaintext_pass optional password
 */
if ( !function_exists( 'wp_new_user_notification' ) ) {
    function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
 
        // set content type to html
        add_filter( 'wp_mail_content_type', 'wpmail_content_type' );
 
        // user
        $user = new WP_User( $user_id );
        $userEmail = stripslashes( $user->user_email );
        $user_login = stripslashes( $user->user_login );
        $user_email = stripslashes( $user->user_email );
        $siteUrl = get_site_url();
        $logoUrl = plugin_dir_url( __FILE__ ).'/sitelogo.gif';
 
        $subject = 'Parabéns! Você registrou-se no ecotemporadas.com, vai uma ajudinha aí?‏‎';
        $headers = 'From: Equipe ecotemporadas <nao-responder@ecotemporadas.com>';
 
        // admin email
        $message  = "[ecotemporadas.com] Um novo usuário foi criado"."\r\n\r\n";
        $message .= 'Email: '.$userEmail."\r\n";
        @wp_mail( get_option( 'admin_email' ), 'Novo Usuário criado', $message, $headers );

        if ( empty($plaintext_pass) )
            return;

        $message  = __('<div marginwidth="0" marginheight="0" style="width:100%;margin:0;padding:0;background-color:#f5f5f5;font-family:Helvetica,Arial,sans-serif"><div style="display:block;min-height:5px;background-color:#0D8557"></div><center><div class="yj6qo"></div><div class="adL"></div><table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%"><tbody><tr><td align="center" valign="top" style="border-collapse:collapse;color:#525252"><table border="0" cellpadding="0" cellspacing="0" width="85%"><tbody><tr><td align="center" valign="top" height="20" style="border-collapse:collapse;color:#525252"></td></tr><tr><td align="center" valign="top" style="border-collapse:collapse;color:#525252"><table width="100%" border="0"><tbody><tr><td width="100%" align="center" style="border-collapse:collapse;color:#525252"><table border="0" cellpadding="0" cellspacing="0" style="margin-bottom:10px;"><tbody><tr><td width="641" height="63" align="center" style="border-collapse:collapse;color:#525252;"><img src="http://ecotemporadas.com/wp-content/uploads/ecotempo-email-header.jpg" width="641" height="63" align="center" ><a href="http://ecotemporadas.com" style="width:80px;min-height:34px;display:block" target="_blank"></a></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td align="center" valign="top" style="border-collapse:collapse;color:#525252"><table width="100%" valign="top" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="100%" style="border-collapse:collapse;color:#525252;padding:10px;background-color:rgb(255,255,255);border-color:rgb(221,221,221);border-width:1px;border-radius:5px;border-style:solid;font-size:13px;padding:15px 40px!important" align="left" valign="top"><table width="100%"><tr><td align="center" colspan="3" style="border-collapse:collapse;color:rgb(82,82,82);font-family:Helvetica,Arial,sans-serif;font-size:30px;font-weight:bold;line-height:120%;text-align:center">Parabéns! Você se cadastrou com sucesso!</td></tr><tr><td align="center" valign="top" height="33" style="border-collapse:collapse;color:#525252"></td></tr><tbody><tr><td width="50%" style="border-collapse:collapse;color:#525252;padding:0px!important" align="left" valign="top"><p style="line-height:20px"></p><div style="font-size:17px;color:rgb(83,83,83);text-align:left;font-weight:bold">Olá ');
        $message .= sprintf( __('%s'), $user_login );
        $message .= __('!</div><br>Obrigado por se cadastrar no <strong>ecotemporadas.com</strong>, agradecemos sua confiança e participação.<br><br>Você tem direito a <strong>30 dias grátis</strong> para cadastrar seus anúncios de imóveis para locação.<br><br>Vamos começar?<br><br></td><td width="50%" style="border-collapse:collapse;color:#525252;padding-left:10px!important" align="left" valign="top"><center><img src="http://bit.ly/ecotempo-email-cadastro" width="290" style="border:0;min-height:auto;line-height:120%;outline:none;text-decoration:none;display:block;margin:0;margin-top:0px" alt="Deliver customer wow!"></center></td></tr><tr><td width="100%" align="left" valign="top" colspan="2"><hr style="margin: 40px 0;border: 0;height: 1px;background: #333;background-image: -webkit-linear-gradient(left, #ccc, #333, #ccc);background-image: -moz-linear-gradient(left, #ccc, #333, #ccc);background-image:-ms-linear-gradient(left, #ccc, #333, #ccc);background-image: -o-linear-gradient(left, #ccc, #333, #ccc);"><p style="line-height:20px"></p><div style="font-size:17px;color:rgb(83,83,83);text-align:left;font-weight:bold">Comece a anunciar agora mesmo:</div><br>Acesse seu painel de controle para gerenciar seus anúncios, não se preocupe é muito simples:<br><br><center><a href="http://ecotemporadas.com/entrar" style="text-decoration:none;color:#0D8557;text-decoration:none" target="_blank"><div style="width:170px;padding:10px 0;background:#F28212;color:#fff;border-radius:5px;font-weight:700;font-style:normal;font-size:14px;text-decoration:none;border:1px solid #F28212">Painel do Anunciante</div></a></center>Suas informações secretas:<br><br><table border="0" cellpadding="0" cellspacing="0"><tbody><tr><td width="75" align="left" style="border-collapse:collapse;color:#525252">Login:</td><td align="left" style="border-collapse:collapse;color:#525252"><strong><span style="text-decoration:none;color:#0D8557" target="_blank">');
        $message .= sprintf( __('%s'), $user_login );
        $message .= __('</span></strong></td></tr><tr><td width="75" align="left" style="border-collapse:collapse;color:#525252">Senha:</td><td align="left" style="border-collapse:collapse;color:#525252"><span style="text-decoration:none;color:#0D8557;font-weight:bold" target="_blank">');
        $message .= sprintf(__('%s'), $plaintext_pass);
        $message .= __('</span></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td align="center" valign="top" style="border-collapse:collapse;color:#525252">');
        $message .= __('<tr><td align="center" valign="top" style="border-collapse:collapse;color:#525252"><table width="100%" valign="top" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" valign="top" height="10" style="border-collapse:collapse;color:#525252"></td></tr><tr><td width="100%" style="border-collapse:collapse;color:#525252;padding:10px;background-color:rgb(255,255,255);border-color:rgb(221,221,221);border-width:1px;border-bottom:0;border-top-left-radius:5px;border-top-right-radius:5px;border-style:solid;font-size:13px;padding:15px 40px!important" align="left" valign="top"><p style="line-height:20px"></p><div style="font-size:17px;color:rgb(83,83,83);text-align:left;font-weight:bold">Opa, vai uma ajudinha aí?</div><br>Caso tenha alguma dúvida ou problema no nosso website, não se preocupe, estaremos a sua disposição para o que for preciso!<br>Conte com nosso suporte 24 horas: <a href="mailto:contato@ecotemporadas.com" style="text-decoration:none;color:#0D8557" target="_blank">contato@<span class="il">ecotemporadas</span>.com</a><br>Ou fale conosco pelo nosso chat do website: <a href="http://www.ecotemporadas.com" style="text-decoration:none;color:#0D8557" target="_blank"><span class="il">Zopim</span>.com</a>.<br><br>Visite também nosso <span>facebook</span>: <a href="http://facebook.com/ecotemporadas" target="_blank">Facebook</a></td></tr>');
        $message .= __('<tr valign="middle"><td width="100%" align="left" valign="middle" style="border-collapse:collapse;color:#525252;padding:10px;background-color:rgb(255,255,255);border-color:rgb(221,221,221);border-width:1px;border-bottom-left-radius:5px;border-bottom-right-radius:5px;border-style:solid;font-size:13px;padding:0!important;vertical-align:middle"><br><p style="line-height:20px;padding:0 40px;margin:0">Atenciosamente,<br><img src="http://ecotemporadas.com/wp-content/uploads/ecotempo-email-signature.jpg" alt="Equipe Ecotemporadas" style="border:0;min-height:auto;line-height:120%;outline:none;text-decoration:none;display:block;margin:0;min-width:75px"></p><br></td></tr><tr><td align="center" valign="top" height="33" style="border-collapse:collapse;color:#525252"></td></tr><tr><td style="border-collapse:collapse;color:#525252"><table width="100%" border="0" style="font-size:10px;line-height:120%;color:#999;text-align:center"><tbody><tr><td style="border-collapse:collapse;color:#525252"><a href="http://ecotemporadas.com" style="color:#999;text-decoration:none" target="_blank"><img src="http://ecotemporadas.com/wp-content/uploads/ecotempo-email-footer.jpg" alt="ecotemporadas" title="ecotemporadas" width="80" height="34" border="0" style="border:0;min-height:auto;line-height:120%;outline:none;text-decoration:none;margin:0 auto;color:#999;font-family:Helvetica,Arial,sans-serif;font-size:25px;vertical-align:text-bottom;text-align:center"></a><br><br><a href="http://ecotemporadas.com/anunciar-seu-imovel/?utm_campaign=EmailDashboard" style="color:#999;text-decoration:none" target="_blank">Painel do Anunciante</a> &nbsp;&nbsp;|&nbsp;&nbsp;<a href="http://ecotemporadas.com/wp-admin/post-new.php?post_type=listing?utm_campaign=EmailAccount" style="color:#999;text-decoration:none" target="_blank">Criar Anúncio</a> &nbsp;&nbsp;|&nbsp;&nbsp;<a href="http://facebook.com/ecotemporadas" style="color:#999;text-decoration:none" target="_blank">Facebook</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="http://ecotemporadas.com/fale-conosco/" style="color:#999;text-decoration:none" target="_blank">Fale com a gente</a><br><br>ecotemporadas.com 2014 Todos os direitos reservados<br>Produced by <a href="http://eaxdesign.com.br" target="_blank">eaxdesign.com.br</a> with WordPress</td></tr></tbody></table></td></tr>');
 
        @wp_mail( $userEmail, $subject, $message, $headers );
 
        // remove html content type
        remove_filter ( 'wp_mail_content_type', 'wpmail_content_type' );
    }
}
 
/**
 * wpmail_content_type
 * allow html emails
 *
 * @author Joe Sexton <joe@webtipblog.com>
 * @return string
 */
function wpmail_content_type() {
 
    return 'text/html';
}