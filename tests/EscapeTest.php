<?php

declare(strict_types=1);

use JakubBoucek\Escape\Escape;
use Nette\Utils\Html;
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
            ['', []],
            ['', [null]],
            ['', ['']],
            ['1', [1]],
            ['string', ['string']],
            ['&lt;br&gt;', ['<br>']],
            ['&lt; &amp; &apos; &quot; &gt;', ['< & \' " >']],
            ['&amp;quot;', ['&quot;']],
            ['`hello', ['`hello']],
            ["foo \u{FFFD} bar", ["foo \u{D800} bar"]], // invalid codepoint high surrogates
            ["foo \u{FFFD}&quot; bar", ["foo \xE3\x80\x22 bar"]], // stripped UTF
            ['Hello World', ['Hello World']],
            ['Hello &lt;World&gt;', ['Hello <World>']],
            ['Hello World', [Html::fromText('Hello World')]],
            ['Hello &lt;World&gt;', [Html::fromText('Hello <World>')]],
            ['&quot; &apos; &lt; &gt; &amp; �', ["\" ' < > & \x8F"]],
            ['`hello`', ['`hello`']],
            ['` &lt;br&gt; `', ['` <br> `']],
            ['Foo<br>bar', [Html::fromHtml('Foo<br>bar')]],
            ['Foo&lt;br&gt;bar', [Html::fromText('Foo<br>bar')]],
            ['Hello &lt;World&gt;Hello &lt;World&gt;', ['Hello <World>', 'Hello <World>']],
            ['Hello &lt;World&gt;Hello <World>', ['Hello <World>', Html::fromHtml('Hello <World>')]],
            ['Hello <World>Hello &lt;World&gt;', [Html::fromHtml('Hello <World>'), 'Hello <World>']],
            ['Hello <World>Hello <World>', [Html::fromHtml('Hello <World>'), Html::fromHtml('Hello <World>')]],
        ];
    }

    /**
     * @param array<string> $data
     * @dataProvider getHtmlArgs
     */
    public function testHtml(string $expected, array $data): void
    {
        Assert::same($expected, Escape::html(...$data));
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
            ['Foo&lt;br&gt;bar', Html::fromHtml('Foo<br>bar')]
        ];
    }

    /**
     * @dataProvider getHtmlAttrArgs
     */
    public function testHtmlAttr(string $expected, $data): void
    {
        Assert::same($expected, Escape::htmlAttr($data));
    }

    public function getHtmlHrefArgs(): array
    {
        return [
            ['', ''],
            ['http://example.com/foo/bar.txt?par=var', 'http://example.com/foo/bar.txt?par=var'],
            ['', 'javascript:alert(1)'],
        ];
    }

    /**
     * @dataProvider getHtmlHrefArgs
     */
    public function testHtmlHref(string $expected, $data): void
    {
        Assert::same($expected, Escape::htmlHref($data));
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
            ['Foo<br>bar', Html::fromHtml('Foo<br>bar')]
        ];
    }

    /**
     * @dataProvider getHtmlCommentArgs
     */
    public function testHtmlComment(string $expected, $data): void
    {
        Assert::same($expected, Escape::htmlComment($data));
    }

    public function getXmlArgs(): array
    {
        return [

            ['', null],
            ['', ''],
            ['1', 1],
            ['string', 'string'],
            ['&lt; &amp; &apos; &quot; &gt;', '< & \' " >'],
            ['&lt;br&gt;', Html::fromHtml('<br>')],
            [
                "\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\x09\x0a\u{FFFD}\u{FFFD}\x0d\u{FFFD}\u{FFFD}",
                "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f"
            ],
            [
                "\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}\u{FFFD}",
                "\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f"
            ],
            // invalid UTF-8
            ["foo \u{FFFD} bar", "foo \u{D800} bar"], // invalid codepoint high surrogates
            ["foo \u{FFFD}&quot; bar", "foo \xE3\x80\x22 bar"], // stripped UTF
            ['&amp;quot;', '&quot;'],
            ['`hello', '`hello'],
            ['Hello &lt;World&gt;', 'Hello <World>'],
            ['` &lt;br&gt; `', '` <br> `'],
            ['Foo&lt;br&gt;bar', Html::fromHtml('Foo<br>bar')]
        ];
    }

    /**
     * @dataProvider getXmlArgs
     */
    public function testXml(string $expected, $data): void
    {
        Assert::same($expected, Escape::xml($data));
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
            ['"Foo<br>bar"', Html::fromHtml('Foo<br>bar')]
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
            ['Foo\\<br\\>bar', Html::fromHtml('Foo<br>bar')]

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
            ['Foo%3Cbr%3Ebar', Html::fromHtml('Foo<br>bar')]
        ];
    }

    /**
     * @dataProvider getUrlArgs
     */
    public function testUrl(string $expected, $data): void
    {
        Assert::same($expected, Escape::url($data));
    }

    public function getSafeUrlArgs(): array
    {
        return [
            ['', null],
            ['', ''],
            ['', 'http://'],
            ['http://x', 'http://x'],
            ['http://x:80', 'http://x:80'],
            ['', 'http://nette.org@1572395127'],
            ['https://x', 'https://x'],
            ['ftp://x', 'ftp://x'],
            ['mailto:x', 'mailto:x'],
            ['/', '/'],
            ['/a:b', '/a:b'],
            ['//x', '//x'],
            ['#aa:b', '#aa:b'],
            ['', 'data:'],
            ['', 'javascript:'],
            ['', ' javascript:'],
            ['javascript', 'javascript'],
            ['http://example.com', Html::fromHtml('http://example.com')],
        ];
    }

    /**
     * @dataProvider getSafeUrlArgs
     */
    public function testSafeUrl(string $expected, $data): void
    {
        Assert::same($expected, Escape::safeUrl($data));
    }

    public function getNoescapeArgs(): array
    {
        return [
            ['', null],
            ['', ''],
            ['1', 1],
            ['string', 'string'],
            ['<br>', '<br>'],
            ['< & \' " >', '< & \' " >'],
            ['&quot;', '&quot;'],
            ['`hello', '`hello'],
            ["foo \u{D800} bar", "foo \u{D800} bar"], // invalid codepoint high surrogates
            ["foo \xE3\x80\x22 bar", "foo \xE3\x80\x22 bar"], // stripped UTF
            ['Hello World', 'Hello World'],
            ['Hello <World>', 'Hello <World>'],
            ["\" ' < > & \x8F", "\" ' < > & \x8F"],
            ['`hello`', '`hello`'],
            ['` <br> `', '` <br> `'],
            ['Foo<br>bar', Html::fromHtml('Foo<br>bar')]
        ];
    }

    /**
     * @dataProvider getNoescapeArgs
     */
    public function testNoescape(string $expected, $data): void
    {
        Assert::same($expected, Escape::noescape($data));
    }

}

(new EscapeTest())->run();
