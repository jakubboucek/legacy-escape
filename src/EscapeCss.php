<?php

// Intentionally no strict – package is most used to secure legacy projects
// declare(strict_types=1);

namespace JakubBoucek\Escape;

class EscapeCss
{
    /**
     * Checks if color value is safe format of known methods
     * (keyword, hexadecimal, `rgb()`, `rgba()`, hsl()`, hsla()`, `lch()`, `lab()`)
     * otherwise return empty string.
     *
     * The `color()` format is not allowed because it's too relative to context, here is assumption the user's value
     * with `color()` format is not desirable.
     *
     * Valid inputs:
     *  - `darkblue`
     *  - `#000`
     *  - `#00008b`
     *  - `#00008bff`
     *  - `#00008BFF`
     *  - `rgb(0,0,139)`
     *  - `rgba(0, 0, 139, 0.8)`
     *  - `rgba(0, 0, 139 / 0.8)`
     *  - `hsl(240,100%,27%)`
     *  - `hsla( 240, 100%, 27%, 0.8)`
     *  - `hsla( 240, 100%, 27% / 0.8)`
     *  - `ActiveText`
     *  - `lab(29.2345% 39.3825 20.0664);`
     *  - `lab(52.2345% 40.1645 59.9971 / .5);`
     *  - `lch(52.2345% 72.2 56.2);`
     *  - `lch(52.2345% 72.2 56.2 / .5);`
     *
     * Invalid inputs:
     *  - `#000; display:none`
     *  - `black</style><script>alert(1)</script>`
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/CSS/color_value#color_keywords
     */
    public static function color(string $color): string
    {
        $result = preg_match(
            '/^\s*(?:[-a-zA-Z]+|#[\da-fA-F]{3,8}|(?:rgba?|hsla?|lch|lab)\([\d,.%\\/ ]+\))\s*$/D',
            $color
        );

        if ($result !== 1) {
            return '';
        }

        return trim($color);
    }
}
