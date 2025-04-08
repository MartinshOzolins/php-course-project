<?php

namespace App\Controllers; // specifies the namespace this class uses

// used classes
use Framework\Database;
use Framework\Validation;
use Framework\Session;

class UserController
{
  protected $db;

  public function __construct()
  {
    // initialise db instance
    $config = require basePath("config/db.php");
    $this->db = new Database($config);
  }


  /**
   * Shows the login page
   * @return void
   */
  public function login()
  {
    loadView("users/login");
  }


  /**
   * Shows the register page
   * @return void
   */
  public function create()
  {
    loadView("users/create");
  }


  /**
   * Store user in database
   * @param
   */
  public function store()
  {

    ["name" => $name, "email" => $email, "city" => $city, "state" => $state, "password" => $password, "password_confirmation" => $passwordConfirmation] = $_POST;

    $errors = [];


    if (!Validation::email($email)) {
      $errors["email"] = "Please enter a valid email address";
    }


    if (!Validation::string($name, 2, 50)) {
      $errors["name"] = "Name must be between 2 and 50 characters";
    }

    if (!Validation::string($password, 6, 50)) {
      $errors["password"] = "Password must be at least 6 characters";
    }

    if (!Validation::match($password, $passwordConfirmation)) {
      $errors["password_confirmation"] = "Passwords do not match!! ❌";
    }

    if (!empty($errors)) {
      loadView("users/create", [
        "errors" => $errors,
        "user" => [
          "name" => $name,
          "email" => $email,
          "city" => $city,
          "state" => $state,
        ]
      ]);
      exit;
    }

    // Checks if email exists
    $params = [
      "email" => $email
    ];

    $user = $this->db->query("SELECT * FROM users WHERE email = :email", $params)->fetch();


    if ($user) {
      $errors["email"] = "That email already exists";
      loadView("/users/create", [
        "errors" => $errors,
        "user" => [
          "name" => $name,
          "email" => $email,
          "city" => $city,
          "state" => $state,
        ]
      ]);
      exit;
    }

    // Creates user account
    $params = [
      "name" => $name,
      "email" => $email,
      "city" => $city,
      "state" => $state,
      "password" => password_hash($password, PASSWORD_DEFAULT),
    ];

    $this->db->query("INSERT INTO users (name, email, city, state, password) VALUES (:name, :email, :city, :state, :password)", $params);


    // Get the new user ID using available pre-defined instance method from PDO database class
    $userId = $this->db->conn->lastInsertId();

    // Sets session with user details using Session static method (our created class)
    Session::set("user", [
      "id" => $userId,
      "name" => $name,
      "email" => $email,
      "city" => $city,
      "state" => $state,
    ]);

    redirect("/");
  }


  /**
   * Logout a user and kill session
   * @return void
   */
  public function logout()
  {
    Session::clearAll();

    $params = session_get_cookie_params(); // Get the session cookie parameters as an associative array, which includes => lifetime, path, domain, secure, httponly, samesite

    // clears cookie set by PHP (its named PHPSESSID)
    setcookie("PHPSESSID", "", time() - 86400, $params["path"], $params["domain"]);


    redirect("/");
  }



  /**
   * Authenticate a user with email and password
   * 
   * @return void 
   */
  public function authenticate()
  {

    // Desctructures posted data
    ["email" => $email, "password" => $password] = $_POST;

    $errors = [];

    // Checks for errors
    if (!Validation::email($email)) {
      $errors["email"] = "Please enter a valid email address";
    }

    if (!Validation::string($password, 6, 50)) {
      $errors["password"] = "Password must be at least 6 characters";
    }

    if (!empty($errors)) {
      loadView("users/login", ["errors" => $errors]);
      exit;
    }

    // Checks if email exists
    $params = [
      "email" => $email
    ];

    $user = $this->db->query("SELECT * FROM users WHERE email = :email", $params)->fetch();


    if (!$user) {
      $errors["email"] = "Incorrect credentials";
      loadView("/users/login", [
        "errors" => $errors,
      ]);
      exit;
    }


    // Checks if password is correct
    if (!password_verify($password, $user->password)) {
      $errors["email"] = "Incorrect credentials";
      loadView("/users/login", [
        "errors" => $errors,
      ]);
      exit;
    }

    // Sets session with user details using Session static method (our created class)
    Session::set("user", [
      "id" => $user->id,
      "name" => $user->name,
      "email" => $user->email,
      "city" => $user->city,
      "state" => $user->state,
    ]);

    redirect("/");
  }
}
