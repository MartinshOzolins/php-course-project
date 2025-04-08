<?php

namespace App\Controllers; // Sets the namespace so Composer can autoload this class

class ErrorController
{
  /**
   * Shows 404 Not Found error page
   *
   * @param string $message - Custom error message (optional)
   * @return void
   */
  public static function notFound($message = "Resource not found!")
  {
    http_response_code(404); // Sets HTTP response status 

    // Renders the 'error' view and passes status/message to it (as variables)
    loadView("error", [
      "status" => "404",
      "message" => $message
    ]);
  }

  /**
   * Shows 403 Unauthorized error page
   *
   * @param string $message - Custom error message (optional)
   * @return void
   */
  public static function unauthorized($message = "You are not authorized to view this resource")
  {
    http_response_code(403); // Sets HTTP response status to 403 (not 404)

    // Renders the 'error' view and passes status/message to it (as variables)
    loadView("error", [
      "status" => "403",
      "message" => $message
    ]);
  }
}
