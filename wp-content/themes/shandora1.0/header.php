<?php
/**
 * Shandora Head Hook
 *
 * @hooke shandora_document_info - 1
 *
 */
	do_atomic('head');
?>
<body id="totop" class="<?php bon_body_class(); ?>">
	<div id="outer-wrap">
	<!-- BEGIN Header -->

	<?php
	/**
	 * Shandora Before Header Hook
	 *
	 *
	 */

	do_atomic('before_header'); ?>

	<header id="header-container" class="container full">

		<div id="header-inner-container" class="container" itemtype="http://schema.org/RealEstateAgent">


			<?php 
			/**
			 * Shandora Header Inner Container Action Hook
			 *
			 * @hooked shandora_close_main_content - 1
			 *
			 */

			do_atomic('header_content'); ?>
			
			
	<!--Start of Zopim Live Chat Script-->
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='//v2.zopim.com/?247FV7eHMObasFPh69cFB897sH2qzubu';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
</script>
<!--End of Zopim Live Chat Script-->	</div>
	</header>
	

	<?php
	/**
	 * Shandora After Header Hook
	 * 
	 * @hooked shandora_get_custom_header - 1
	 *
	 */

	do_atomic('after_header'); ?>
	
	<!-- END Header -->