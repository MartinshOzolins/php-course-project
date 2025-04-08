<?php

namespace App\Controllers; // Sets namespace so Composer can autoload this class correctly

use Framework\Database; // Imports Database class from the Framework namespace

class HomeController
{
  protected $db;

  public function __construct()
  {
    // Loads DB config array from file
    $config = require basePath('config/db.php');
    // Creates a new Database instance using the config
    $this->db = new Database($config);
  }

  // Handles request to homepage
  public function index()
  {
    // Fetches latest 6 listings from DB
    $listings = $this->db->query("SELECT * FROM listings ORDER BY created_at DESC LIMIT 6")->fetchAll();

    // Loads 'home' view and passes listings to it as a variable 
    loadView("home", [
      "listings" => $listings
    ]);
  }
}
