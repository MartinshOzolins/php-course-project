<?php

namespace App\Controllers; // Sets the namespace so Composer can autoload this class

use Framework\Database; // Imports Database class from Framework namespace
use App\Controllers\ErrorController; // Imports ErrorController for handling missing listings
use Framework\Session; // Imports Session class from Framework namespace for handling Sessions (checks if session exists, allows to add a session key/value pairs)
use Framework\Validation; // Imports Validation class from Framework namespace for validating form inputs
use Framework\Authorization; // Imports Authorization class for checking if user own a resource (for deleting, editing) 

class ListingController
{
  protected $db;

  public function __construct()
  {
    // Loads DB config array from file
    $config = require basePath('config/db.php');

    // Creates a new Database instance using the config
    $this->db = new Database($config);
  }


  // 1. Instance method that is called to match root route "/".
  /**
   * Show the latest listings
   *
   * @return void
   */
  public function index()
  {
    // Fetches 6 most recent listings from DB
    $listings = $this->db->query("SELECT * FROM listings ORDER BY created_at DESC")->fetchAll();

    // Loads the 'listings/index' view and passes the listings data
    loadView("listings/index", [
      "listings" => $listings
    ]);
  }


  // 2. Instance method that is called to match "listings/create" route and showcase create form
  /**
   * Show the create listing form
   *
   * @return void
   */
  public function create()
  {
    // Loads the form view for creating a new listing
    loadView("listings/create");
  }


  // 3. Instance method that is called to match "listing/id" route
  /**
   * Show a single listing based on ID
   *  
   * @param array $params - URL parameters (e.g., ['id' => 5])
   * @return void
   */
  public function show($params)
  {
    // Extracts 'id' from passed URL parameters
    $id = $params["id"];

    // Prepares an array for binding query parameters
    $params = ["id" => $id];

    // Fetches a listing with the given ID
    $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();

    // If no listing found, show 404 error page
    if (!$listing) {
      ErrorController::notFound("Listing not found.");
      return;
    }

    // Loads the 'listings/show' view and passes the listing data (as variable)
    loadView("listings/show", ["listing" => $listing]);
  }


  // 4.
  /**
   * Store data in database
   * @return void
   */
  public function store()
  {
    $allowedFields = ["title", "description", "salary", "tags", "company", "address", "city", "state", "phone", "email", "requirements", "benefits"];

    // checks submitted fields and leaves only our fields
    // array_intersect_key() - returns key only if it in both arrays
    // array_flip() - flips keys to values and in opposite
    $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

    // retrieves current user ID
    $newListingData["user_id"] = Session::get("user")["id"];


    // Sanitizes values using our helper function
    $newListingData = array_map('sanitize', $newListingData);


    // Strictly required fields are submitted
    $requiredFields = ["title", "description", "email", "city", "state", "salary"];
    // Checks if required fields are valid, if not adds error message to field
    $errors = [];
    foreach ($requiredFields as $field) {
      if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
        $errors[$field] = ucfirst($field) . " is required";
      }
    }

    if (!empty($errors)) {
      //Reloads view with errors and already submitted values
      loadView("listings/create", ["errors" => $errors, "listing" => $newListingData]);
    } else {
      // Submits data

      // Prepares column names => title, description, salary (only available inputs are left)
      $fields = [];
      foreach ($newListingData as $field => $value) {
        $fields[] = $field;
      }
      // Turns fields/keys into strings
      $fields = implode(", ", $fields);


      // Prepares value placeholders => :title, :description...
      $values = [];
      foreach ($newListingData as $field => $value) {
        // Converts empty strings to null
        if ($value === "") {
          $newListingData[$field] = null;
        }
        $values[] = ":" . $field;
      }
      $values = implode(", ", $values);

      inspect($fields);
      inspect($values);

      // Inserts into listings table
      $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";
      // Calls Database class instance method query() that takes query and values as an associative array
      $this->db->query($query, $newListingData);



      // Sets flash success messages using Session class
      Session::setFlashMessage("success_message",  "Listings created successfully");

      // Redirects
      redirect("/listings");
    }
  }


  // 5.
  /**
   * Store data in database
   * @param array $params
   * @return void
   */
  public function destroy($params)
  {
    $id = $params["id"];

    $params = [
      "id" => $id
    ];

    // Checks if listing exists
    $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();

    if (!$listing) {
      ErrorController::notFound("Listing not found!");
      return;
    }

    // Authorization - checks if user own this resource
    if (!Authorization::isOwner($listing->user_id)) {
      // sets errors messages using Session class
      Session::setFlashMessage("error_message", "You are not authorized to delete this listing");
      return  redirect("/listings/{$listing->id}");
    }

    // Deletes
    $this->db->query("DELETE FROM listings WHERE id= :id", $params);



    // sets flash success messages using Session class
    Session::setFlashMessage("success_message",  "Listings deleted successfully");

    // Redirects
    redirect("/listings");
  }


  // 6.
  /**
   * Show a single listing edit form
   *  
   * @param array $params - URL parameters (e.g., ['id' => 5])
   * @return void
   */
  public function edit($params)
  {

    // Extracts 'id' from passed URL parameters
    $id = $params["id"];

    // Prepares an array for binding query parameters
    $params = ["id" => $id];

    // Fetches a listing with the given ID
    $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();

    // If no listing found, show 404 error page
    if (!$listing) {
      ErrorController::notFound("Listing not found.");
      return;
    }

    // Authorization - checks if user own this resource
    if (!Authorization::isOwner($listing->user_id)) {
      // sets errors messages using Session class
      Session::setFlashMessage("error_message", "You are not authorized to update this listing");
      return  redirect("/listings/{$listing->id}");
    }

    // Loads the 'listings/show' view and passes the listing data (as variable)
    loadView("listings/edit", ["listing" => $listing]);
  }


  // 7.
  /**
   * Update a single listing
   *  
   * @param array $params - URL parameters (e.g., ['id' => 5])
   * @return void
   */
  public function update($params)
  {
    // Extracts 'id' from passed URL parameters
    $id = $params["id"];

    // Prepares an array for binding query parameters
    $params = ["id" => $id];

    // Fetches a listing with the given ID
    $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();

    // If no listing found, show 404 error page
    if (!$listing) {
      ErrorController::notFound("Listing not found.");
      return;
    }

    // Authorization - checks if user own this resource
    if (!Authorization::isOwner($listing->user_id)) {
      // sets errors messages using Session class
      Session::setFlashMessage("error_message", "You are not authorized to update this listing");
      return  redirect("/listings/{$listing->id}");
    }

    // Extracts only allowed fields from submitted form
    $allowedFields = ["title", "description", "salary", "tags", "company", "address", "city", "state", "phone", "email", "requirements", "benefits"];
    $updateValues = [];

    // checks submitted fields and leaves only our fields
    // array_intersect_key() - returns key only if it in both arrays
    // array_flip() - flips keys to values and in opposite
    $updateValues = array_intersect_key($_POST, array_flip($allowedFields));


    // Sanitizes values
    $updateValues = array_map("sanitize", $updateValues);

    // Strictly required fields 
    $requiredFields = ["title", "description", "email", "city", "state", "salary"];
    // Checks inputs and validates them
    $errors = [];
    foreach ($requiredFields as $field) {
      if (empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
        // If any of the required fields are empty or do not pass validation, add to errors array
        $errors[$field] = ucfirst($field) . " is required";
      }
    }

    if (!empty($errors)) {
      loadView("listings/edit", [
        "listing" => $listing,
        "errors" => $errors
      ]);
      exit;
    } else {
      // Submit to database


      // Builds query
      // Creates field to insert into placeholders (field = :field, field= :field)
      $updateFields = [];
      foreach (array_keys($updateValues) as $field) {
        $updateFields[] = "{$field} = :{$field}";
      }
      $updateFields = implode(", ", $updateFields);

      $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";

      // Adds id to array since its coming from $params and we did not insert it into fields to update initially
      $updateValues["id"] = $id;

      // Inserts into db by calling Database instance method
      $this->db->query($updateQuery, $updateValues);

      // Sets flash success messages using Session class
      Session::setFlashMessage("success_message",  "Listings updated successfully");

      // redirects
      redirect("/listings/" . $id);
    }
  }
}
