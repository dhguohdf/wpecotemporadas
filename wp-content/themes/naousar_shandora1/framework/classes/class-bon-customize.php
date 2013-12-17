<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Class Bon Form
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.0
 * @package 	BonFramework
 * @category 	Core
 *
 *
*/ 

/**
 * Textarea customize control class.
 *
 * @since 1.0
 */
class BON_Customize_Control_Textarea extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since 1.0
	 */
	public $type = 'textarea';

	/**
	 * Displays the textarea on the customize screen.
	 *
	 * @since 1.0
	 */
	public function render_content() { ?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div class="customize-control-content">
				<textarea class="widefat" cols="45" rows="5" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
			</div>
		</label>
	<?php }
}

?>