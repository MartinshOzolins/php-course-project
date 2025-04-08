<?php

namespace Framework; // Declares this class is part of the Framework namespace

use App\Controllers\ErrorController; // Imports the ErrorController class so we can use it for handling 404s

use Framework\Middleware\Authorize;

class Router
{
  // 🧾 This array will hold all registered routes (added via get(), post(), etc.)
  protected $routes = [];


  // 1. 🔧 Adds a route to the routes array (called by get(), post(), put(), delete())
  /**
   * Add a new route
   * @param string $method      HTTP method (GET, POST, etc.)
   * @param string $uri         The route URI (e.g., "/posts", "/posts/{id}")
   * @param string $action      The controller and method in "Controller@method" format
   * @param array $middleware
   * @return void
   */
  public function registerRoute($method, $uri, $action, $middleware)
  {
    // Split "PostController@show" into ["PostController", "show"]
    list($controller, $controllerMethod) = explode("@", $action);

    $this->routes[] = [
      "method" => $method,
      "uri" => $uri,
      "controller" => $controller,
      "controllerMethod" => $controllerMethod,
      "middleware" => $middleware
    ];
  }

  /**
   * Adds a GET route
   *
   * @param string $uri
   * @param string $controller
   * @param array $middleware
   * @return void
   */
  // 2. 📥 Shortcut method to register a GET route
  public function get($uri, $controller, $middleware = [])
  {
    $this->registerRoute("GET", $uri, $controller, $middleware);
  }

  // 3. 📤 Shortcut method to register a POST route
  public function post($uri, $controller, $middleware = [])
  {
    $this->registerRoute("POST", $uri, $controller, $middleware);
  }

  // 4. ✏️ Shortcut method to register a PUT route
  public function put($uri, $controller, $middleware = [])
  {
    $this->registerRoute("PUT", $uri, $controller, $middleware);
  }

  // 5. ❌ Shortcut method to register a DELETE route
  public function delete($uri, $controller, $middleware = [])
  {
    $this->registerRoute("DELETE", $uri, $controller, $middleware);
  }


  // 6. This method checks the current request against all registered routes and runs the matching controller
  /**
   * Route the request
   * @param string $uri     The current request URI (e.g., "/posts/42")
   * @return void
   */
  public function route($uri)
  {
    // Get the request method (GET, POST, etc.) for the current page load
    $requestMethod = $_SERVER["REQUEST_METHOD"];


    // Checks for _method input (for DELETE, PUT routes)
    if ($requestMethod === "POST" && isset($_POST["_method"])) {
      // Ovverides the request method with the value of _method
      $requestMethod = strtoupper($_POST["_method"]);
    }

    // Loop through all the registered routes
    foreach ($this->routes as $route) {

      // Break both the requested URI and route URI into parts (e.g. "/posts/42" → ["posts", "42"])
      $uriSegments = explode("/", trim($uri, "/"));
      $routeSegments = explode("/", trim($route["uri"], "/"));

      $match = true;

      // Check if segment count and HTTP method match
      if (count($uriSegments) === count($routeSegments) && strtoupper($route['method']) === $requestMethod) {

        $params = []; // Store any parameters like {id} from the route

        for ($i = 0; $i < count($uriSegments); $i++) {

          // If segments are not equal and the route does NOT expect a parameter, it's not a match
          if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
            $match = false;
            break; // Exit loop early if mismatch
          }

          // RegEx checks if segment is a parameter (e.g., {id}) and extracts its value from the URI
          if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
            // if current registered route's segment contains {id}, it will be matched and saved into matches array, which then we access and get dynamic value from user's URI
            // $matches[1] will be "id", so we set $params["id"] = "42"
            // $uriSegment[$i] gets dynamic value, since segment count matches and it should be there
            $params[$matches[1]] = $uriSegments[$i];
          }
        }

        if ($match) {

          // checks if user is authorised (not creating a variable since Authorize class will redirect if user is accessing the wrong route)
          foreach ($route["middleware"] as $middleware) {
            (new Authorize())->handle($middleware);
          }

          // Build full controller class path using namespace
          $controller = "App\\Controllers\\" . $route["controller"];
          $controllerMethod = $route["controllerMethod"];

          // Instantiate the controller class and call the method with any parameters
          // When we use "new $controller", PHP does not explicitly require an import because the autoloader (via Composer) will automatically include the class based on its namespace, as defined in the "composer.json" file.
          $controllerInstance = new $controller();
          $controllerInstance->$controllerMethod($params);
          return; // Route matched and executed — stop here

        }
      }
    }

    // No matching route found — load the error page
    ErrorController::notFound();
  }
}
