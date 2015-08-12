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
		$featured = get_post_meta( $post->ID, '_external_authors_featured', true );
		$no_author = get_post_meta( $post->ID, '_external_authors_no_author', true );

		if ( empty( $authors ) ) {
			$authors[] = array( 'name' => '', 'dci' => '' );
		}

		echo '<div class="external-author-input">';
		foreach ( $authors as $index => $author ) {
			echo '<div class="external-author">';

			$id_attr = 'external-authors[' . $index . '][name]';
			echo '<div class="external-author-name"><label for="' . $id_attr . '">';
			_e( 'Full name' );
			echo '</label>';

			// author name and remove author button
			echo '<input class="text maybe-disable" type="text" name="' . $id_attr . '" id="' . $id_attr . '" value="' . esc_attr( $author['name'] ) . '" size="25" />';
			echo '<input type="button" class="button remove-author maybe-disable" value="X"/>';
			echo '</div>';

			// author DCI number
			$id_attr = 'external-authors[' . $index . '][dci]';
			echo '<div class="external-author-dci"><label for="' . $id_attr . '">';
			_e( 'DCI Number' );
			echo '</label>';
			echo '<input class="text maybe-disable" type="number" min="0" name="' . $id_attr . '" id="' . $id_attr . '" value="' . esc_attr( $author['dci'] ) . '" size="25" />';
			echo '</div>';

			// use author image as featured image
			echo '<div class="external-authors-featured">';
			$id_attr = 'external-authors[' . $index . '][featured]';
			$checked = '';
			if ( $featured != null && $featured == $index ) {
				$checked = ' checked="checked"';
			}
			echo '<input class="featured maybe-disable" type="checkbox" name="external-authors-featured" id="' . $id_attr . '" value="' . esc_attr( $index ) . '"' . $checked . ' />';
			echo '<label for="' . $id_attr . '">';
			_e( 'Featured Author' );
			echo '</label>';
			echo '</div>';

			echo '</div>';
		}
		echo '</div>';

		echo '<input id="external-author-add-author" class="button maybe-disable" type="button" value="' . __( 'Add additional author' ) . '">';

		printf( '<div class="no-author-field"><input type="checkbox" id="no-author" name="external-author-no-author" value="true" %1$s >',
			checked( $no_author, true, false ) );
		printf( '<label for="no-author">%1$s</label></div>', __( 'Display no author' ) );
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

		if ( isset( $_POST['external-author-no-author'] ) ) {
			update_post_meta( $post_id, '_external_authors_no_author', $_POST['external-author-no-author'] === 'true' ? true : false );
		} else if ( isset( $_POST['external-authors'] ) ) {
			update_post_meta( $post_id, '_external_authors_no_author', false );
			$authors = $_POST['external-authors'];
			$featured = null;
			if ( isset( $_POST['external-authors-featured'] ) ) {
				$featured = intval( $_POST['external-authors-featured'] );
			}
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
			update_post_meta( $post_id, '_external_authors_featured', $featured );
		}

		return $post_id;
	}
}