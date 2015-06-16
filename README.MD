# Kohana Multilanguage module

Multilanguage module for Kohana PHP Framework, version 3.3

Based on this module https://github.com/cichys/kohana-mlang .
updated for Kohana 3.3 and added some features



## Configuration

  :::php

    return array(
        /**
         * Default site language
         */
        'default'             =>'pt',
        /**
         * Example: default language "pt" redirects to "/pt/"
         */
        'default_changes_url' =>FALSE,
        /**
         * Name of cookie used to save language
         */
        'cookie'              =>'lang',
        /**
         * Detect HTTP accepted languages and use most compatible,
         * Otherwise it will not automatically change the site's language
         */
        'detect_accepted_lang'=>TRUE,
        /**
         * setlocale settings
         * Options:
         *  * FALSE - Don't change
         *  * LC_ALL - All of the below
         *  * LC_CTYPE|LC_NUMERIC|LC_TIME|LC_COLLATE|LC_MONETARY|LC_MESSAGES
         *
         * @link http://php.net/manual/en/function.setlocale.php
         */
        'setlocale'           =>LC_ALL,
        /**
         * set language in i18n?
         */
        'seti18n'             =>TRUE,
        /**
         * Available languages
         */
        'languages'           =>array(
            'pt'=>array(
                'i18n'  =>'pt_PT',
                'locale'=>array('pt_PT.utf-8'),
                'label'=>'portugues',
            ),
            'en'   =>array(
                'i18n'  =>'en_GB',
                'locale'=>array('en_GB.utf-8'),
                'label'=>'english',
            ),
        ),
    );


## Access current language

i18n::$lang;
