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

wp_register_style( 'custom_form_with_fields',
	'/wp-content/plugins/custom_form_with_fields/assets/css/style.css' );
wp_enqueue_style( 'custom_form_with_fields' );

function custom_form_with_fields_scripts() {
	wp_enqueue_script( 'hubspot', '//js.hs-scripts.com/7805462.js',  '' , '', true );
}
add_action( 'admin_enqueue_scripts', 'custom_form_with_fields_scripts' );

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
		$formatted_message = nl2br( $message );
		$subject           = urldecode( $subject );
		$message           = urldecode( $message );
		$message           = trim( $message );

		if ( wp_mail( "yury80@gmail.com", 'subject: '. "$subject", 'message: '. "$message" ) ) {
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
add_action( 'admin_menu', 'custom_form_with_fields_add_admin_pages' );


