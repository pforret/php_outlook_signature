# Create Outlook email signatures from a template

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pforret/php_outlook_signature.svg?style=flat-square)](https://packagist.org/packages/pforret/php_outlook_signature)
![Tests](https://github.com/pforret/php_outlook_signature/workflows/Tests/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/pforret/php_outlook_signature.svg?style=flat-square)](https://packagist.org/packages/pforret/php_outlook_signature)


Create valid Outlook HTML Signatures, from a template with placeholders. Kind of mail merge for Outlook email signatures.
## Installation

You can install the package via composer:

```bash
composer require pforret/php_outlook_signature
```

## Usage

```php
use Pforret\PhpOutlookSignature\PhpOutlookSignature
$signature = new PhpOutlookSignature("<folder template>");
$personal_details=[
    "person_name"   => "Peter Gibbons",
    ...
];
echo $signature->create($output_file,$personal_details);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

- [Peter Forret](https://github.com/pforret)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
