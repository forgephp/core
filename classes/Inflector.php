<?php defined( 'FOUNDATION' ) or die( 'No direct script access.' );

/**
 * Inflector helper class. Inflection is changing the form of a word based on
 * the context it is used in. For example, changing a word into a plural form.
 *
 * [!!] Inflection is only tested with English, and is will not work with other languages.
 *
 * @package    SuperFan
 * @category   Helpers
 * @author     Zach Jenkins <zach@superfanu.com>
 * @copyright  (c) 2017 SuperFan, Inc.
 */
class Inflector
{
    /**
     * @var  array  cached inflections
     */
    protected static $cache = array();

    /**
     * @var  array  uncountable words
     */
    protected static $uncountable;

    /**
     * @var  array  irregular words
     */
    protected static $irregular;

    /**
     * Checks if a word is defined as uncountable. An uncountable word has a
     * single form. For instance, one "fish" and many "fish", not "fishes".
     *
     *     Inflector::uncountable('fish'); // TRUE
     *     Inflector::uncountable('cat');  // FALSE
     *
     * If you find a word is being pluralized improperly, it has probably not
     * been defined as uncountable in `config/inflector.php`.
     *
     * @param   string  $str    word to check
     * @return  boolean
     */
    public static function uncountable( $str )
    {
        if( self::$uncountable === NULL )
        {
            // Cache uncountables
            self::$uncountable = Foundation::$config->load( 'inflector' )->uncountable;

            // Make uncountables mirrored
            self::$uncountable = array_combine( self::$uncountable, self::$uncountable );
        }

        return isset( self::$uncountable[strtolower( $str )] );
    }

    /**
     * Makes a plural word singular.
     *
     *     echo Inflector::singular('cats'); // "cat"
     *     echo Inflector::singular('fish'); // "fish", uncountable
     *
     * You can also provide the count to make inflection more intelligent.
     * In this case, it will only return the singular value if the count is
     * greater than one and not zero.
     *
     *     echo Inflector::singular('cats', 2); // "cats"
     *
     * [!!] Special inflections are defined in `config/inflector.php`.
     *
     * @param   string  $str    word to make singular
     * @param   integer $count  count of thing
     * @return  string
     * @uses    Inflector::uncountable
     */
    public static function singular( $str, $count = NULL )
    {
        // $count should always be a float
        $count = ( $count === NULL ) ? 1.0 : (float) $count;

        // Do nothing when $count is not 1
        if( $count != 1 )
        {
            return $str;
        }

        // Remove garbage
        $str = strtolower( trim( $str ) );

        // Cache key name
        $key = 'singular_' . $str . $count;

        if( isset( self::$cache[$key] ) )
        {
            return self::$cache[$key];
        }

        if( self::uncountable( $str ) )
        {
            return self::$cache[$key] = $str;
        }

        if( empty( self::$irregular ) )
        {
            // Cache irregular words
            self::$irregular = Foundation::$config->load('inflector')->irregular;
        }

        if( $irregular = array_search( $str, self::$irregular ) )
        {
            $str = $irregular;
        }
        else if( preg_match( '/us$/', $str ) )
        {
            // http://en.wikipedia.org/wiki/Plural_form_of_words_ending_in_-us
            // Already singular, do nothing
        }
        else if( preg_match( '/[sxz]es$/', $str ) || preg_match( '/[^aeioudgkprt]hes$/', $str ) )
        {
            // Remove "es"
            $str = substr( $str, 0, -2 );
        }
        else if( preg_match( '/[^aeiou]ies$/', $str ) )
        {
            // Replace "ies" with "y"
            $str = substr( $str, 0, -3 ) . 'y';
        }
        else if( substr( $str, -1 ) === 's'  && substr( $str, -2 ) !== 'ss' )
        {
            // Remove singular "s"
            $str = substr( $str, 0, -1 );
        }

        return self::$cache[$key] = $str;
    }

    /**
     * Makes a singular word plural.
     *
     *     echo Inflector::plural('fish'); // "fish", uncountable
     *     echo Inflector::plural('cat');  // "cats"
     *
     * You can also provide the count to make inflection more intelligent.
     * In this case, it will only return the plural value if the count is
     * not one.
     *
     *     echo Inflector::singular('cats', 3); // "cats"
     *
     * [!!] Special inflections are defined in `config/inflector.php`.
     *
     * @param   string  $str    word to pluralize
     * @param   integer $count  count of thing
     * @return  string
     * @uses    Inflector::uncountable
     */
    public static function plural( $str, $count = NULL )
    {
        // $count should always be a float
        $count = ( $count === NULL ) ? 0.0 : (float) $count;

        // Do nothing with singular
        if( $count == 1 )
        {
            return $str;
        }

        // Remove garbage
        $str = trim( $str );

        // Cache key name
        $key = 'plural_' . $str . $count;

        // Check uppercase
        $is_uppercase = ctype_upper( $str );

        if( isset( self::$cache[$key] ) )
        {
            return self::$cache[$key];
        }

        if( self::uncountable( $str ) )
        {
            return self::$cache[$key] = $str;
        }

        if( empty( self::$irregular ) )
        {
            // Cache irregular words
            self::$irregular = Foundation::$config->load('inflector')->irregular;
        }

        if( isset( self::$irregular[$str] ) )
        {
            $str = self::$irregular[$str];
        }
        else if( in_array( $str, self::$irregular ) )
        {
            // Do nothing
        }
        else if( preg_match( '/[sxz]$/', $str ) || preg_match( '/[^aeioudgkprt]h$/', $str ) )
        {
            $str .= 'es';
        }
        else if( preg_match( '/[^aeiou]y$/', $str ) )
        {
            // Change "y" to "ies"
            $str = substr_replace( $str, 'ies', -1 );
        }
        else
        {
            $str .= 's';
        }

        // Convert to uppercase if necessary
        if( $is_uppercase )
        {
            $str = strtoupper( $str );
        }

        // Set the cache and return
        return self::$cache[$key] = $str;
    }

    /**
     * Makes a phrase camel case. Spaces and underscores will be removed.
     *
     *     $str = Inflector::camelize('mother cat');     // "motherCat"
     *     $str = Inflector::camelize('kittens in bed'); // "kittensInBed"
     *
     * @param   string  $str    phrase to camelize
     * @return  string
     */
    public static function camelize( $str )
    {
        $str = 'x' . strtolower( trim( $str ) );
        $str = ucwords( preg_replace( '/[\s_]+/', ' ', $str ) );

        return substr(str_replace( ' ', '', $str ), 1 );
    }

    /**
     * Converts a camel case phrase into a spaced phrase.
     *
     *     $str = Inflector::decamelize('houseCat');    // "house cat"
     *     $str = Inflector::decamelize('kingAllyCat'); // "king ally cat"
     *
     * @param   string  $str    phrase to camelize
     * @param   string  $sep    word separator
     * @return  string
     */
    public static function decamelize( $str, $sep = ' ' )
    {
        return strtolower( preg_replace( '/([a-z])([A-Z])/', '$1' . $sep . '$2', trim( $str ) ) );
    }

    /**
     * Makes a phrase underscored instead of spaced.
     *
     *     $str = Inflector::underscore('five cats'); // "five_cats";
     *
     * @param   string  $str    phrase to underscore
     * @return  string
     */
    public static function underscore( $str )
    {
        return preg_replace( '/\s+/', '_', trim( $str ) );
    }

    /**
     * Makes an underscored or dashed phrase human-readable.
     *
     *     $str = Inflector::humanize('kittens-are-cats'); // "kittens are cats"
     *     $str = Inflector::humanize('dogs_as_well');     // "dogs as well"
     *
     * @param   string  $str    phrase to make human-readable
     * @return  string
     */
    public static function humanize( $str )
    {
        return preg_replace( '/[_-]+/', ' ', trim( $str ) );
    }
}
