<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Multilanguage module routes class
 */
class Multilanguage_Routes {

  protected $_routes = array();

  /**
   * Set routes for each language
   * You can pass an array with the language code as the key and the uri as the value.
   *
   *    Routes::set('homepage', array(
   *        'en'    => 'home',
   *        'fr'    => 'accueil',
   *      ))->defaults(array(
   *        'controller'    => 'homepage',
   *        'action'      => 'index',
   *      ));
   *
   * @param   string   route name
   * @param   array   URI patterns (array of "language code" => "uri")
   * @param   array    regex patterns for route keys
   * @return  Routes
   */
  public static function set($name, array $uris = array(), $regex = NULL)
  {
    $routes = new Routes();

    // we add all the routes setting the name to code.name (en.homepage for example).
    foreach($uris as $lang => $uri)
    {
      $routes->_routes[$lang.'.'.$name] = Route::set($name, $uri, $regex, $lang);
    }

    return $routes;
  }


  /**
   * Set the defaults values for each route
   * @param array $defaults
   * @return Multilanguage_Routes
   */
  public function defaults(array $defaults = NULL)
  {
    foreach($this->_routes as $route)
    {
      $route->defaults($defaults);
    }

    return $this;
  }


}