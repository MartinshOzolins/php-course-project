<?php


namespace Framework;

use Framework\Session;


class Authorization
{
  /**
   * Checks if current logged in user own a resource 
   * @param int $resourceId
   * @return bool
   */
  public static function isOwner($resourceId)
  {
    $sessionUser = Session::get("user");


    if ($sessionUser !== null && isset($sessionUser["id"])) {
      $sessionUserId = (int) $sessionUser["id"];
      return $sessionUserId === $resourceId;
    }

    return false;
  }
}
