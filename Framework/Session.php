<?php

namespace Framework;

class Session
{

  /**
   * Start the session
   * @return void
   */
  public static function start()
  {

    // Checks if session is already started
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
      // session_status() returns:
      // int(0) - if session disabled (== PHP_SESSION_DISABLED)
      // int(1) - if no session (== PHP_SESSION_NONE)
      // int(2) - if session is active (== PHP_SESSION_ACTIVE)

      // session_start() - initialize session data
    }
  }


  /**
   * Set a session key/value pair
   * 
   * @param string $key
   * @param mixed $value
   * @return void
   */
  public static function set($key, $value)
  {
    $_SESSION[$key] = $value;
    // sets key/value session  (sessions remain available until removed or until current browser is closed)
  }


  /**
   * Get a session value by the key
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public static function get($key, $default = null)
  {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
  }

  /**
   * Checks if session key exists
   * @param string $key
   * @return bool
   */
  public static function has($key)
  {
    return isset($_SESSION[$key]);
  }

  /** Clears session by a key
   *  
   * @param string $key
   * @return void
   */
  public static function clear($key)
  {
    if (isset($_SESSION[$key])) {
      // unsets given variable
      unset($_SESSION[$key]);
    }
  }

  /** 
   * Clears all session data
   * @return void
   */
  public static function clearAll()
  {
    session_unset(); // Free all session variables
    session_destroy(); // Destroys all data registered to a session
  }


  /**
   * Set a flash message
   * @param string $key
   * @param string $message
   * @return void
   */
  public static function setFlashMessage($key, $message)
  {
    self::set("flash_" . $key, $message);
  }


  /**
   * Get a flash message and unset
   * @param string $key
   * @param mixed $default
   * @return string
   */
  public static function getFlashMessage($key, $default = null)
  {
    $message = self::get("flash_" . $key, $default);
    self::clear("flash_" . $key);
    return $message;
  }
}
