<?php

// Inserts routes into "routes" array using Router instance methods (router is available as we require this file after iniating the new instance)
$router->get("/", "HomeController@index");
$router->get("/listings", "ListingController@index");
$router->get("/listings/create", "ListingController@create", ["auth"]);


$router->get("/listings/edit/{id}", "ListingController@edit", ["auth"]);
$router->get("listings/search", "ListingController@search");
$router->get("/listings/{id}", "ListingController@show"); // The {id} is a placeholder for the dynamic part of the URL.


$router->post("/listings", "ListingController@store", ["auth"]);


$router->put("/listings/{id}", "ListingController@update", ["auth"]);

//
$router->delete("/listings/{id}", "ListingController@destroy", ["auth"]);


//
$router->get("/auth/register", "UserController@create", ["guest"]);
$router->get("/auth/login", "UserController@login", ["guest"]);


$router->post("/auth/register", "UserController@store", ["guest"]);
$router->post("/auth/logout", "UserController@logout", ["auth"]);
$router->post("/auth/login", "UserController@authenticate", ["guest"]);
