<?php

namespace Framework\Middleware;

use Framework\Session;


class Authorize
{


  /**
   * Checks if user is authenticated
   * @return bool
   */
  public function isAuthenticated()
  {
    return Session::has("user");
  }

  /**
   * Handles the user's request
   * @param string $role
   * @return bool
   */
  public function handle($role)
  {
    // checks if route is for guests, and if is authenticated redirect (used to not allow access to login if already logged)
    if ($role === "guest" && $this->isAuthenticated()) {
      return redirect("/");

      // checks if route is for authenticated users, and if not logged, redirects to login page
    } elseif ($role === "auth" && !$this->isAuthenticated()) {
      return redirect("/auth/login");
    }
  }
}
