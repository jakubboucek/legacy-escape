<?php

use JakubBoucek\Escape\Escape;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;

require __DIR__ . '/../vendor/autoload.php';

Environment::setup();

/** @testCase */
class EscapeTest extends TestCase
{
    public function getHtmlArgs(): array
    {
        return [
            ['', null],
            ['', ''],
            ['1', 1],
            ['string', 'string'],
            ['&lt;br&gt;', '<br>'],
            ['&lt; &amp; &apos; &quot; &gt;', '< & \' " >'],
            ['&amp;quot;', '&quot;'],
            ['`hello', '`hello'],
            ["foo \u{FFFD} bar", "foo \u{D800} bar"], // invalid codepoint high surrogates
            ["foo \u{FFFD}&quot; bar", "foo \xE3\x80\x22 bar"], // stripped UTF
            ['Hello World', 'Hello World'],
            ['Hello &lt;World&gt;', 'Hello <World>'],
            ['&quot; &apos; &lt; &gt; &amp; �', "\" ' < > & \x8F"],
            ['`hello`', '`hello`'],
            ['` &lt;br&gt; `', '` <br> `'],
        ];
    }

    /**
     * @dataProvider getHtmlArgs
     */
    public function testHtml(string $expected, $data): void
    {
        Assert::same($expected, Escape::html($data));
    }

    public function getHtmlAttrArgs(): array
    {
        return [
            ['', null],
            ['', ''],
            ['1', 1],
            ['string', 'string'],
            ['&lt; &amp; &apos; &quot; &gt;', '< & \' " >'],
            ['&amp;quot;', '&quot;'],
            ['`hello ', '`hello'],
            ['`hello&quot;', '`hello"'],
            ['`hello&apos;', "`hello'"],
            ["foo \u{FFFD} bar", "foo \u{D800} bar"], // invalid codepoint high surrogates
            ["foo \u{FFFD}&quot; bar", "foo \xE3\x80\x22 bar"], // stripped UTF
            ['Hello World', 'Hello World'],
            ['Hello &lt;World&gt;', 'Hello <World>'],
            ['&quot; &apos; &lt; &gt; &amp; �', "\" ' < > & \x8F"],
            ['`hello` ', '`hello`'],
            ['``onmouseover=alert(1) ', '``onmouseover=alert(1)'],
            ['` &lt;br&gt; `', '` <br> `'],
        ];
    }

    /**
     * @dataProvider getHtmlAttrArgs
     */
    public function testHtmlAttr(string $expected, $data): void
    {
        Assert::same($expected, Escape::htmlAttr($data));
    }

    public function getHtmlCommentArgs(): array
    {
        return [
            ['', null],
            ['', ''],
            ['1', 1],
            ['string', 'string'],
            ['< & \' " >', '< & \' " >'],
            ['&quot;', '&quot;'],
            [' - ', '-'],
            [' - - ', '--'],
            [' - - - ', '---'],
            [' >', '>'],
            [' !', '!'],
            ["foo \u{D800} bar", "foo \u{D800} bar"], // invalid codepoint high surrogates
            ["foo \xE3\x80\x22 bar", "foo \xE3\x80\x22 bar"], // stripped UTF
            ['Hello World', 'Hello World'],
            ['Hello <World>', 'Hello <World>'],
            ["\" ' < > & \x8F", "\" ' < > & \x8F"],
            ['`hello`', '`hello`'],
            ['``onmouseover=alert(1)', '``onmouseover=alert(1)'],
            ['` <br> `', '` <br> `'],
        ];
    }

    /**
     * @dataProvider getHtmlCommentArgs
     */
    public function testHtmlComment(string $expected, $data): void
    {
        Assert::same($expected, Escape::htmlComment($data));
    }

    public function getJsArgs(): array
    {
        return [
            ['null', null],
            ['""', ''],
            ['1', 1],
            ['"string"', 'string'],
            ['"<\/tag"', '</tag'],
            ['"\u2028 \u2029 ]]\u003E \u003C!"', "\u{2028} \u{2029} ]]> <!"],
            ['[0,1]', [0, 1]],
            ['["0","1"]', ['0', '1']],
            ['{"a":"0","b":"1"}', ['a' => '0', 'b' => '1']],
            ['"<\\/script>"', '</script>'],
        ];
    }

    /**
     * @dataProvider getJsArgs
     */
    public function testJs(string $expected, $data): void
    {
        Assert::same($expected, Escape::js($data));
    }

    public function getCssArgs(): array
    {
        return [
            ['', null],
            ['', ''],
            ['1', 1],
            ['string', 'string'],
            ['\!\"\#\$\%\&\\\'\(\)\*\+\,\.\/\:\;\<\=\>\?\@\[\\\\\]\^\`\{\|\}\~', '!"#$%&\'()*+,./:;<=>?@[\]^`{|}~'],
            ["foo \u{D800} bar", "foo \u{D800} bar"], // invalid codepoint high surrogates
            ["foo \xE3\x80\\\x22 bar", "foo \xE3\x80\x22 bar"], // stripped UTF
            ['\\<\\/style\\>', '</style>'],
        ];
    }

    /**
     * @dataProvider getCssArgs
     */
    public function testCss(string $expected, $data): void
    {
        Assert::same($expected, Escape::css($data));
    }

    public function getUrlArgs(): array
    {
        return [
            ['', null],
            ['', ''],
            ['1', 1],
            ['string', 'string'],
            ['a%2Fb', 'a/b'],
            ['a%3Fb', 'a?b'],
            ['a%26b', 'a&b'],
            ['a%2Bb', 'a+b'],
            ['a+b', 'a b'],
            ['a%27b', 'a\'b'],
            ['a%22b', 'a"b'],
        ];
    }

    /**
     * @dataProvider getUrlArgs
     */
    public function testUrl(string $expected, $data): void
    {
        Assert::same($expected, Escape::url($data));
    }
}

(new EscapeTest())->run();
