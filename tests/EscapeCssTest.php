<?php

use JakubBoucek\Escape\Escape;
use JakubBoucek\Escape\EscapeCss;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;

require __DIR__ . '/../vendor/autoload.php';

Environment::setup();

/** @testCase */
class EscapeCssTest extends TestCase
{
    public function getCssColorArgs(): array
    {
        return [
            ['darkblue', 'darkblue'],
            ['#000', '#000'],
            ['#00008b', '#00008b'],
            ['#00008bff', '#00008bff'],
            ['#00008BFF', '#00008BFF'],
            ['rgb(0,0,139)', 'rgb(0,0,139)'],
            ['rgba(0, 0, 139, 0.8)', 'rgba(0, 0, 139, 0.8)'],
            ['rgba(0, 0, 139 / 0.8)', 'rgba(0, 0, 139 / 0.8)'],
            ['hsl(240,100%,27%)', 'hsl(240,100%,27%)'],
            ['hsla( 240, 100%, 27%, 0.8)', 'hsla( 240, 100%, 27%, 0.8)'],
            ['hsla( 240, 100%, 27% / 0.8)', 'hsla( 240, 100%, 27% / 0.8)'],
            ['ActiveText', 'ActiveText'],
            ['lab(29.2345% 39.3825 20.0664)', 'lab(29.2345% 39.3825 20.0664)'],
            ['lab(52.2345% 40.1645 59.9971 / .5)', 'lab(52.2345% 40.1645 59.9971 / .5)'],
            ['lch(52.2345% 72.2 56.2)', 'lch(52.2345% 72.2 56.2)'],
            ['lch(52.2345% 72.2 56.2 / .5)', 'lch(52.2345% 72.2 56.2 / .5)'],
            [' darkblue', 'darkblue'],
            ["\tdarkblue", 'darkblue'],
            ["\ndarkblue", 'darkblue'],
            ["\r\ndarkblue", 'darkblue'],
            ['darkblue ', 'darkblue'],
            ["darkblue\t", 'darkblue'],
            ["darkblue\n", 'darkblue'],
            ["darkblue\r\n", 'darkblue'],
            ["#000; display:none", ''],
            ["black</style><script>alert(1)</script>", ''],
        ];
    }

    /**
     * @dataProvider getCssColorArgs
     */
    public function testCssColor(string $data, string $expected): void
    {
        Assert::same($expected, EscapeCss::color($data));
    }
}

(new EscapeCssTest())->run();
