<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer
 */
final class BlankLineBeforeStatementFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideConfigureRejectsInvalidControlStatementCases
     *
     * @param mixed $controlStatement
     */
    public function testConfigureRejectsInvalidControlStatement($controlStatement): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);

        $this->fixer->configure([
            'statements' => [$controlStatement],
        ]);
    }

    public static function provideConfigureRejectsInvalidControlStatementCases(): iterable
    {
        return [
            'null' => [null],
            'false' => [false],
            'true' => [true],
            'int' => [0],
            'float' => [3.14],
            'array' => [[]],
            'object' => [new \stdClass()],
            'unknown' => ['foo'],
        ];
    }

    /**
     * @dataProvider provideFixWithReturnCases
     */
    public function testFixWithDefaultConfiguration(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixWithBreakCases
     */
    public function testFixWithBreak(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['break'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithBreakCases(): iterable
    {
        return [
            [
                '<?php
switch ($a) {
    case 42:
        break;
}',
            ],
            [
                '<?php
switch ($a) {
    case 42:
        $foo = $bar;

        break;
}',
                '<?php
switch ($a) {
    case 42:
        $foo = $bar;
        break;
}',
            ],
            [
                '<?php
while (true) {
    if ($foo === $bar) {
        break;
    }
}',
            ],
            [
                '<?php
while (true) {
    if ($foo === $bar) {
        break 1;
    }
}',
            ],
            [
                '<?php
while (true) {
    if ($foo === $bar) {
        echo $baz;

        break;
    }
}',
                '<?php
while (true) {
    if ($foo === $bar) {
        echo $baz;
        break;
    }
}',
            ],
            [
                '<?php
while (true) {
    if ($foo === $bar) {
        echo $baz;

        break 1;
    }
}',
                '<?php
while (true) {
    if ($foo === $bar) {
        echo $baz;
        break 1;
    }
}',
            ],
            [
                '<?php
while (true) {
    if ($foo === $bar) {
        /** X */
        break 1;
    }
}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithCaseCases
     */
    public function testFixWithCase(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['case'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithCaseCases(): iterable
    {
        return [
            [
                '<?php
switch ($a) {
    case 1:
        return 1;

    case 2;
        return 2;

    case 3:
        return 3;
}',
                '<?php
switch ($a) {
    case 1:
        return 1;
    case 2;
        return 2;
    case 3:
        return 3;
}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithContinueCases
     */
    public function testFixWithContinue(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['continue'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithContinueCases(): iterable
    {
        return [
            [
                '<?php
while (true) {
    continue;
}',
            ],
            [
                '<?php
while (true) {
    continue 1;
}',
            ],
            [
                '<?php
while (true) {
    while (true) {
        continue 2;
    }
}',
            ],
            [
                '<?php
while (true) {
    $foo = true;

    continue;
}',
                '<?php
while (true) {
    $foo = true;
    continue;
}',
            ],
            [
                '<?php
while (true) {
    $foo = true;

    continue 1;
}',
                '<?php
while (true) {
    $foo = true;
    continue 1;
}',
            ],
            [
                '<?php
while (true) {
    while (true) {
        switch($a) {
            case 1:
                echo 1;

                continue;
        }
        $foo = true;

        continue 2;
    }
}',
                '<?php
while (true) {
    while (true) {
        switch($a) {
            case 1:
                echo 1;
                continue;
        }
        $foo = true;
        continue 2;
    }
}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithDeclareCases
     */
    public function testFixWithDeclare(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['declare'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithDeclareCases(): iterable
    {
        return [
            [
                '<?php
declare(ticks=1);',
            ],
            [
                '<?php
$foo = "bar";
do {
} while (true);
$foo = "bar";

declare(ticks=1);',
                '<?php
$foo = "bar";
do {
} while (true);
$foo = "bar";
declare(ticks=1);',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithDefaultCases
     */
    public function testFixWithDefault(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['default'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithDefaultCases(): iterable
    {
        return [
            [
                '<?php
switch ($a) {
    case 1:
        return 1;

    default:
        return 2;
}

switch ($a1) {
    default:
        return 22;
}',
                '<?php
switch ($a) {
    case 1:
        return 1;
    default:
        return 2;
}

switch ($a1) {
    default:
        return 22;
}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithDoCases
     */
    public function testFixWithDo(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['do'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithDoCases(): iterable
    {
        return [
            [
                '<?php
do {
} while (true);',
            ],
            [
                '<?php
$foo = "bar";

do {
} while (true);',
                '<?php
$foo = "bar";
do {
} while (true);',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithExitCases
     */
    public function testFixWithExit(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['exit'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithExitCases(): iterable
    {
        return [
            [
                '<?php
if ($foo === $bar) {
    exit();
}',
            ],
            [
                '<?php
if ($foo === $bar) {
    echo $baz;

    exit();
}',
                '<?php
if ($foo === $bar) {
    echo $baz;
    exit();
}',
            ],
            [
                '<?php
if ($foo === $bar) {
    echo $baz;

    exit();
}',
            ],
            [
                '<?php
mysqli_connect() or exit();',
            ],
            [
                '<?php
if ($foo === $bar) {
    $bar = 9001;
    mysqli_connect() or exit();
}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithForCases
     */
    public function testFixWithFor(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['for'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithForCases(): iterable
    {
        return [
            [
                '<?php
                    echo 1;

                    for(;;){break;}
                ',
                '<?php
                    echo 1;
                    for(;;){break;}
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithGotoCases
     */
    public function testFixWithGoto(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['goto'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithGotoCases(): iterable
    {
        return [
            [
                '<?php
a:

if ($foo === $bar) {
    goto a;
}',
            ],
            [
                '<?php
a:

if ($foo === $bar) {
    echo $baz;

    goto a;
}',
                '<?php
a:

if ($foo === $bar) {
    echo $baz;
    goto a;
}',
            ],
            [
                '<?php
a:

if ($foo === $bar) {
    echo $baz;

    goto a;
}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithIfCases
     */
    public function testFixWithIf(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['if'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithIfCases(): iterable
    {
        return [
            [
                '<?php if (true) {
    echo $bar;
}',
            ],
            [
                '<?php
if (true) {
    echo $bar;
}',
            ],
            [
                '<?php
$foo = $bar;

if (true) {
    echo $bar;
}',
                '<?php
$foo = $bar;
if (true) {
    echo $bar;
}',
            ],
            [
                '<?php
// foo
if ($foo) { }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithForEachCases
     */
    public function testFixWithForEach(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['foreach'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithForEachCases(): iterable
    {
        return [
            [
                '<?php
                    echo 1;

                    foreach($a as $b){break;}
                ',
                '<?php
                    echo 1;
                    foreach($a as $b){break;}
                ',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithIncludeCases
     */
    public function testFixWithInclude(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['include'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithIncludeCases(): iterable
    {
        return [
            [
                '<?php
include "foo.php";',
            ],
            [
                '<?php
$foo = $bar;

include "foo.php";',
                '<?php
$foo = $bar;
include "foo.php";',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithIncludeOnceCases
     */
    public function testFixWithIncludeOnce(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['include_once'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithIncludeOnceCases(): iterable
    {
        return [
            [
                '<?php
include_once "foo.php";',
            ],
            [
                '<?php
$foo = $bar;

include_once "foo.php";',
                '<?php
$foo = $bar;
include_once "foo.php";',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithRequireCases
     */
    public function testFixWithRequire(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['require'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithRequireCases(): iterable
    {
        return [
            [
                '<?php
require "foo.php";',
            ],
            [
                '<?php
$foo = $bar;

require "foo.php";',
                '<?php
$foo = $bar;
require "foo.php";',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithRequireOnceCases
     */
    public function testFixWithRequireOnce(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['require_once'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithRequireOnceCases(): iterable
    {
        return [
            [
                '<?php
require_once "foo.php";',
            ],
            [
                '<?php
$foo = $bar;

require_once "foo.php";',
                '<?php
$foo = $bar;
require_once "foo.php";',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithReturnCases
     */
    public function testFixWithReturn(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['return'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithReturnCases(): iterable
    {
        return [
            [
                '<?php
if ($a) { /* 1 */ /* 2 */ /* 3 */ // something about $a
    return $b;
}
',
            ],
            [
                '<?php
if ($a) { // something about $a
    return $b;
}
',
            ],
            [
                '
$a = $a;
return $a;',
            ],
            [
                '<?php
$a = $a;

return $a;',
                '<?php
$a = $a; return $a;',
            ],
            [
                '<?php
$b = $b;

return $b;',
                '<?php
$b = $b;return $b;',
            ],
            [
                '<?php
$c = $c;

return $c;',
                '<?php
$c = $c;
return $c;',
            ],
            [
                '<?php
$d = $d;

return $d;',
                '<?php
$d = $d;
return $d;',
            ],
            [
                '<?php
if (true) {
    return 1;
}',
            ],
            [
                '<?php
if (true)
    return 1;',
            ],
            [
                '<?php
if (true) {
    return 1;
} else {
    return 2;
}',
            ],
            [
                '<?php
if (true)
    return 1;
else
    return 2;',
            ],
            [
                '<?php
if (true) {
    return 1;
} elseif (false) {
    return 2;
}',
            ],
            [
                '<?php
if (true)
    return 1;
elseif (false)
    return 2;',
            ],
            [
                '<?php
throw new Exception("return true; //.");',
            ],
            [
                '<?php
function foo()
{
    // comment
    return "foo";
}',
            ],
            [
                '<?php
function foo()
{
    // comment

    return "bar";
}',
            ],
            [
                '<?php

function foo()
{
    switch ($foo) {
        case 2: // comment
            return 1;
    }
}',
            ],
            'do not fix when there is empty line between statement and preceding comment' => [
                '<?php function foo()
                {
                    bar();
                    // comment

                    return 42;
                }',
            ],
            'do not fix when there is empty line between preceding comments' => [
                '<?php function foo()
                {
                    bar();
                    // comment1
                    // comment2

                    // comment3
                    return 42;
                }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithReturnAndMessyWhitespacesCases
     */
    public function testFixWithReturnAndMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public static function provideFixWithReturnAndMessyWhitespacesCases(): iterable
    {
        return [
            [
                "<?php\r\n\$a = \$a;\r\n\r\nreturn \$a;",
                "<?php\r\n\$a = \$a; return \$a;",
            ],
            [
                "<?php\r\n\$b = \$b;\r\n\r\nreturn \$b;",
                "<?php\r\n\$b = \$b;return \$b;",
            ],
            [
                "<?php\r\n\$c = \$c;\r\n\r\nreturn \$c;",
                "<?php\r\n\$c = \$c;\r\nreturn \$c;",
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithSwitchCases
     */
    public function testFixWithSwitch(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['switch'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithSwitchCases(): iterable
    {
        return [
            [
                '<?php
switch ($a) {
    case 42:
        break;
}',
            ],
            [
                '<?php
$foo = $bar;

switch ($foo) {
    case $bar:
        break;
}',
                '<?php
$foo = $bar;
switch ($foo) {
    case $bar:
        break;
}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithThrowCases
     */
    public function testFixWithThrow(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['throw'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithThrowCases(): iterable
    {
        return [
            [
                '<?php
if (false) {
    throw new \Exception("Something unexpected happened.");
}',
            ],
            [
                '<?php
if (false) {
    $log->error("No");

    throw new \Exception("Something unexpected happened.");
}',
                '<?php
if (false) {
    $log->error("No");
    throw new \Exception("Something unexpected happened.");
}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithTryCases
     */
    public function testFixWithTry(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['try'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithTryCases(): iterable
    {
        return [
            [
                '<?php
try {
    $transaction->commit();
} catch (\Exception $exception) {
    $transaction->rollback();
}',
            ],
            [
                '<?php
$foo = $bar;

try {
    $transaction->commit();
} catch (\Exception $exception) {
    $transaction->rollback();
}',
                '<?php
$foo = $bar;
try {
    $transaction->commit();
} catch (\Exception $exception) {
    $transaction->rollback();
}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithWhileCases
     */
    public function testFixWithWhile(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['while'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithWhileCases(): iterable
    {
        return [
            [
                '<?php
while (true) {
    $worker->work();
}',
            ],
            [
                '<?php
$foo = $bar;

while (true) {
    $worker->work();
}',
                '<?php
$foo = $bar;
while (true) {
    $worker->work();
}',
            ],
            [
                '<?php
$foo = $bar;

do {
    echo 1;

    while($a());
    $worker->work();
} while (true);',
                '<?php
$foo = $bar;

do {
    echo 1;
    while($a());
    $worker->work();
} while (true);',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithYieldCases
     */
    public function testFixWithYield(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['yield'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @yield array
     */
    public static function provideFixWithYieldCases(): iterable
    {
        return [
            [
                '<?php
function foo() {
yield $a; /* a *//* b */     /* c */       /* d *//* e *//* etc */
   '.'
yield $b;
}',
                '<?php
function foo() {
yield $a; /* a *//* b */     /* c */       /* d *//* e *//* etc */   '.'
yield $b;
}',
            ],
            [
                '<?php
function foo() {
    yield $a; // test

    yield $b; // test /* A */

    yield $c;

    yield $d;

yield $e;#

yield $f;

    /* @var int $g */
    yield $g;

/* @var int $h */
yield $i;

yield $j;
}',
                '<?php
function foo() {
    yield $a; // test
    yield $b; // test /* A */
    yield $c;
    yield $d;yield $e;#
yield $f;
    /* @var int $g */
    yield $g;
/* @var int $h */
yield $i;
yield $j;
}',
            ],
            [
                '<?php
function foo() {
    yield $a;
}',
            ],
            [
                '<?php
function foo() {
    yield $a;

    yield $b;
}',
                '<?php
function foo() {
    yield $a;
    yield $b;
}',
            ],
            [
                '<?php
function foo() {
    yield \'b\' => $a;

    yield "a" => $b;
}',
                '<?php
function foo() {
    yield \'b\' => $a;
    yield "a" => $b;
}',
            ],
            [
                '<?php
function foo() {
    $a = $a;

    yield $a;
}',
            ],
            [
                '<?php
function foo() {
    $a = $a;

    yield $a;
}',
                '<?php
function foo() {
    $a = $a;
    yield $a;
}',
            ],
            [
                '<?php function foo() {
                    // yield 1
                    yield 1;

                    // yield 2
                    yield 2;
                }',
                '<?php function foo() {
                    // yield 1
                    yield 1;
                    // yield 2
                    yield 2;
                }',
            ],
            [
                '<?php function foo() {
                    yield 1;

                    // yield 2
                    // or maybe yield 3
                    // better compromise
                    yield 2.5;
                }',
                '<?php function foo() {
                    yield 1;
                    // yield 2
                    // or maybe yield 3
                    // better compromise
                    yield 2.5;
                }',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithYieldFromCases
     */
    public function testFixWithYieldFrom(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['yield_from'],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @yield array
     */
    public static function provideFixWithYieldFromCases(): iterable
    {
        return [
            [
                '<?php
function foo() {
    yield from $a;
}',
            ],
            [
                '<?php
function foo() {
    yield from $a;

    yield from $b;
}',
                '<?php
function foo() {
    yield from $a;
    yield from $b;
}',
            ],
            [
                '<?php
function foo() {
    $a = $a;

    yield from $a;

    yield $a;
    yield $b;
}',
            ],
            [
                '<?php
function foo() {
    $a = $a;

    yield from $a;
}',
                '<?php
function foo() {
    $a = $a;
    yield from $a;
}',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithMultipleConfigStatementsCases
     *
     * @param string[] $statements
     */
    public function testFixWithMultipleConfigStatements(array $statements, string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['statements' => $statements]);
        $this->doTest($expected, $input);
    }

    public static function provideFixWithMultipleConfigStatementsCases(): iterable
    {
        $statementsWithoutCaseOrDefault = [
            'break',
            'continue',
            'declare',
            'do',
            'for',
            'foreach',
            'if',
            'include',
            'include_once',
            'require',
            'require_once',
            'return',
            'switch',
            'throw',
            'try',
            'while',
        ];

        $allStatements = array_merge($statementsWithoutCaseOrDefault, ['case', 'default']);

        return [
            [
                $statementsWithoutCaseOrDefault,
                '<?php
                    while($a) {
                        if ($c) {
                            switch ($d) {
                                case $e:
                                    continue 2;
                                case 4:
                                    break;
                                case 5:
                                    return 1;
                                default:
                                    return 0;
                            }
                        }
                    }
                ',
            ],
            [
                $allStatements,
                '<?php
                    while($a) {
                        if ($c) {
                            switch ($d) {
                                case $e:
                                    continue 2;

                                case 4:
                                    break;

                                case 5:
                                    return 1;

                                default:
                                    return 0;
                            }
                        }
                    }
                ',
            ],
            [
                ['break', 'throw'],
                '<?php
do {
    echo 0;
    if ($a) {
        echo 1;

        break;
    }
    echo 2;

    throw $f;
} while(true);',
                '<?php
do {
    echo 0;
    if ($a) {
        echo 1;
        break;
    }
    echo 2;
    throw $f;
} while(true);',
            ],
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['default'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'match' => [
            '<?php
                match ($foo) {
                    1 => "a",
                    default => "b"
                };

                match ($foo) {
                    1 => "a1",


                    default => "b2"
                };
            ',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['case'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'enum' => [
            '<?php
enum Suit {
    case Hearts;

    case Diamonds;

    case Clubs;


    case Spades;
}

enum UserStatus: string {
    case Pending = "P";

    case Active = "A";

    public function label(): string {
        switch ($a) {
            case 1:
                return 1;

            case 2:
                return 2; // do fix
        }

        return "label";
    }
}
',
            '<?php
enum Suit {
    case Hearts;
    case Diamonds;
    case Clubs;


    case Spades;
}

enum UserStatus: string {
    case Pending = "P";
    case Active = "A";

    public function label(): string {
        switch ($a) {
            case 1:
                return 1;
            case 2:
                return 2; // do fix
        }

        return "label";
    }
}
',
        ];
    }

    /**
     * @dataProvider provideFixWithDocCommentCases
     */
    public function testFixWithDocComment(string $expected, string $input = null): void
    {
        $this->fixer->configure([
            'statements' => ['phpdoc'],
        ]);

        $this->doTest($expected, $input);
    }

    public static function provideFixWithDocCommentCases(): iterable
    {
        yield [
            '<?php
/** @var int $foo */
$foo = 123;

/** @var float $bar */
$bar = 45.6;

/** @var string */
$baz = "789";
',
            '<?php
/** @var int $foo */
$foo = 123;
/** @var float $bar */
$bar = 45.6;
/** @var string */
$baz = "789";
',
        ];

        yield [
            '<?php
/* header */

/**
 * Class description
 */
class Foo {
    /** test */
    public function bar() {}
}
',
        ];
    }
}
