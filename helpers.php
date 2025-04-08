<?php
// Helper functions

// These explanations are just for the developers to easier read the code
/**
 * 1. Gets the base path (used in other files to avoid writing hard coded absolute paths)
 * 
 * @param string $path
 * @return string 
 */
function basePath($path = "")
{
  return __DIR__ . "/" . $path;
}


/**
 * 2. Loads a view (used in controllers to load a specific view and insert data)
 * 
 * @param string $name
 * @return void
 */
function loadView($name, $data = [])
{
  $viewPath = basePath("App/views/{$name}.view.php");

  // Check if the file exists at the given path
  // file_exists($file) - Returns true if file exists, false otherwise
  if (file_exists($viewPath)) {
    extract($data); // extracts key/values from an array and makes them available within the file as variables
    require $viewPath;
  } else {
    echo "View {$name} not found! ";
  }
}


/**
 * 3. Loads a partial into views
 * 
 * @param string $name
 * @return void
 */
function loadPartial($name, $data = [])
{
  $partialPath = basePath("App/views/partials/{$name}.php");

  // if file exists, require
  if (file_exists($partialPath)) {
    extract($data);
    require $partialPath;
  } else {
    echo "View {$name} not found! ";
  }
}

/**
 * 4. Inspects a value(s) (good for development)
 * @param mixed $value
 * @return void
 */
function inspect($value)
{
  echo "<pre>";
  var_dump($value);
  echo "</pre>";
}


/**
 * 5.Inspects a value(s) and die (stops execution of script) (good for development)
 * @param mixed $value
 * @return void
 */
function inspectAndDie($value)
{
  echo "<pre>";
  die(var_dump($value)); // dumps info about a variable and stops execution of the script
  echo "</pre>";
}


/**
 * 6. Format salary
 * @param string $salary
 * @return string Formatted Salary
 */
function formatSalary($salary)
{
  return "$" . number_format(floatval($salary));
}



/**
 * 7. Sanitize Data
 * @param string $dirty
 * @return string
 */
function sanitize($dirty)
{
  return filter_var(trim($dirty), FILTER_SANITIZE_SPECIAL_CHARS);
}


/**
 * 7. Redirect to a given url
 * @param string $url
 * @return void
 */
function redirect($url)
{
  header("Location: {$url}");
  exit;
}
