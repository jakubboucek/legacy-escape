<?php

// Intentionally no strict – package is most used to secure legacy projects
// declare(strict_types=1);

namespace JakubBoucek\Escape;

use Nette\HtmlStringable;
use Nette\Utils\IHtmlString;
use RuntimeException;

/**
 * Escape functions. Uses UTF-8 only.
 * Substrate of Filters class from Latte/Latte package.
 *
 * @link https://latte.nette.org/
 * @link https://api.nette.org/2.4/source-Latte.Runtime.Filters.php.html#Filters
 */
class Escape
{
    /**
     * Escapes strings for use everywhere inside HTML (except for comments) and concatenate it to string.
     * @param string|HtmlStringable|IHtmlString|mixed ...$data
     * @return string
     *
     * @link https://api.nette.org/2.4/source-Latte.Runtime.Filters.php.html#27-35
     */
    public static function html(...$data): string
    {
        $output = '';

        foreach ($data as $item) {
            if ($item instanceof HtmlStringable || $item instanceof IHtmlString) {
                $output .= $item;
            } else {
                $output .= htmlspecialchars((string)$item, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE);
            }
        }

        return $output;
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
     * Escapes string for use inside HTML attribute `href` or `src` which contains URL string.
     * @param string|mixed $data
     * @return string
     */
    public static function htmlHref($data): string
    {
        return self::htmlAttr(self::safeUrl($data));
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
     * Escapes string for use everywhere inside XML (except for comments).
     * @param string|mixed $data
     * @return string XML
     *
     * @link https://api.nette.org/2.4/source-Latte.Runtime.Filters.php.html#_escapeXml
     */
    public static function xml($data): string
    {
        $data = (string)$data;
        // XML 1.0: \x09 \x0A \x0D and C1 allowed directly, C0 forbidden
        // XML 1.1: \x00 forbidden directly and as a character reference,
        //   \x09 \x0A \x0D \x85 allowed directly, C0, C1 and \x7F allowed as character references
        $data = preg_replace('#[\x00-\x08\x0B\x0C\x0E-\x1F]#', "\u{FFFD}", $data);
        return htmlspecialchars($data, ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Escapes string for use inside JS code.
     * @param string|HtmlStringable|IHtmlString|mixed $data
     * @return string
     *
     * @link https://api.nette.org/2.4/source-Latte.Runtime.Filters.php.html#_escapeJs
     */
    public static function js($data): string
    {
        if ($data instanceof HtmlStringable || $data instanceof IHtmlString) {
            $data = (string)$data;
        }

        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);

        if ($json === false) {
            throw new RuntimeException("JSON encode failed: " . json_last_error_msg(), json_last_error());
        }

        return str_replace([']]>', '<!', '</'], [']]\u003E', '\u003C!', '<\/'], $json);
    }

    /**
     * Escapes string for use inside CSS code.
     * @param string|mixed $data
     * @return string
     *
     * @link https://api.nette.org/2.4/source-Latte.Runtime.Filters.php.html#_escapeCss
     */
    public static function css($data): string
    {
        $data = (string)$data;
        // http://www.w3.org/TR/2006/WD-CSS21-20060411/syndata.html#q6
        return addcslashes($data, "\x00..\x1F!\"#$%&'()*+,./:;<=>?@[\\]^`{|}~");
    }

    /**
     * Escapes string for use inside URL.
     * @param string|mixed $data
     * @return string
     */
    public static function url($data): string
    {
        $data = (string)$data;
        return urlencode($data);
    }

    /**
     * Sanitizes string for use inside href attribute.
     * @param string|mixed $data
     * @param bool $warning
     * @return string
     *
     * @link https://api.nette.org/2.4/source-Latte.Runtime.Filters.php.html#_safeUrl
     */
    public static function safeUrl($data, bool $warning = false):string
    {
        if (preg_match('~^(?:(?:https?|ftp)://[^@]+(?:/.*)?|(?:mailto|tel|sms):.+|[/?#].*|[^:]+)$~Di', (string)$data)) {
            return (string)$data;
        }

        if($warning) {
            trigger_error('URL was removed because is invalid or unsafe: ' . $data, E_USER_WARNING);
        }

        return '';
    }

    /**
     * Just returns argument as is without any escaping.
     * Method is useful to mark code as intentionally unescaped as opposed to simple neglected.
     * @param string|mixed $data
     * @return string
     */
    public static function noescape($data): string
    {
        return (string)$data;
    }
}
