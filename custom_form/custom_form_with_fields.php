<?php
/*
Plugin Name: custom form with fields
Description: custom form which collect user date from fields and create contact at https://hubspot.com/
Version: 0.1
Author: yury@gmail.com
 */
?>
<?php
require_once 'classes/LogFile.php';
function custom_form_with_fields_add_admin_pages() {
	add_options_page( 'custom form with fields Options', 'custom form with fields',
		'manage_options', 'custom_form_with_fields_options_page',
		'custom_form_with_fields_options_page' );
}
function shortcode() {
	add_options_page( 'custom form with fields Options', 'custom form with fields shortcode',
		'manage_options', 'your_shortcode',
		'your_shortcode' );
}


wp_register_style( 'custom_form_with_fields',
	'/wp-content/plugins/custom_form_with_fields/assets/css/style.css' );
wp_enqueue_style( 'custom_form_with_fields' );

function custom_form_with_fields_options_page() {

	echo '<h1>Custom form with fields options page<h1>';
	if ( ! isset( $_POST['message'] ) ) {
		?>
		<div>
			<form name="custom_form" method="POST" enctype="multipart/form-data">
				<fieldset>
					<label> First Name:
						<br>
						<input type="text" placeholder="First Name" name="first_name" required>
					</label>
				</fieldset>

				<fieldset>
					<label> Last Name:
						<br>
						<input type="text" placeholder="Last Name" name="last_name" required>
					</label>
				</fieldset>
				<br>
				<fieldset>
					<label> Subject:
						<br>
						<input type="text" placeholder="Subject" name="subject" required>
					</label>
				</fieldset>
				<br>
				<fieldset>
					<label>Message:
						<br>
						<br>
						<textarea type="text" name="message" cols="50" rows="16" required>
				</textarea>
					</label>
				</fieldset>
				<br>
				<p> E-mail: </p>
				<input type="email" placeholder="E-mail" pattern="^.+@.+\..+$" name="email">

				<hr>
				<button type="submit" name="custom_form_send" value="send">send</button>
			</form>
		</div><?php

	} else {
		$subject           = htmlspecialchars( $_POST['subject'] );
		$message           = htmlspecialchars( $_POST['message'] );
		$firstname         = htmlspecialchars( $_POST['first_name'] );
		$lastname          = htmlspecialchars( $_POST['last_name'] );
		$email             = htmlspecialchars( $_POST['email'] );
		$formatted_message = nl2br( $message );
		$firstname         = urldecode( $firstname );
		$lastname          = urldecode( $lastname );
		$email             = urldecode( $email );
		$subject           = urldecode( $subject );
		$message           = urldecode( $message );
		$message           = trim( $message );

		$arr = [
			'properties' => [
				[
					'property' => 'email',
					'value'    => $email,
				],
				[
					'property' => 'firstname',
					'value'    => $firstname,
				],
				[
					'property' => 'lastname',
					'value'    => $lastname,
				],
			],
		];

		$post_json = json_encode( $arr );
		$hapikey   = 'f23b2a6e-ab7b-495b-af36-e043f5b3897b';
		$endpoint  = 'https://api.hubapi.com/contacts/v1/contact?hapikey=' . $hapikey;
		$ch        = @curl_init();
		@curl_setopt( $ch, CURLOPT_POST, true );
		@curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_json );
		@curl_setopt( $ch, CURLOPT_URL, $endpoint );
		@curl_setopt( $ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' ] );
		@curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$response    = @curl_exec( $ch );
		$status_code = @curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		$curl_errors = curl_error( $ch );
		@curl_close( $ch );
		$Api_log = new Log_file( __DIR__ . '/logs/apilog.log' );
		$api_log =
			"curl Errors: " . $curl_errors . "\nStatus code: " . $status_code . "\nResponse: " .
			$response;
		$Api_log->write_to_file( $api_log );


		if ( wp_mail( "yury80@gmail.com", 'subject: ' . "$subject", 'message: ' . "$message" ) ) {
			echo "<p>Message sent <br> Your message is: " . " $formatted_message" . "</p>";
			$Log = new Log_file( __DIR__ . '/logs/log.log' );
			$log = 'author:  ' . $_POST['email'] . "\n" . 'at ' . date( 'Y-m-d H:i:s' ) . "\n" .
			       ' wrote: ' . "$message" . "\n" . "\n";
			$Log->write_to_file( $log );

		} else {
			echo '<p>Message not sent</p>';
		}
	}
}
function your_shortcode(){
	echo '<h1>Your code is</h1> <h2>[custom_form_shortcode]</h2>';
}
add_action( 'admin_menu', 'custom_form_with_fields_add_admin_pages' );
add_action( 'admin_menu', 'shortcode' );
add_shortcode('custom_form_shortcode', 'custom_form_with_fields_options_page');