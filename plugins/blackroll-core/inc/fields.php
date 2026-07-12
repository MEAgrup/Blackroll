<?php
/**
 * Native field panels (D3 = no SCF). Hand-built meta boxes for shade/project,
 * with a lightweight wp.media image picker. Data shape is identical to what
 * SCF would produce; only the UX is native.
 *
 * @package BlackrollCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box( 'blackroll-shade-fields', __( 'Shade Details', 'blackroll-core' ), 'blackroll_render_shade_box', 'shade', 'normal', 'high' );
		add_meta_box( 'blackroll-project-fields', __( 'Project Details', 'blackroll-core' ), 'blackroll_render_project_box', 'project', 'normal', 'high' );
	}
);

/**
 * Render a single field row.
 *
 * @param string $key    Meta key.
 * @param array  $def    Field definition (type,label).
 * @param mixed  $value  Current value.
 */
function blackroll_render_field( $key, $def, $value ) {
	$id = 'blackroll_' . $key;
	echo '<p class="blackroll-field blackroll-field--' . esc_attr( $def['type'] ) . '">';
	printf( '<label for="%s"><strong>%s</strong></label><br>', esc_attr( $id ), esc_html( $def['label'] ) );

	if ( 'boolean' === $def['type'] ) {
		printf(
			'<input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s>',
			esc_attr( $id ),
			checked( (bool) $value, true, false )
		);
	} elseif ( 'integer' === $def['type'] ) {
		// Image picker for *_image / preview_* fields; plain number otherwise.
		$is_image = ( false !== strpos( $key, 'image' ) || 0 === strpos( $key, 'preview' ) || 'swatch_image' === $key );
		printf(
			'<input type="number" id="%1$s" name="%1$s" value="%2$s" class="small-text blackroll-media-id" min="0" step="1">',
			esc_attr( $id ),
			esc_attr( (string) $value )
		);
		if ( $is_image ) {
			echo ' <button type="button" class="button blackroll-media-pick" data-target="' . esc_attr( $id ) . '">' . esc_html__( 'Select image', 'blackroll-core' ) . '</button>';
			$thumb = $value ? wp_get_attachment_image( (int) $value, array( 60, 60 ) ) : '';
			echo ' <span class="blackroll-media-preview" data-for="' . esc_attr( $id ) . '">' . wp_kses_post( $thumb ) . '</span>';
		}
	} else {
		printf(
			'<input type="text" id="%1$s" name="%1$s" value="%2$s" class="widefat">',
			esc_attr( $id ),
			esc_attr( (string) $value )
		);
	}
	echo '</p>';
}

/**
 * Render the shade meta box.
 *
 * @param WP_Post $post Post.
 */
function blackroll_render_shade_box( $post ) {
	wp_nonce_field( 'blackroll_save_meta', 'blackroll_meta_nonce' );
	foreach ( blackroll_shade_meta_schema() as $key => $def ) {
		blackroll_render_field( $key, $def, get_post_meta( $post->ID, $key, true ) );
	}
	echo '<p class="description">' . esc_html__( 'Assign the Color Series (Black/White) in the sidebar. SKU code is entered verbatim from the client sheet.', 'blackroll-core' ) . '</p>';
}

/**
 * Render the project meta box.
 *
 * @param WP_Post $post Post.
 */
function blackroll_render_project_box( $post ) {
	wp_nonce_field( 'blackroll_save_meta', 'blackroll_meta_nonce' );
	foreach ( blackroll_project_meta_schema() as $key => $def ) {
		blackroll_render_field( $key, $def, get_post_meta( $post->ID, $key, true ) );
	}
	echo '<p class="description">' . esc_html__( 'Set the Project Type (Residensial/Kantor/Apartemen) in the sidebar. Gallery IDs = comma-separated attachment IDs.', 'blackroll-core' ) . '</p>';
}

/**
 * Persist meta on save.
 *
 * @param int $post_id Post ID.
 */
add_action(
	'save_post',
	function ( $post_id ) {
		if ( ! isset( $_POST['blackroll_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['blackroll_meta_nonce'] ), 'blackroll_save_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$type   = get_post_type( $post_id );
		$schema = ( 'shade' === $type ) ? blackroll_shade_meta_schema() : ( ( 'project' === $type ) ? blackroll_project_meta_schema() : array() );

		foreach ( $schema as $key => $def ) {
			$field = 'blackroll_' . $key;
			if ( 'boolean' === $def['type'] ) {
				update_post_meta( $post_id, $key, isset( $_POST[ $field ] ) ? 1 : 0 );
			} elseif ( 'integer' === $def['type'] ) {
				update_post_meta( $post_id, $key, isset( $_POST[ $field ] ) ? absint( $_POST[ $field ] ) : 0 );
			} else {
				update_post_meta( $post_id, $key, isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : '' );
			}
		}
	}
);

/**
 * Enqueue the media picker + panel styles on shade/project edit screens.
 */
add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->post_type, array( 'shade', 'project' ), true ) ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script( 'blackroll-admin', BLACKROLL_CORE_URL . 'assets/admin.js', array( 'jquery' ), BLACKROLL_CORE_VERSION, true );
		wp_enqueue_style( 'blackroll-admin', BLACKROLL_CORE_URL . 'assets/admin.css', array(), BLACKROLL_CORE_VERSION );
	}
);
