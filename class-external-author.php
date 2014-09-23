<?php

class External_Author {
	public function __construct() {
		add_action( 'add_meta_boxes_post', array( $this, 'add_custom_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	public function add_custom_meta_box() {
		add_meta_box(
			'external-author-meta-box',
			__( 'External Author' ),
			array( $this, 'render_meta_box' ),
			'post',
			'side'
		);
	}

	public function render_meta_box( $post ) {
		wp_register_style( 'external_author_admin', plugins_url( '/css/external-author-admin.css', __FILE__ ) );
		wp_enqueue_style( 'external_author_admin' );

		wp_register_script( 'external_author', plugins_url( '/js/external-author.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'external_author' );

		wp_nonce_field( 'external_author_meta_box', 'external_author_meta_box_nonce' );

		$authors = get_post_meta( $post->ID, '_external_authors', true );

		echo '<div class="external-author-input">';
		foreach ( $authors as $index => $author ) {
			echo '<div class="external-author">';

			echo '<div class="external-author-name"><label for="external-authors[' . $index . '][name]">';
			_e( 'Full name' );
			echo '</label>';
			echo '<input class="text" type="text" name="external-authors[' . $index . '][name]" value="'
			     . esc_attr( $author['name'] ) . '" size="25" />';
			echo '</div>';

			echo '<div class="external-author-dci"><label for="external-authors[' . $index . '][dci]">';
			_e( 'DCI Number ' );
			echo '</label>';
			echo '<input class="text" type="number" min="0" name="external-authors[' . $index . '][dci]" value="' .
			     esc_attr( $author['dci'] ) . '" size="25" />';
			echo '</div>';

			echo '</div>';
		}
		echo '</div>';

		echo '<input id="external-author-add-author" class="button" type="button" value="Add additional author">';
	}

	public function save( $post_id ) {
		if ( ! isset( $_POST['external_author_meta_box_nonce'] ) ) {
			return $post_id;
		}

		if ( ! wp_verify_nonce( $_POST['external_author_meta_box_nonce'], 'external_author_meta_box' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( ! isset( $_POST['external-authors'] ) ) {
			return $post_id;
		}
		$authors = $_POST['external-authors'];
		if ( ! is_array( $authors ) ) {
			$authors[0] = $authors;
		}

		foreach ( $authors as $index => $author ) {
			foreach ( $author as $key => $value ) {
				$author[ $key ] = sanitize_text_field( $author[ $key ] );
			}
			if ( empty( $author['name'] ) ) {
				unset( $authors[ $index ] );
			}
		}

		update_post_meta( $post_id, '_external_authors', $authors );
	}
}