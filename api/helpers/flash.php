<?php
function flash($key, $message = null)
{
  if ($message !== null) {
    $_SESSION['flash'][$key] = $message;
    return;
  }

  if (isset($_SESSION['flash'][$key])) {
    $msg = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $msg;
  }

  return null;
}
