<?php defined('SYSPATH') or die('No direct script access.');

class Multilanguage_URL extends Kohana_URL {
	
	/*
	 * Fetches an absolute site URL based on a URI segment
	 * and adds lang code to uri
	 */	
	public static function site($uri = '', $protocol = NULL, $index = TRUE, $lang = NULL)
	{
      // Chop off possible scheme, host, port, user and pass parts
      $path = preg_replace('~^[-a-z0-9+.]++://[^/]++/?~', '', trim($uri, '/'));

      if ( ! UTF8::is_ascii($path)){
         // Encode all non-ASCII characters, as per RFC 1738
         $path = preg_replace_callback('~([^/]+)~', 'URL::_rawurlencode_callback', $path);
      }

      $config = Kohana::$config->load('multilanguage');

      $empty_default = ! $config->default_changes_url && $config->default == Request::$lang;

      // Force add selected language
      if(isset($config['languages'][$lang])){
         $path_wo_lang = Multilanguage::uri_strip_lang($path);

         // If current lang is default and default_changes_url is false
         if(! $config->default_changes_url && $config->default == $lang)
            return URL::base($protocol, $index).$path_wo_lang;
         else
            return URL::base($protocol, $index).$lang.'/'.$path_wo_lang;
      }

      // If already has lang and $lang is not set
      if(Multilanguage::uri_has_lang($path) && null == $lang)
      {
         return URL::base($protocol, $index).$path;
      }

      $file_exists = file_exists(DOCROOT.$path);

      // Add language if link is not for existed file or root path
      if(! $path || (Request::$lang && ! $file_exists && ! $empty_default))
      {
         $path = Request::$lang.'/'.$path;
      }

      if($file_exists)
      {
         return URL::base($protocol, false).$path;
      }

      return URL::base($protocol, $index).$path;
	}
}