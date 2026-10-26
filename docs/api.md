# API reference

All methods return strings. Text escaping assumes UTF-8. Choose the method for the output context; attribute values must be quoted.

## `JakubBoucek\Escape\Escape`

| Method | Behavior |
| --- | --- |
| `html(...$data)` | Concatenates arguments, escaping ordinary values as HTML text. Preserves Nette HTML objects. Escapes `{` and breaks `{{` with an HTML comment. |
| `htmlAttr($data)` | Casts to string and escapes quotes, HTML characters, and `{` for a quoted HTML attribute. HTML objects are escaped too. |
| `htmlJsonAttr($data)` | JSON-encodes a value, then applies `htmlAttr()`. Intended for quoted data attributes. Substitutes malformed UTF-8 and throws `RuntimeException` for encoding failures such as `NAN`. |
| `htmlHref($data)` | Applies `safeUrl()` followed by `htmlAttr()` for a quoted `href` or `src` value. |
| `htmlComment($data)` | Adds spacing around problematic leading characters, double hyphens, and a trailing hyphen in HTML comment content. |
| `xml($data)` | Replaces forbidden XML 1.0 control characters and escapes XML text or quoted attribute content. Not for XML comments. |
| `js($data)` | Produces a JSON literal for a JavaScript value, preserving zero fractions and escaping `]]>`, `<!`, and `</`. Throws `RuntimeException` on JSON encoding failure, including malformed UTF-8. |
| `css($data)` | Backslash-escapes control characters and a fixed punctuation set using `addcslashes()`. Does not parse CSS or validate a whole declaration. |
| `url($data)` | Applies `urlencode()` to a URL component; spaces become `+`. Does not filter URL schemes or preserve a complete URL. |
| `safeUrl($data, bool $warning = false)` | Returns the original string when its URL pattern matches, otherwise `''`. Optionally emits `E_USER_WARNING` for rejected input. Does not escape HTML. |
| `noescape($data)` | Casts to string without escaping, marking intentionally raw output. |

`safeUrl()` accepts patterns for HTTP, HTTPS, FTP, `mailto:`, `tel:`, `sms:`, strings starting with `/`, `?`, or `#`, and nonempty strings without a colon. Scheme matching is case-insensitive. This is a pattern filter, not a complete URL parser or a destination allowlist; protocol-relative URLs are accepted.

Only `html()` treats `Nette\HtmlStringable` and `Nette\Utils\IHtmlString` objects as trusted HTML. `js()` converts those objects to strings before JSON encoding. Trusted HTML and `noescape()` do not sanitize their contents.

`html()`, `htmlAttr()`, and `xml()` substitute malformed UTF-8. Already escaped HTML entities are escaped again. Other helpers do not share a universal UTF-8 normalization step.

## `JakubBoucek\Escape\EscapeCss`

`color(string $color)` returns trimmed input matching its pattern, otherwise `''`. It accepts letters/hyphens, `#` followed by 3–8 hex digits, and lowercase `rgb()`, `rgba()`, `hsl()`, `hsla()`, `lch()`, or `lab()` with digits, commas, periods, percent signs, slashes, and spaces inside.

This is a restricted character-pattern filter, not CSS semantic validation: it does not check keyword names, numeric ranges, argument counts, or valid hex lengths within that range. It rejects `color()`, negative numeric components, and declaration separators.
