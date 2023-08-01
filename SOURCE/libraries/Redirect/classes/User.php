<?php
/**
 * Handles user session.
 */
class User
{
  function __construct()
  {
    session_start();
  }

  public function check_user_session()
  {
    if(!$_SESSION['user']) {
      header('Location: /login/');
    }
  }
}
 ?>
