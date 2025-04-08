<?php


namespace Framework;

use PDO;

class Database
{
  public $conn;

  /**
   * 1. Constructor for the Database class.
   * Initializes the database connection using the provided configuration.
   * @param array $config
   */
  function __construct($config)
  {
    // Creates connection string using the provided configuration
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";

    // Database connection options
    // - PDO::ATTR_ERRMODE: Enables exceptions for errors
    // - PDO::ATTR_DEFAULT_FETCH_MODE: Sets the default fetch mode to objects (listing->title instead of associative listing["title"])
    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ];

    try {
      // Save the database connection to the 'conn' property
      // This creates a new instance of PDO, which allows interaction with the database
      $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);
    } catch (\PDOException $e) {
      // If connection fails, throw a custom exception
      throw new \Exception("Database connection failed: {$e->getMessage()}");
    }
  }



  /**
   * 2.Queries the database with the provided query and optional parameters (in controllers)
   * @param string $query
   * @return PDOStatement
   * @throws PDOException
   */
  public function query($query, $params = [])
  {
    try {
      // Prepares the query with the passed query(e.g. "SELECT * FROM ..)
      $sth = $this->conn->prepare($query);

      // Bind the provided parameters to the prepared statement
      // For example: ":id" => 2 (binds the placeholder ':id' to the value 2)
      foreach ($params as $param => $value) {
        $sth->bindValue(":" . $param, $value);
      }

      // Execute the prepared statement
      $sth->execute();

      // Return the PDOStatement object for further processing
      return $sth;
    } catch (\PDOException $e) {
      // If query execution fails, throw a custom exception

      throw new \Exception("Query failed to execute: {$e->getMessage()}");
    }
  }
}
