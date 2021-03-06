<?php
/**
 * CPAC_Column_Media_Alternate_Text
 *
 * @since 2.0
 */
class CPAC_Column_Media_Alternate_Text extends CPAC_Column {

	/**
	 * @see CPAC_Column::init()
	 * @since 2.2.1
	 */
	public function init() {

		parent::init();

		// Properties
		$this->properties['type']	 = 'column-alternate_text';
		$this->properties['label']	 = __( 'Alt', 'cpac' );
	}

	/**
	 * @see CPAC_Column::get_value()
	 * @since 2.0
	 */
	function get_value( $id ) {

		return $this->strip_trim( get_post_meta( $id, '_wp_attachment_image_alt', true ) );
	}
}