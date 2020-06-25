<?php
	include 'db.php';
	require 'mail/PHPMailerAutoload.php';


	$SENDER_EMAIL = 'example@gmail.com';
	$SENDER_PASSWORDWORD = 'example';

	// Getting date for db query
	$date = date('Y-m-d');
	$today = date('Y-m-d', strtotime($date .' +1 day'));
	$tommorow = date('Y-m-d', strtotime($date .' +24 hour'));

	$sql = 'SELECT * FROM wp_bookings_calendar WHERE date_start BETWEEN "' . $today . '" AND "'. $tommorow.'"' ;
	$each_booking= mysqli_query($con,$sql);

	if(mysqli_num_rows($each_booking) > 0){
		// Main loop for mail sending
		while($booking = mysqli_fetch_assoc($each_booking)){

			$current_user = 'SELECT * FROM wp_users WHERE ID = ' . $booking['bookings_author'];
			$user_email = mysqli_fetch_assoc(mysqli_query($con, $current_user))['user_email'];

			// SMTP Settings
			$mail = new PHPMailer;

			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = 'tls';

			$mail->Username = $SENDER_EMAIL;
			$mail->Password = $SENDER_PASSWORD;

			$mail->setFrom($SENDER_EMAIL, 'John Doe');
			$mail->addAddress($user_email);
			$mail->addReplyTo($SENDER_EMAIL);

			// Message Body

			$mail->isHTML(true);
			$mail->Subject = 'Sample Subject';
			$mail->Body = '<h1 align=center color=red> Test Title </h1><br>';

			if (!$mail->send()) {
					echo 'Error';
			}else{
					echo 'Success';
			}
		}
	}else{
		echo "0 Results";
	} 

?>