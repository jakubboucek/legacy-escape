# Escape

PHP library to right escape outputs in your legacy project.

Don't use package for new projects, use [Latte](https://latte.nette.org/) instead.

Package is substrate of [Latte package](https://github.com/nette/latte/)
[filters](https://github.com/nette/latte/blob/master/src/Latte/Runtime/Filters.php).

## Features

- Escape HTML
- Escape HTML attributes
- Encode JSON for HTML attributes
- Escape HTML href attributes
- Escape HTML comments
- Escape XML
- Escape JS
- Escape URL
- Escape CSS
- Escape CSS specifics for few properties: 
    - `color` value

## Install

```shell
composer require jakubboucek/legacy-escape
```

## Usage

Instead:
```php
echo 'Registered user: ' . $username;
```

Use:
```php
use JakubBoucek\Escape\Escape;

echo 'Registered user: ' . Escape::html($username);
```

You can also use an alias:
```php
use JakubBoucek\Escape\Escape as E;

echo 'Registered user: ' . E::html($username);
```

### Attributes and JavaScript

```php
echo '<a href="' . Escape::htmlHref($url) . '">' . Escape::html($label) . '</a>';
echo '<div data-options="' . Escape::htmlJsonAttr($options) . '"></div>';
echo '<script>const options = ' . Escape::js($options) . ';</script>';
```

`js()` returns a complete JSON literal; do not wrap it in additional JavaScript quotes. `url()` encodes individual URL components, while `htmlHref()` filters a URL and escapes it for an HTML attribute.

## CSS specifics

`Escape::css()` escapes punctuation that some CSS values need. `EscapeCss` provides a dedicated filter for color values.

### `color` property

Filter a CSS color value:

```php
use JakubBoucek\Escape\EscapeCss;

echo '<style>.label { color: ' . EscapeCss::color($cssColor) . '; }</style>';
```

The filter returns an empty string for rejected input. It checks a restricted pattern, not full CSS validity; see the [API reference](docs/api.md).

## Safe HTML content

With the optional `nette/utils` package installed, `html()` can combine trusted Nette HTML objects with escaped text. It supports both `Nette\HtmlStringable` and `Nette\Utils\IHtmlString`.

Usage:
```php
use JakubBoucek\Escape\Escape;
use Nette\Utils\Html; 

$avatarUrl = 'https://example.com/avatar.png';
$username = 'John Doe <script>hack</script>';

$avatarImage = Html::el('img')->src($avatarUrl)->width(16);
echo Escape::html($avatarImage, ' ', $username);

// <img src="https://example.com/avatar.png" width="16"> John Doe &lt;script&gt;hack&lt;/script&gt;
```

## Output without any escaping

In some cases you intentionally want to output variable without any escaping, but somebody other or your future self may
mistakenly believe you forgot to escape it. Here you can use `noescape()` method to mark code as intentionally unescaped. 

```php
echo \JakubBoucek\Escape\Escape::noescape($htmlContent);
```

## FAQ

### Is it support for escaping SQL query?

No, SQL requires access to active SQL connection to right escape. This package is only allows to escape contexts without
external requirements.

## Contributing
Please don't hesitate send Issue or Pull Request.

## Security
If you discover any security related issues, please email pan@jakubboucek.cz instead of using the issue tracker.

## License
The MIT License (MIT). Please see [License File](LICENSE) for more information.

### Origin code licences
- [New BSD License](https://github.com/nette/latte/blob/master/license.md#new-bsd-license)
- [GNU General Public License](https://github.com/nette/latte/blob/master/license.md#gnu-general-public-license)

Copyright (c) 2004, 2014 David Grudl (https://davidgrudl.com) All rights reserved.
Please see [License File](https://github.com/nette/latte/blob/master/license.md) for more information.
