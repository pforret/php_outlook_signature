<?php

namespace Pforret\PhpOutlookSignature\Tests;

use Pforret\PhpOutlookSignature\PhpOutlookSignature;
use PHPUnit\Framework\TestCase;

class SignatureTest extends TestCase
{
    public function test_template_not_exist()
    {
        $this->expectExceptionMessage('does not exist');
        $sign = new PhpOutlookSignature(__DIR__.'/templates/does_not_exist');
    }

    public function test_template_empty()
    {
        $this->expectExceptionMessage('does not contain');
        $sign = new PhpOutlookSignature(__DIR__.'/templates/empty');
    }

    public function test_template_no_assets()
    {
        $this->expectExceptionMessage('cannot be found');
        $sign = new PhpOutlookSignature(__DIR__.'/templates/no_assets');
    }

    public function test_template_valid()
    {
        $sign = new PhpOutlookSignature(__DIR__.'/templates/valid');
        $this->assertTrue(isset($sign), 'PhpOutlookSignature not initialised');
        $this->assertTrue(count($sign->get_assets()) > 0, 'Assets not found');
        $this->assertTrue(count($sign->get_keywords()) > 0, 'Keywords not found');
        $this->assertTrue(in_array('language', $sign->get_keywords()), 'Keyword [language] not found');
    }

    public function test_signature_no_values()
    {
        $sign = new PhpOutlookSignature(__DIR__.'/templates/valid');
        $tempdir = sys_get_temp_dir().'/SignatureTest';
        if (! file_exists($tempdir)) {
            mkdir($tempdir);
        }
        $this->assertDirectoryExists($tempdir, 'Cannot create temp folder');
        $params = [];
        $this->expectExceptionMessage('but none was given');
        // Template expects [assets] but none was given
        $sign->create("$tempdir/message.htm", $params);
    }

    public function test_signature_with_values()
    {
        $sign = new PhpOutlookSignature(__DIR__.'/templates/valid');
        $tempdir = sys_get_temp_dir().'/SignatureTest';
        if (! file_exists($tempdir)) {
            mkdir($tempdir);
        }
        $this->assertDirectoryExists($tempdir, 'Cannot create temp folder');
        $params = [
            'language' => 'en-us',
            'person_name' => 'Peter Gibbons',
            'person_function' => 'Programmer',
            'person_phone' => '+1-234-567890',
            'person_fax' => '+1-234-999999',
            'person_mobile' => '+1-666-999999',
            'company' => 'Initech',
            'company_url' => 'www.imdb.com/title/tt0151804/',
            'company_domain' => 'www.initech.con',
        ];
        $sign->create("$tempdir/message.htm", $params);
        $message = file_get_contents("$tempdir/message.htm");
        $this->assertStringContainsString('Peter Gibbons', $message, 'Missing substitution {person_name}');
        unlink("$tempdir/message.htm");
    }
}
