# Escape

PHP library to right escape outputs in your legacy project.

Don't use package for new projects, use [Latte](https://latte.nette.org/) instead.

Package is substrate of [Latte package](https://github.com/nette/latte/)
[filters](https://github.com/nette/latte/blob/master/src/Latte/Runtime/Filters.php).

## Features

- Escape HTML
- Escape HTML attributes
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

You can use shortcut by aliasing too:
```php
use JakubBoucek\Escape\Escape as E;

echo 'Registered user: ' . E::html($username);
```

## CSS specifics

In few cases you cannot use `Escape::css($cssColor)` to escape
some known format, because standard escaping is broke CSS format. Class `EscapeCss` has prepared
limited set of known propetries with specefics format:

### `color` property

Sanitize value od CSS `color` property to safe format, example:

```php
use JakubBoucek\Escape\EscapeCss;

echo '<style>color: ' . EscapeCss::color($cssColor) . ';</style>';
```

It's prevent attact by escaping color value context.

## Safe HTML content

Package supports escaping HTML with included [safe HTML content](https://doc.nette.org/en/3.1/html-elements).

Usage:
```php
use JakubBoucek\Escape\Escape;
use Nette\Utils\Html; 

$avatarUrl = 'http:/example.com/avatar.png';
$username = 'John Doe <script>hack</script>';

$avatarImage = Html::el('img')->src($avatarUrl)->width(16);
echo Escape::html($avatarImage, ' ', $username);

// <img src="http:/example.com/avatar.png" width="16"> John Doe &lt;script&gt;hack&lt;/script&gt;
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
