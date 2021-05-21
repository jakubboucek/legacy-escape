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
- Escape CSS
- Escape URL

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

## FAQ

### Is it support for escaping SQL query?

No, SQL requires access to active SQL connection to right escape. This package is only aloow to escape contexts without
external requrements.

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
