# Create Outlook email signatures from a template

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pforret/php_outlook_signature.svg?style=flat-square)](https://packagist.org/packages/pforret/php_outlook_signature)
[![Tests](https://github.com/pforret/php_outlook_signature/workflows/Tests/badge.svg)](https://github.com/pforret/php_outlook_signature/actions)
[![Quality Score](https://img.shields.io/scrutinizer/g/pforret/php_outlook_signature.svg?style=flat-square)](https://scrutinizer-ci.com/g/pforret/php_outlook_signature)
[![Total Downloads](https://img.shields.io/packagist/dt/pforret/php_outlook_signature.svg?style=flat-square)](https://packagist.org/packages/pforret/php_outlook_signature)


Create valid Outlook HTML Signatures, from a template with placeholders. Kind of mail merge for Outlook email signatures.
## Installation

You can install the package via composer:

```bash
composer require pforret/php_outlook_signature
```

## Usage

```php
use Pforret\PhpOutlookSignature\PhpOutlookSignature;
$signature = new PhpOutlookSignature("<folder template>");
$personal_details=[
    "person_name"   => "Peter Gibbons",
    "person_function"   => "TPS Manager",
    ...
];
echo $signature->create("initech/pgibbons.htm",$personal_details);
// this will create the pgibbons.htm email signature, and copy all required files into pgibbons_files/ subfolder.
// it will also generate a install_signature.cmd script for easy installation of the signature into Outlook (Windows)
```

## Signature template

* let's say you call your template `waffle`
* your template folder should contain 1 HTML file `waffle.htm`
and one asset folder with all the extra files needed (called `waffle_files`)
* in the assets folder there should be a `filelist.xml`. If not, this package will generate one.
* images in the assets folder that are referenced in the template as `src="<assetfolder>/<imagefile>"` 
will be included as (hidden) attachments of the email and always show up for the receiver,
i.e. not be filtered out like external images
* the HTML template can contain `{information}` placeholders. 
They will be replaced by the actual value of `"information" => "..."` from the $personal_details array.
* a template with an `{information}` placeholder that is not specified in the $personal_details array, will throw an error.

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

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
