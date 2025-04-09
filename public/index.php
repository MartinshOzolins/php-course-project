<?php
// TO START LOCAL PHP SERVER FOR DEVELOPMENT (serves 'public' as the root folder):
// php -S localhost:8000 -t public

// Requires autoload.php from vendor folder to use classes without imports
require __DIR__ . "/../vendor/autoload.php";

// autoloads classes with autoloader 
use Framework\Router;
use Framework\Session;

// Initiates session
Session::start();

// imports helper functions to make them available throughout the entire code
require "../helpers.php";


// ✅ Composer Autoloader: a modern, efficient way to automatically load PHP classes
// - Instead of manually including files (e.g., require 'Class.php'), Composer loads classes based on namespaces and directory structure.
// - This also works with external libraries/packages, making it more powerful than manual autoloading (like spl_autoload_register).

// 🔧 To set up Composer autoloading:
// 1. Create a `composer.json` file in your project root.
// 2. Add an `autoload` section using PSR-4 standard:
//    {
//      "autoload": {
//        "psr-4": {
//          "Framework\\": "Framework/",
//          "App\\": "App/"
//        }
//      }
//    }

// 3. Run `composer install` or `composer dump-autoload` in the terminal:
//    - This generates the `vendor/` folder and the `vendor/autoload.php` file.
//    - Think of `vendor/` like `node_modules` in JavaScript—it contains all dependencies and the autoloader.

// 📁 How the autoloader works:
// - When you write `use Framework\Database;`, Composer looks for the file `Framework/Database.php`.
// - The namespace (`Framework\\`) maps to the folder (`Framework/`).
// - The double backslash (`\\`) is needed in JSON to escape the character, so it becomes a single `\` in actual PHP.

// 📁 You only need to specify the *outer* folder (e.g., "App\\") in composer.json.
// - Composer will automatically locate inner folders based on the namespace path.
// - Example: `use App\Controllers\ErrorController;`
//   → Composer looks for: `App/Controllers/ErrorController.php`
//   → This works because we mapped `"App\\": "App/"` in composer.json.


// 🧠 Important notes:
// - In each class file, declare its namespace at the top like: `namespace Framework;`
// - This tells PHP that the class belongs to the `Framework` namespace (and should be located in `Framework/` directory).
// - With this setup, you can use classes across your project without requiring each one manually.

// 🧪 Final tip:
// - Anytime you add new classes or change namespace mappings in composer.json, run:
//   `composer dump-autoload`
//   to regenerate the autoloader.





// initializes the Router instance
$router = new Router();

// loads the routes and adds them to the "router" using instance methods (inside "routes.php" we use router->get("url", "controller to match"); )
require basePath("routes.php");

// gets the current URI (path and query string) after the root URL (e.g., /params?urlQueryParams=value or simply "/listings")
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH); // parse_url(PHP_URL_PASS) only returns /path without url query params


// match the current params with the correct controller/view using the "route" instance method
$router->route($uri);
