<?php

	/**
	 * Fields
	 */

	function photoboard_photoswipe_settings_field_wrapper_atts() {
		$options = photoboard_photoswipe_get_theme_options();
		?>
		<input type="text" name="photoboard_photoswipe_theme_options[wrapper_atts]" class="large-text" id="ps-wrapper-atts" value="<?php echo stripslashes( esc_attr( $options['wrapper_atts'] ) ); ?>" /><br />
		<label class="description" for="ps-wrapper-atts"><?php _e( 'Attributes to apply to the PhotoSwipe wrapper', 'photoboard_photoswipe' ); ?></label>
		<?php
	}

	function photoboard_photoswipe_settings_field_link_atts() {
		$options = photoboard_photoswipe_get_theme_options();
		?>
		<input type="text" name="photoboard_photoswipe_theme_options[link_atts]" class="large-text" id="ps-link-atts" value="<?php echo stripslashes( esc_attr( $options['link_atts'] ) ); ?>" /><br />
		<label class="description" for="ps-link-atts"><?php _e( 'Attributes to apply to individual photo links', 'photoboard_photoswipe' ); ?></label>
		<?php
	}

	function photoboard_photoswipe_settings_field_img_atts() {
		$options = photoboard_photoswipe_get_theme_options();
		?>
		<input type="text" name="photoboard_photoswipe_theme_options[img_atts]" class="large-text" id="ps-img-atts" value="<?php echo stripslashes( esc_attr( $options['img_atts'] ) ); ?>" /><br />
		<label class="description" for="ps-img-atts"><?php _e( 'Attributes to apply to gallery images', 'photoboard_photoswipe' ); ?></label>
		<?php
	}

	function photoboard_photoswipe_settings_field_caption_atts() {
		$options = photoboard_photoswipe_get_theme_options();
		?>
		<input type="text" name="photoboard_photoswipe_theme_options[caption_atts]" class="large-text" id="ps-caption-atts" value="<?php echo stripslashes( esc_attr( $options['caption_atts'] ) ); ?>" /><br />
		<label class="description" for="ps-caption-atts"><?php _e( 'Attributes to apply to photo captions', 'photoboard_photoswipe' ); ?></label>
		<?php
	}


	/**
	 * Menu
	 */

	// Register the theme options page and its fields
	function photoboard_photoswipe_theme_options_init() {
		register_setting(
			'photoboard_photoswipe_options', // Options group, see settings_fields() call in photoboard_photoswipe_theme_options_render_page()
			'photoboard_photoswipe_theme_options', // Database option, see photoboard_photoswipe_get_theme_options()
			'photoboard_photoswipe_theme_options_validate' // The sanitization callback, see photoboard_photoswipe_theme_options_validate()
		);

		// Register our settings field group
		add_settings_section(
			'general', // Unique identifier for the settings section
			'', // Section title (we don't want one)
			'__return_false', // Section callback (we don't want anything)
			'photoboard_photoswipe_theme_options' // Menu slug, used to uniquely identify the page; see photoboard_photoswipe_theme_options_add_page()
		);

		// Register our individual settings fields
		// add_settings_field( $id, $title, $callback, $page, $section );
		// $id - Unique identifier for the field.
		// $title - Setting field title.
		// $callback - Function that creates the field (from the Theme Option Fields section).
		// $page - The menu page on which to display this field.
		// $section - The section of the settings page in which to show the field.

		add_settings_field( 'photoboard_photoswipe_wrapper_atts', __( 'Wrapper Attributes', 'photoboard_photoswipe' ), 'photoboard_photoswipe_settings_field_wrapper_atts', 'photoboard_photoswipe_theme_options', 'general' );
		add_settings_field( 'photoboard_photoswipe_link_atts', __( 'Link Attributes', 'photoboard_photoswipe' ), 'photoboard_photoswipe_settings_field_link_atts', 'photoboard_photoswipe_theme_options', 'general' );
		add_settings_field( 'photoboard_photoswipe_img_atts', __( 'Image Class', 'photoboard_photoswipe' ), 'photoboard_photoswipe_settings_field_img_atts', 'photoboard_photoswipe_theme_options', 'general' );
		add_settings_field( 'photoboard_photoswipe_caption_atts', __( 'Caption Attributes', 'photoboard_photoswipe' ), 'photoboard_photoswipe_settings_field_caption_atts', 'photoboard_photoswipe_theme_options', 'general' );
	}
	add_action( 'admin_init', 'photoboard_photoswipe_theme_options_init' );



	// Create theme options menu
	// The content that's rendered on the menu page.
	function photoboard_photoswipe_theme_options_render_page() {
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e( 'PhotoSwipe Photo Galleries', 'photoboard_photoswipe' ); ?></h2>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'photoboard_photoswipe_options' );
					do_settings_sections( 'photoboard_photoswipe_theme_options' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}



	// Add the theme options page to the admin menu
	function photoboard_photoswipe_theme_options_add_page() {
		$theme_page = add_submenu_page(
			'upload.php', // parent slug
			'Photo Galleries', // Label in menu
			'Photo Galleries', // Label in menu
			'edit_theme_options', // Capability required
			'photoboard_photoswipe_theme_options', // Menu slug, used to uniquely identify the page
			'photoboard_photoswipe_theme_options_render_page' // Function that renders the options page
		);
	}
	add_action( 'admin_menu', 'photoboard_photoswipe_theme_options_add_page' );



	// Restrict access to the theme options page to admins
	function photoboard_photoswipe_option_page_capability( $capability ) {
		return 'edit_theme_options';
	}
	add_filter( 'option_page_capability_photoboard_photoswipe_options', 'photoboard_photoswipe_option_page_capability' );



	/**
	 * Process Options
	 */

	// Get the current options from the database.
	// If none are specified, use these defaults.
	function photoboard_photoswipe_get_theme_options() {
		$saved = (array) get_option( 'photoboard_photoswipe_theme_options' );
		$defaults = array(
			'wrapper_atts' => '',
			'link_atts' => '',
			'img_atts' => '',
			'caption_atts' => '',
		);

		$defaults = apply_filters( 'photoboard_photoswipe_default_theme_options', $defaults );

		$options = wp_parse_args( $saved, $defaults );
		$options = array_intersect_key( $options, $defaults );

		return $options;
	}



	// Sanitize and validate updated theme options
	function photoboard_photoswipe_theme_options_validate( $input ) {
		$output = array();

		if ( isset( $input['wrapper_atts'] ) && ! empty( $input['wrapper_atts'] ) )
			$output['wrapper_atts'] = wp_filter_post_kses( $input['wrapper_atts'] );

		if ( isset( $input['link_atts'] ) && ! empty( $input['link_atts'] ) )
			$output['link_atts'] = wp_filter_post_kses( $input['link_atts'] );

		if ( isset( $input['img_atts'] ) && ! empty( $input['img_atts'] ) )
			$output['img_atts'] = wp_filter_post_kses( $input['img_atts'] );

		if ( isset( $input['caption_atts'] ) && ! empty( $input['caption_atts'] ) )
			$output['caption_atts'] = wp_filter_post_kses( $input['caption_atts'] );

		return apply_filters( 'photoboard_photoswipe_theme_options_validate', $output, $input );
	}