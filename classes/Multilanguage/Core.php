<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Multilanguage
 */
class Multilanguage_Core
{

   /**
    * Initialize the config and cookies
    *
    * @used-by Request
    */
   public static function init()
   {
      // Load configuration
      $config = Kohana::$config->load('multilanguage');

      // Suported languages
      $langs = $config['languages'];

      // Default language
      $default = $config['default'];

      // Do we already have a set cookie language?
      $lang = Request::$lang === '' ? $default : Request::$lang;

      if($config['seti18n'] === true){
         // Set language in I18n
         I18n::lang($lang);
      }

      if($config['setlocale'] !== false){
         // Set global locale
         setlocale($config['setlocale'], $langs[$lang]['locale']);
      }

      if(Cookie::get($config['cookie']) !== Request::$lang){
         // Set cookie lang if it differs from request
         Cookie::set($config['cookie'], Request::$lang);
      }
   }

   /**
    * Looks for the best default language available and returns it.
    * A language cookie and HTTP Accept-Language headers are taken into account.
    *
    * @used-by Request
    *
    * @return  string  language key, e.g. "en", "fr", "nl", etc.
    */
   public static function find_default()
   {
      // Load configuration
      $config = Kohana::$config->load('multilanguage');

      // Look for cookie language first
      if($lang = Cookie::get($config['cookie']) AND isset($config['languages'][$lang])){
         // Valid language found in cookie
         return $lang;
      }else{
         // Delete unset lang
         Cookie::delete($config['cookie']);
      }

      // Parse HTTP Accept-Language headers
      if($config['detect_accepted_lang']){
         foreach(Request::accept_lang() as $lang => $quality){
            // Return the first language found (the language with the highest quality)
            if(isset($config['languages'][$lang])){
               return $lang;
            }
         }
      }

      // Return the hard-coded default language as final fallback
      return $config['default'];
   }

   public static function uri_has_lang($uri)
   {
      $config = Kohana::$config->load('multilanguage');

      // If language already added
      if(preg_match('/^('.implode('\/|', array_keys($config->languages)).').*/i', $uri)){
         return true;
      }

      return false;
   }

   public static function uri_strip_lang($uri)
   {
      $config = Kohana::$config->load('multilanguage');

      // Remove language from URL
      return trim(preg_replace('/^('.implode('\/|', array_keys($config->languages)).')/i', '', $uri), '/');
   }

   public static function get_languages()
   {
      $config = Kohana::$config->load('multilanguage');

      $languages = array();

      $curr_lang = Request::$lang === '' ? $config->languages[$config->default] : Request::$lang;

      foreach($config->languages as $code => $lang){
         $languages['languages'][$code] = $lang;
         $languages['languages'][$code]['active'] = false;

         if($code == $curr_lang){
            $languages['languages'][$code]['active'] = true;
            $languages['active'] = $lang;
         }
      }

      return $languages;
   }


   /**
    * Redirects with headers
    *
    * @param type $uri
    */
   public static function redirect($uri)
   {
      header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL']
            : HTTP::$protocol).' 302 Found');
      header('Vary: Accept-Language,Accept-Encoding');
      header('Location: '.$uri);
      exit;
   }

}