<?php
	$page_action = tep_fill_variable('page_action', 'get');
	
	$contact_name = tep_fill_variable('contact_name', 'post', '');
	$contact_company = tep_fill_variable('contact_company', 'post', '');
	$contact_email = tep_fill_variable('contact_emal', 'post', '');
	$contact_phone = tep_fill_variable('contact_phone', 'post', '');
	$contact_message = tep_fill_variable('contact_message', 'post', '');
	$contact_subject = tep_fill_variable('contact_subject', 'post', '');
	
  if ($page_action == 'submit') {

    if (empty($contact_name) && empty($contact_company)) {
      $error->add_error('contact_us', 'Please enter either your name or your company name.');
    }
    if (empty($contact_email) && empty($contact_phone)) {
      $error->add_error('contact_us', 'Please enter either your email address or your phone number.');
    }
    if (!empty($contact_email) && strpos($contact_email, '@') === FALSE)
      $error->add_error('contact_us', 'Please enter a valid email address.');

    if (empty($contact_message)) {
      $error->add_error('contact_us', 'Please enter your message.');
    }

    if (!$error->get_error_status('contact_us')) {
      $from_name = '';
      if (!empty($contact_name))
        $from_name = $contact_name;
      if (!empty($contact_company))
        $from_name .= (!empty($from_name)?' - ':'').$contact_company;

      if (SEND_EMAILS != 'true') return false;

      // Instantiate a new mail object
      $message = new email(array(MAILER_NAME));

      // Build the text version
      $message->add_text($contact_message."\n\n".$contact_phone);
      if (!empty($contact_email)) {
        $from_email = $contact_email;
      } else {
        $from_email = '';
      }
      // Send message
      $subject = 'From Website: '. (empty($contact_subject) ? '(no subject)' : $contact_subject);

      $message->build_message();

      $message->send('', INFO_EMAIL, $from_name, $from_email, $subject);


      $error->add_error('contact_us', 'Please enter your name.', 'success');
      tep_redirect(PAGE_URL.'?page_action=success');

    } else {
      $page_action = '';
	 // echo $twig->render('public/contact_us.html.twig', array('user' => $user, 'page' => $page, 'page_action'=>$page_action));
    }
	//echo $twig->render('public/contact_us.html.twig', array('user' => $user, 'page' => $page));
  }
  else {
	 // echo $twig->render('public/contact_us.html.twig', array('user' => $user, 'page' => $page));
  }
//echo var_dump($error);
	echo $twig->render('public/contact_us.html.twig', array('user' => $user, 'error'=>$error, 'page' => $page, 'page_action'=>$page_action));
?>

