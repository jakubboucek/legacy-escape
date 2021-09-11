<?php

// Intentionally no strict – package is most used to secure legacy projects
// declare(strict_types=1);

namespace JakubBoucek\Escape;

use Nette\Utils\Json;

/**
 * Escape funxtions. Uses UTF-8 only.
 * Substrate of Filters class from Latte/Latte package
 *
 * @link https://latte.nette.org/
 * @link https://api.nette.org/2.4/source-Latte.Runtime.Filters.php.html#Filters
 */
class Escape
{
    /**
     * Escapes string for use everywhere inside HTML (except for comments)
     * @param string|mixed $data
     * @return string
     *
     * @link https://api.nette.org/2.4/source-Latte.Runtime.Filters.php.html#27-35
     */
    public static function html($data): string
    {
        return htmlspecialchars((string)$data, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE);
    }

    /**
     * Escapes string for use inside HTML attribute value.
     * @param string|mixed $data
     * @return string
     *
     * @link https://api.nette.org/2.4/source-Latte.Runtime.Filters.php.html#_escapeHtmlAttr
     */
    public static function htmlAttr($data): string
    {
        $data = (string)$data;
        if (strpos($data, '`') !== false && strpbrk($data, ' <>"\'') === false) {
            $data .= ' '; // protection against innerHTML mXSS vulnerability nette/nette#1496
        }
        return self::html($data);
    }

    /**
     * Escapes string for use inside HTML comments.
     * @param string|mixed $data
     * @return string
     *
     * @link https://api.nette.org/2.4/source-Latte.Runtime.Filters.php.html#_escapeHtmlComment
     */
    public static function htmlComment($data): string
    {
        $data = (string)$data;
        if ($data && ($data[0] === '-' || $data[0] === '>' || $data[0] === '!')) {
            $data = ' ' . $data;
        }
        $data = str_replace('--', '- - ', $data);
        if (substr($data, -1) === '-') {
            $data .= ' ';
        }
        return $data;
    }

    /**
     * Escapes string for use inside JS code
     * @param mixed $data
     * @return string
     *
     * @link https://api.nette.org/2.4/source-Latte.Runtime.Filters.php.html#_escapeJs
     */
    public static function js($data): string
    {
        $json = Json::encode($data);

        return str_replace([']]>', '<!', '</'], [']]\u003E', '\u003C!', '<\/'], $json);
    }

    /**
     * Escapes string for use inside CSS code
     * @param string|mixed $data
     * @return string
     *
     * @link https://api.nette.org/2.4/source-Latte.Runtime.Filters.php.html#_escapeCss
     */
    public static function css($data): string
    {
        // http://www.w3.org/TR/2006/WD-CSS21-20060411/syndata.html#q6
        return addcslashes((string)$data, "\x00..\x1F!\"#$%&'()*+,./:;<=>?@[\\]^`{|}~");
    }

    /**
     * Escapes string for use inside URL
     * @param string|mixed $url
     * @return string
     */
    public static function url($url): string
    {
        return urlencode((string)$url);
    }

    /**
     * Just returns argument as is without any escaping
     * Method is useful to mark code as intentionally unescaped as opposed to simple neglected
     * @param string|mixed $url
     * @return string
     */
    public static function noescape($url): string
    {
        return (string)$url;
    }
}
