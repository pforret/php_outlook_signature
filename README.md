# Create Outlook email signatures from a template

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/php_outlook_signature.svg?style=flat-square)](https://packagist.org/packages/spatie/php_outlook_signature)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/spatie/php_outlook_signature/run-tests?label=tests)](https://github.com/spatie/php_outlook_signature/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/php_outlook_signature.svg?style=flat-square)](https://packagist.org/packages/spatie/php_outlook_signature)


Create valid Outlook HTML Signatures, from a template with placeholders. Kind of mail merge for Outlook email signatures.
## Installation

You can install the package via composer:

```bash
composer require pforret/php_outlook_signature
```

## Usage

``` php
$sign = new pforret\PhpOutlookSignature();
echo $sign->create($data,$template);
```

## Testing

``` bash
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
