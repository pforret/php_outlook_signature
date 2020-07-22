<?php

namespace Pforret\PhpOutlookSignature\Tests;

use Pforret\PhpOutlookSignature\PhpOutlookSignature;
use PHPUnit\Framework\TestCase;

class SignatureTest extends TestCase
{
    public function test_templates()
    {
        try {
            $sign = new PhpOutlookSignature(__DIR__ . "/templates/empty");
        } catch (\Exception $e) {
        }
        print_r($sign->get_keywords());
    }
}
