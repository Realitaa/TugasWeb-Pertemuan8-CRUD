<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function testEFunctionEscapesHtmlEntities(): void
    {
        $input = '<script>alert("XSS & danger")</script>';
        $expected = '&lt;script&gt;alert(&quot;XSS &amp; danger&quot;)&lt;/script&gt;';

        $this->assertSame($expected, e($input));
    }

    public function testEFunctionEscapesSingleQuotes(): void
    {
        $input = "O'Connor & Sons";
        $expected = "O&#039;Connor &amp; Sons";

        $this->assertSame($expected, e($input));
    }

    public function testEFunctionHandlesNull(): void
    {
        $this->assertSame('', e(null));
    }

    public function testEFunctionHandlesNumbers(): void
    {
        $this->assertSame('12345', e(12345));
        $this->assertSame('99.9', e(99.9));
    }

    public function testEFunctionDoubleEncode(): void
    {
        $input = '&amp;';
        $this->assertSame('&amp;amp;', e($input, true));
        $this->assertSame('&amp;', e($input, false));
    }

    public function testFormatRupiahHelper(): void
    {
        $this->assertSame('Rp 150.000', format_rupiah(150000));
        $this->assertSame('Rp 0', format_rupiah(0));
    }
}
