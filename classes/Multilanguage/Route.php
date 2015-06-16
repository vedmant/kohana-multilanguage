<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Multilanguage module Route class
 */
class Multilanguage_Route extends Kohana_Route {

  protected $_lang;

  /**
   * Stores a named route and returns it. The "action" will always be set to
   * "index" if it is not defined.
   *
   *     Route::set('default', '(<controller>(/<action>(/<id>)))', $regex, $lang)
   *         ->defaults(array(
   *             'controller' => 'welcome',
   *         ));
   *
   *      Route::set('home', array(
   *        'pt' => 'pagina_inicial',
   *        'en' => 'homepage',
   *      ), $regex)
   *      ->defaults(array(
   *        'controller' => 'home',
   *        'action' => 'index'
   *      ));
   *
   *      Route::set('home', 'pagina_inicial', NULL, 'pt')->defaults(array(
   *        'controller'  => 'home',
   *        'action'      => 'index',
   *      ));
   *      Route::set('home', 'homepage', NULL, 'en')->defaults(array(
   *        'controller'  => 'home',
   *        'action'      => 'index',
   *      ));
   *
   * @param   string  $name           route name
   * @param   string  $uri            URI pattern
   * @param   array   $regex          regex patterns for route keys
   * @param   string  $lang           route language
   * @return  Route
   */
  public static function set($name, $uri = NULL, $regex = NULL, $lang = NULL)
  {
    if (is_array($uri))
    {
      foreach ($uri as $lang => $iuri)
      {
        Route::$_routes[$lang.'.'.$name] = new Route($iuri, $regex, $lang);
      }

      $name = $lang.'.'.$name;
    }
    else
    {
      $lang !== NULL and $name = $lang.'.'.$name;
      Route::$_routes[$name] = new Route($uri, $regex, $lang);
    }

    return Route::$_routes[$name];
  }

  public static function get($name, $lang = NULL)
  {
    // We use the current language if none given
    if ($lang === NULL)
    {
      $lang = Request::$lang;
    }

    // We first look for a "given_language.name" route.
    if (isset(Route::$_routes[$lang.'.'.$name]))
    {
      $name = $lang.'.'.$name;
    }
    // then the default language
    elseif (isset(Route::$_routes[Kohana::$config->load('multilanguage.default').'.'.$name]))
    {
      $name = Kohana::$config->load('multilanguage.default').'.'.$name;
    }

    $route = parent::get($name);

    if ($route !== NULL)
    {
      $route->_lang = $lang;
    }

    return $route;
  }

  /**
   * Extended construct to support multilanguages
   *
   * Creates a new route. Sets the URI and regular expressions for keys.
   * Routes should always be created with [Route::set] or they will not
   * be properly stored.
   *
   *     $route = new Route($uri, $regex);
   *
   * The $uri parameter should be a string for basic regex matching.
   *
   *
   * @param   string  $uri    route URI pattern
   * @param   array   $regex  key patterns
   * @return  void
   * @uses    Route::_compile
   */
  public function __construct($uri = NULL, $regex = NULL, $lang = NULL)
  {
    $this->_lang = $lang;
    return parent::__construct($uri, $regex);
  }

  /**
   * Extended method to support multilingual uris
   *
   * Generates a URI for the current route based on the parameters given.
   *
   *     // Using the "default" route: "users/profile/10"
   *     $route->uri(array(
   *         'controller' => 'users',
   *         'action'     => 'profile',
   *         'id'         => '10'
   *     ));
   *
   * @param   array   $params URI parameters
   * @return  string
   * @throws  Kohana_Exception
   * @uses    Route::REGEX_Key
   */
  public function uri(array $params = NULL, $lang = NULL)
  {
    $uri = parent::uri($params);

    // We add the language code if required
    if ($this->_lang)
    {
      // we don't use the route language to avoid some issues with routes of different languages having the same pattern
      $lang = $lang === NULL ? Request::$lang : $lang;

      return $lang.'/'.$uri;
    }

    return $uri;
  }

  /**
   * Extended method to support multilanguages
   *
   * Create a URL from a route name. This is a shortcut for:
   *
   *     echo URL::site(Route::get($name)->uri($params), $protocol);
   *
   * @param   string  $name       route name
   * @param   array   $params     URI parameters
   * @param   mixed   $protocol   protocol string or boolean, adds protocol and domain
   * @param   string  $lang       route url language
   * @return  string
   * @since   3.0.7
   * @uses    URL::site
   */
  public static function url($name, array $params = NULL, $protocol = NULL, $lang = NULL)
  {
    // Create an URI with the route and convert it to a URL
    return URL::site(Route::get($name, $lang)->uri($params), $protocol);
  }

}