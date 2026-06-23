<?php
/**
 * Request-for-quote workflow.
 *
 * @package KechooCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Kechoo_RFQ {
	private static $errors;
	private static $success_reference = '';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_shortcode( 'kechoo_rfq_form', array( __CLASS__, 'render_shortcode' ) );
		add_filter( 'manage_kechoo_rfq_posts_columns', array( __CLASS__, 'admin_columns' ) );
		add_action( 'manage_kechoo_rfq_posts_custom_column', array( __CLASS__, 'admin_column_content' ), 10, 2 );
	}

	public static function register_post_type() {
		register_post_type(
			'kechoo_rfq',
			array(
				'labels' => array(
					'name'          => __( 'RFQ requests', 'kechoo-core' ),
					'singular_name' => __( 'RFQ request', 'kechoo-core' ),
					'menu_name'     => __( 'KECHOO RFQs', 'kechoo-core' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_icon'           => 'dashicons-media-spreadsheet',
				'supports'            => array( 'title' ),
				'capability_type'     => 'post',
				'map_meta_cap'        => true,
				'exclude_from_search' => true,
				'show_in_rest'        => false,
			)
		);
	}

	private static function value( $key, $default = '' ) {
		if ( isset( $_POST[ $key ] ) ) {
			return sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
		}

		if ( isset( $_GET[ $key ] ) ) {
			return sanitize_text_field( wp_unslash( $_GET[ $key ] ) );
		}

		return $default;
	}

	private static function error( $key ) {
		if ( ! self::$errors instanceof WP_Error ) {
			return '';
		}

		$data = self::$errors->get_error_data( $key );
		return is_string( $data ) ? $data : '';
	}

	public static function render_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'buyer_type' => '',
			),
			$atts,
			'kechoo_rfq_form'
		);

		if ( isset( $_POST['kechoo_rfq_action'] ) && 'submit' === sanitize_text_field( wp_unslash( $_POST['kechoo_rfq_action'] ) ) ) {
			self::handle_submission();
		}

		ob_start();
		if ( self::$success_reference ) {
			?>
			<div class="kechoo-form-message kechoo-form-message--success" role="status" tabindex="-1" data-kechoo-form-message>
				<strong><?php esc_html_e( 'Your request is with the KECHOO team.', 'kechoo-core' ); ?></strong>
				<p><?php echo esc_html( sprintf( __( 'Reference: %s. We will review the application and reply using the email you provided.', 'kechoo-core' ), self::$success_reference ) ); ?></p>
			</div>
			<?php
			return ob_get_clean();
		}

		if ( self::$errors instanceof WP_Error && self::$errors->has_errors() ) {
			?>
			<div class="kechoo-form-message kechoo-form-message--error" role="alert" tabindex="-1" data-kechoo-form-message>
				<strong><?php esc_html_e( 'We could not send the request yet.', 'kechoo-core' ); ?></strong>
				<p><?php esc_html_e( 'Review the fields marked below. Your information has been kept.', 'kechoo-core' ); ?></p>
			</div>
			<?php
		}

		$buyer_type = self::value( 'buyer_type', $atts['buyer_type'] );
		$product    = self::value( 'product' );
		?>
		<form class="kechoo-rfq-form" method="post" enctype="multipart/form-data" novalidate data-kechoo-prevent-double-submit>
			<?php wp_nonce_field( 'kechoo_submit_rfq', 'kechoo_rfq_nonce' ); ?>
			<input type="hidden" name="kechoo_rfq_action" value="submit">
			<input type="text" name="website" value="" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;inset-inline-start:-10000px">

			<fieldset>
				<legend><?php esc_html_e( 'Contact and company', 'kechoo-core' ); ?></legend>
				<div class="kechoo-rfq-form__grid">
					<?php self::text_field( 'contact_name', __( 'Your name', 'kechoo-core' ), true, 'text', 'name' ); ?>
					<?php self::text_field( 'email', __( 'Business email', 'kechoo-core' ), true, 'email', 'email' ); ?>
					<?php self::text_field( 'company', __( 'Company', 'kechoo-core' ), true, 'text', 'organization' ); ?>
					<?php self::text_field( 'country', __( 'Country / region', 'kechoo-core' ), true, 'text', 'country-name' ); ?>
					<div class="kechoo-field">
						<label for="kechoo-buyer-type"><?php esc_html_e( 'Buyer type', 'kechoo-core' ); ?> <span class="kechoo-required" aria-hidden="true">*</span></label>
						<select id="kechoo-buyer-type" name="buyer_type" required aria-describedby="<?php echo self::error( 'buyer_type' ) ? 'kechoo-buyer-type-error' : ''; ?>">
							<option value=""><?php esc_html_e( 'Select buyer type', 'kechoo-core' ); ?></option>
							<option value="end-user" <?php selected( $buyer_type, 'end-user' ); ?>><?php esc_html_e( 'End user / factory', 'kechoo-core' ); ?></option>
							<option value="distributor" <?php selected( $buyer_type, 'distributor' ); ?>><?php esc_html_e( 'Distributor / reseller', 'kechoo-core' ); ?></option>
							<option value="oem" <?php selected( $buyer_type, 'oem' ); ?>><?php esc_html_e( 'OEM / private label', 'kechoo-core' ); ?></option>
						</select>
						<?php self::field_error( 'buyer_type', 'kechoo-buyer-type-error' ); ?>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend><?php esc_html_e( 'Cutting requirement', 'kechoo-core' ); ?></legend>
				<div class="kechoo-rfq-form__grid">
					<?php self::text_field( 'product', __( 'Product or blade family', 'kechoo-core' ), false, 'text', '', $product ); ?>
					<?php self::text_field( 'application', __( 'What are you cutting?', 'kechoo-core' ), true ); ?>
					<?php self::text_field( 'material', __( 'Workpiece material', 'kechoo-core' ), true ); ?>
					<?php self::text_field( 'machine', __( 'Machine brand and model', 'kechoo-core' ), false ); ?>
					<?php self::text_field( 'dimensions', __( 'Blade dimensions', 'kechoo-core' ), false ); ?>
					<?php self::text_field( 'quantity', __( 'Estimated quantity', 'kechoo-core' ), true ); ?>
					<div class="kechoo-field kechoo-field--wide">
						<label for="kechoo-message"><?php esc_html_e( 'Cutting goal and other details', 'kechoo-core' ); ?></label>
						<textarea id="kechoo-message" name="message" rows="6" maxlength="3000" aria-describedby="kechoo-message-hint"><?php echo esc_textarea( isset( $_POST['message'] ) ? wp_unslash( $_POST['message'] ) : '' ); ?></textarea>
						<p class="kechoo-rfq-form__hint" id="kechoo-message-hint"><?php esc_html_e( 'Include workpiece size, current blade, desired finish, blade life, cutting speed, or problems to solve.', 'kechoo-core' ); ?></p>
					</div>
					<div class="kechoo-field kechoo-field--wide">
						<label for="kechoo-drawing"><?php esc_html_e( 'Drawing, blade label, or product photo', 'kechoo-core' ); ?></label>
						<input id="kechoo-drawing" type="file" name="drawing" accept=".pdf,.jpg,.jpeg,.png,.webp" aria-describedby="kechoo-drawing-hint kechoo-drawing-error">
						<p class="kechoo-rfq-form__hint" id="kechoo-drawing-hint"><?php esc_html_e( 'PDF, JPG, PNG, or WebP. Maximum 5 MB.', 'kechoo-core' ); ?></p>
						<?php self::field_error( 'drawing', 'kechoo-drawing-error' ); ?>
					</div>
				</div>
			</fieldset>

			<div class="kechoo-field kechoo-field--consent">
				<label><input type="checkbox" name="consent" value="1" <?php checked( isset( $_POST['consent'] ) ); ?> required> <?php esc_html_e( 'I agree that KECHOO may use this information to answer my request.', 'kechoo-core' ); ?> <span class="kechoo-required" aria-hidden="true">*</span></label>
				<?php self::field_error( 'consent', 'kechoo-consent-error' ); ?>
			</div>

			<button class="kechoo-button" type="submit" data-loading-label="<?php esc_attr_e( 'Sending request…', 'kechoo-core' ); ?>"><?php esc_html_e( 'Send Quote Request', 'kechoo-core' ); ?></button>
		</form>
		<?php
		return ob_get_clean();
	}

	private static function text_field( $key, $label, $required = false, $type = 'text', $autocomplete = '', $default = '' ) {
		$value    = self::value( $key, $default );
		$error    = self::error( $key );
		$error_id = 'kechoo-' . str_replace( '_', '-', $key ) . '-error';
		?>
		<div class="kechoo-field">
			<label for="kechoo-<?php echo esc_attr( str_replace( '_', '-', $key ) ); ?>">
				<?php echo esc_html( $label ); ?><?php if ( $required ) : ?> <span class="kechoo-required" aria-hidden="true">*</span><?php endif; ?>
			</label>
			<input id="kechoo-<?php echo esc_attr( str_replace( '_', '-', $key ) ); ?>" type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>" maxlength="200" <?php echo $autocomplete ? 'autocomplete="' . esc_attr( $autocomplete ) . '"' : ''; ?> <?php echo $required ? 'required' : ''; ?> <?php echo $error ? 'aria-invalid="true" aria-describedby="' . esc_attr( $error_id ) . '"' : ''; ?>>
			<?php self::field_error( $key, $error_id ); ?>
		</div>
		<?php
	}

	private static function field_error( $key, $id ) {
		$error = self::error( $key );
		if ( $error ) {
			printf( '<span class="kechoo-field-error" id="%1$s">%2$s</span>', esc_attr( $id ), esc_html( $error ) );
		}
	}

	private static function handle_submission() {
		self::$errors = new WP_Error();

		if ( ! isset( $_POST['kechoo_rfq_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kechoo_rfq_nonce'] ) ), 'kechoo_submit_rfq' ) ) {
			self::$errors->add( 'security', __( 'The form session expired. Reload the page and send the request again.', 'kechoo-core' ) );
			return;
		}

		if ( ! empty( $_POST['website'] ) ) {
			self::$errors->add( 'spam', __( 'We could not accept this request.', 'kechoo-core' ) );
			return;
		}

		$rate_key = 'kechoo_rfq_' . md5( self::request_ip() );
		if ( get_transient( $rate_key ) ) {
			self::$errors->add( 'rate', __( 'A request was sent recently. Wait five minutes before sending another.', 'kechoo-core' ) );
			return;
		}

		$required = array(
			'contact_name' => __( 'Please enter your name.', 'kechoo-core' ),
			'email'        => __( 'Please enter your business email.', 'kechoo-core' ),
			'company'      => __( 'Please enter your company.', 'kechoo-core' ),
			'country'      => __( 'Please enter your country or region.', 'kechoo-core' ),
			'buyer_type'   => __( 'Please select a buyer type.', 'kechoo-core' ),
			'application'  => __( 'Please tell us what you are cutting.', 'kechoo-core' ),
			'material'     => __( 'Please enter the workpiece material.', 'kechoo-core' ),
			'quantity'     => __( 'Please enter an estimated quantity.', 'kechoo-core' ),
			'consent'      => __( 'Please agree so we can answer your request.', 'kechoo-core' ),
		);

		foreach ( $required as $key => $message ) {
			if ( empty( $_POST[ $key ] ) ) {
				self::$errors->add( $key, $key, $message );
			}
		}

		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		if ( $email && ! is_email( $email ) ) {
			self::$errors->add( 'email', 'email', __( 'Email needs a valid address such as name@company.com.', 'kechoo-core' ) );
		}

		if ( ! empty( $_FILES['drawing']['name'] ) && (int) $_FILES['drawing']['size'] > 5 * MB_IN_BYTES ) {
			self::$errors->add( 'drawing', 'drawing', __( 'The file is larger than 5 MB. Choose a smaller PDF or image.', 'kechoo-core' ) );
		}

		if ( self::$errors->has_errors() ) {
			return;
		}

		$contact_name = sanitize_text_field( wp_unslash( $_POST['contact_name'] ) );
		$company      = sanitize_text_field( wp_unslash( $_POST['company'] ) );
		$post_id      = wp_insert_post(
			array(
				'post_type'   => 'kechoo_rfq',
				'post_status' => 'private',
				'post_title'  => sprintf( 'RFQ — %s — %s', $company ?: $contact_name, current_time( 'Y-m-d H:i' ) ),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			self::$errors->add( 'server', __( 'We could not save the request. Email the KECHOO team or try again later.', 'kechoo-core' ) );
			return;
		}

		$fields = array( 'contact_name', 'email', 'company', 'country', 'buyer_type', 'product', 'application', 'material', 'machine', 'dimensions', 'quantity' );
		foreach ( $fields as $field ) {
			$value = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : '';
			update_post_meta( $post_id, '_kechoo_rfq_' . $field, $value );
		}
		update_post_meta( $post_id, '_kechoo_rfq_message', isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '' );

		if ( ! empty( $_FILES['drawing']['name'] ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';

			$attachment_id = media_handle_upload(
				'drawing',
				$post_id,
				array(),
				array(
					'test_form' => false,
					'mimes'     => array(
						'pdf'      => 'application/pdf',
						'jpg|jpeg' => 'image/jpeg',
						'png'      => 'image/png',
						'webp'     => 'image/webp',
					),
				)
			);

			if ( is_wp_error( $attachment_id ) ) {
				wp_delete_post( $post_id, true );
				self::$errors->add( 'drawing', 'drawing', __( 'We could not upload this file. Use a PDF, JPG, PNG, or WebP under 5 MB.', 'kechoo-core' ) );
				return;
			}
			update_post_meta( $post_id, '_kechoo_rfq_attachment_id', $attachment_id );
		}

		$reference = 'KQ-' . str_pad( (string) $post_id, 6, '0', STR_PAD_LEFT );
		update_post_meta( $post_id, '_kechoo_rfq_reference', $reference );
		self::send_notification( $post_id, $reference );
		set_transient( $rate_key, 1, 5 * MINUTE_IN_SECONDS );
		self::$success_reference = $reference;
		$_POST = array();
	}

	private static function send_notification( $post_id, $reference ) {
		$meta = get_post_meta( $post_id );
		$labels = array(
			'contact_name' => 'Name',
			'email'        => 'Email',
			'company'      => 'Company',
			'country'      => 'Country / region',
			'buyer_type'   => 'Buyer type',
			'product'      => 'Product',
			'application'  => 'Application',
			'material'     => 'Material',
			'machine'      => 'Machine',
			'dimensions'   => 'Dimensions',
			'quantity'     => 'Quantity',
		);

		$lines = array( 'New KECHOO RFQ: ' . $reference, '' );
		foreach ( $labels as $key => $label ) {
			$value = isset( $meta[ '_kechoo_rfq_' . $key ][0] ) ? $meta[ '_kechoo_rfq_' . $key ][0] : '';
			$lines[] = $label . ': ' . $value;
		}
		$lines[] = '';
		$lines[] = 'Message: ' . ( isset( $meta['_kechoo_rfq_message'][0] ) ? $meta['_kechoo_rfq_message'][0] : '' );
		$lines[] = '';
		$lines[] = 'Review in WordPress: ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' );

		wp_mail(
			get_option( 'admin_email' ),
			sprintf( '[%s] New KECHOO quote request from %s', $reference, isset( $meta['_kechoo_rfq_company'][0] ) ? $meta['_kechoo_rfq_company'][0] : 'website visitor' ),
			implode( "\n", $lines )
		);
	}

	private static function request_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
		return wp_salt( 'nonce' ) . '|' . $ip;
	}

	public static function admin_columns( $columns ) {
		return array(
			'cb'        => $columns['cb'],
			'title'     => __( 'Request', 'kechoo-core' ),
			'reference' => __( 'Reference', 'kechoo-core' ),
			'company'   => __( 'Company', 'kechoo-core' ),
			'email'     => __( 'Email', 'kechoo-core' ),
			'buyer'     => __( 'Buyer type', 'kechoo-core' ),
			'date'      => $columns['date'],
		);
	}

	public static function admin_column_content( $column, $post_id ) {
		$map = array(
			'reference' => '_kechoo_rfq_reference',
			'company'   => '_kechoo_rfq_company',
			'email'     => '_kechoo_rfq_email',
			'buyer'     => '_kechoo_rfq_buyer_type',
		);

		if ( isset( $map[ $column ] ) ) {
			echo esc_html( get_post_meta( $post_id, $map[ $column ], true ) );
		}
	}
}

