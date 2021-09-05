# Escape

PHP library to right escape outputs in your legacy project.

Don't use package for new projects, use [Latte](https://latte.nette.org/) instead.

Package is substrate of [Latte package](https://github.com/nette/latte/)
[filters](https://github.com/nette/latte/blob/master/src/Latte/Runtime/Filters.php).

## Features

- Escape HTML
- Escape HTML attributes
- Escape HTML comments
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
echo 'Registered user: ' . \JakubBoucek\Escape\Escape::html($username);
```

## CSS specifics

In few cases you cannot use `\JakubBoucek\Escape\Escape::css($cssColor)` to escape
some known format, because standard escaping is broke CSS format. Class `EscapeCss` has prepared
limited set of known propetries with specefics format:

### `color` property

Sanitize value od CSS `color` property to safe format, example:

```php
echo '<style>color: ' . \JakubBoucek\Escape\EscapeCss::color($cssColor) . ';</style>';
```

It's prevent attact by escaping color value context.

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
