<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  echo "A password reset link has been sent to $email.";
}
?>