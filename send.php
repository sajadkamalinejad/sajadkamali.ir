<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  require 'PHPMailer/src/Exception.php';
  require 'PHPMailer/src/PHPMailer.php';
  require 'PHPMailer/src/SMTP.php';

  $phone_number;
  $Success_url ='/RequestSent.html';
  $Fail_url='/RequestFailed.html';

  if(isset($_POST['name']))
  {
    $name=$_POST['name'];
  }
  else {
    echo '<h2>Name Should not be empty.</h2>';
    echo '<script language="javascript">';
    echo 'alert("Name Should not be empty".");';
    echo 'window.history.go(-2)';
    echo '</script>';
  }

  if(isset($_POST['email']))
  {
    $email=$_POST['email'];
  }
  else
  {
    echo '<h2>Email Should not be empty.</h2>';
    echo '<script language="javascript">';
    echo 'alert("Email Should not be empty.");';
    echo 'window.history.go(-2)';
    echo '</script>';
  }


  if($_POST['phone']!="")
  {
    if(isset($_POST['phone']))
    {
      $phone=$_POST['phone'];
      if (substr($phone, 0, 1) === '+')
      {
        $temp_phone = substr($phone, 1);
        $check=ctype_digit($temp_phone);
        if($check == true)
        {
          $phone_number = strip_tags($phone);
        }
        else
        {
          echo '<h2>Please enter a valid number</h2>';
          echo '<script language="javascript">';
          echo 'alert("Please enter a valid number");';
          echo 'window.history.go(-2)';
          echo '</script>';
        }
      }
      else
      {
        echo '<h2>Number should start with a +</h2>';
        echo '<script language="javascript">';
        echo 'alert("Number should start with a +");';
        echo 'window.history.go(-2)';
        echo '</script>';
      }

    }
  }

  if(isset($_POST['message']))
  {
    $message=$_POST['message'];
  }
  else
  {
    echo '<h2>Message Should not be empty.</h2>';
    echo '<script language="javascript">';
    echo 'alert("Message Should not be empty.");';
    echo 'window.history.go(-2)';
    echo '</script>';
  }

  if(isset($_POST['g-recaptcha-response']))
  {
    $captcha=$_POST['g-recaptcha-response'];
  }
  if(!$captcha){
    echo '<h2>Captcha Verification Failure.</h2>';
    echo '<script language="javascript">';
    echo 'alert("Captcha Verification Failure.");';
    echo 'window.history.go(-2)';
    echo '</script>';
    exit;
  }

  function robot_check()
  {
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
      'secret' => '6LeI0f0UAAAAAIpCnVbEJKlXwfWpsWGNwsDCE0S3',
      'response' => $captcha
    );
    $options = array(
      'http' => array (
        'method' => 'POST',
        'content' => http_build_query($data)
      )
    );
    $context  = stream_context_create($options);
    $verify = file_get_contents($url, false, $context);
    $captcha_success=json_decode($verify);

    if ($captcha_success->success==false)
    {
      return true;
    }
    else if ($captcha_success->success==true)
    {
      echo '<h2>Security check could not verify that you are a human please go to home page and resubmit you request</h2>';
      echo '<script language="javascript">';
      echo 'alert("Security check could not verify that you are a human please go to home page and resubmit you request");';
      echo 'window.history.go(-2)';
      echo '</script>';
    }
  }

  function mail_check($mail)
  {
    $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';


    if (preg_match($pattern, $mail) === 1)
    {
      return true;
    }
    else
    {
      echo '<h2>Please enter a valid email.</h2>';
      echo '<script language="javascript">';
      echo 'alert("Please enter a valid email.");';
      echo 'window.history.go(-2)';
      echo '</script>';
    }
  }

  robot_check();
  mail_check($email);

  if(isset($phone_number))
  {
    $subject = strip_tags($name).":".strip_tags($email).":".$phone_number;
  }
  else
  {
    $subject = strip_tags($name).":".strip_tags($email);
  }
  $MailBody = strip_tags($message);


  $mail = new PHPMailer;
  $mail->isSMTP();
  $mail->SMTPDebug = 0;
  $mail->Host = "smtp.yandex.ru";
  $mail->Port = 465;
  $mail->SMTPSecure = 'ssl';
  $mail->SMTPAuth = true;
  $mail->Username = "mailsender@sajadkamali.ir";
  $mail->Password = "Hb5ZECP9rwmuxjwDZVNwiWgtt9BkKJL7mvk7c6"  ;
  $mail->setFrom("mailsender@sajadkamali.ir", "Contact Form Mail Sender");
  $mail->addAddress("Info@sajadkamali.ir", "Info Email");
  $mail->Subject = $subject;
  $mail->msgHTML($MailBody);
  $mail->AltBody = $MailBody;

  if(!$mail->send())
  {
      header( "Location: $Fail_url" );
      die();
  }
  else
  {
    header( "Location: $Success_url" );
    exit();
  }

?>
