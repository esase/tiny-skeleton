<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tiny\Skeleton;

use PHPUnit\Framework\TestCase;
use Tiny\Skeleton\Module\Core\Exception;

class ViewTest extends TestCase
{

    public function test__getMethod()
    {
        $view = new View(
            [
                'test' => 'testValue',
            ]
        );

        $this->assertEquals('testValue', $view->test);
    }

    public function test__getMethodUsingNotRegisteredVars()
    {
        $view = new View();

        $this->assertEmpty($view->test);
    }

    public function test_toStringMethod()
    {
        $view = new View(
            [
                'test' => 'test_value',
            ]
        );
        $view->setTemplatePath(__DIR__.'/fixtures/templates/test_template.phtml');
        $view->setLayoutPath(__DIR__.'/fixtures/templates/test_layout.phtml');

        $this->assertEquals(
            __DIR__.'/fixtures/templates/test_template.phtml',
            $view->getTemplatePath()
        );

        $this->assertEquals(
            __DIR__.'/fixtures/templates/test_layout.phtml',
            $view->getLayoutPath()
        );

        $content = $view->__toString();

        $this->assertEquals(
            '<layout><template>test_value</template></layout>',
            $content
        );

    }

    public function test_toStringMethodUsingEmptyLayout()
    {
        $view = new View(
            [
                'test' => 'test_value',
            ]
        );
        $view->setTemplatePath(__DIR__.'/fixtures/templates/test_template.phtml');

        $content = $view->__toString();

        $this->assertEquals(
            '<template>test_value</template>',
            $content
        );
    }

    public function test_toStringMethodUsingEmptyTemplatePath()
    {
        $this->expectException(Exception\UnexpectedValueException::class);
        $this->expectExceptionMessage('Template file path is empty.');

        $view = new View();
        $view->__toString();
    }

}
