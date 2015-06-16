<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Multilanguage module Request class
 */
class Multilanguage_Request extends Kohana_Request
{

    /**
     * @var string request language code
     */
    public static $lang = '';

    /**
     * Extension of the request factory method.
     *
     * @param   string   URI of the request
     * @param Kohana_Cache cache object
     * @return  Request
     */
    public static function factory($uri = TRUE, $client_params = array(),
        $allow_external = TRUE, $injected_routes = array())
    {
        // Load config
        $config = Kohana::$config->load('multilanguage');

        if(Request::$lang == ''){

            if($uri === true){
                $uri = Request::detect_uri();
            }

            // Normalize URI
            $uri = ltrim($uri, '/');
            // process language
            $lang = '';
            // regex matches
            $matches = array();

            // Look for a supported language in the first URI segment
            if(!preg_match(
               '~^(?:'.implode('|', array_keys($config['languages'])).')(?=/|$)~i',
               $uri, $matches)
            ){
                // If we have don't a matched language

                if($config['default_changes_url']){
                    Multilanguage::redirect(URL::base(true, true).Multilanguage::find_default().'/'.$uri);
                }

                $lang = $config['default'];
            }else{
                // If we have a matched language
                // Normalize language
                $lang = strtolower($matches[0]);

                // Remove language from URI
                $uri = substr($uri, strlen($lang));

                // fallback for root request
                if(empty($uri)) $uri = '/';

                // check if we need to redirect from /default/ to the root
                if(!$config['default_changes_url'] AND $lang === $config['default']){
                    Multilanguage::redirect($uri);
                }
            }

            Request::$lang = $lang;

            Multilanguage::init();
        }

        return parent::factory($uri, $client_params, $allow_external,
                $injected_routes);
    }

}