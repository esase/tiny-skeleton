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
use Tiny\EventManager;

class ViewTest extends TestCase
{

    public function test__callMethodUsingNotInitializedEventManager()
    {
        $method = 'test';

        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The method "%s()" is unsupported.',
                $method
            )
        );

        $view = new View();

        $view->{$method}();
    }

    public function test__callMethodUsingNotRegisteredHelper()
    {
        $method = 'test';

        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The method "%s()" is unsupported.',
                $method
            )
        );

        $view = new View();
        $view->setEventManager(
            $this->createStub(EventManager\EventManager::class)
        );

        $view->{$method}();
    }

    public function test__callMethod()
    {
        $method = 'test';

        $eventManagerMock = $this->createMock(EventManager\EventManager::class);
        $eventManagerMock->expects($this->once())
            ->method('isEventHasSubscribers')
            ->with(
                'view.call.helper.test'
            )
            ->willReturn(true);
        $eventManagerMock->expects($this->once())
            ->method('trigger')
            ->with(
                'view.call.helper.test',
                $this->isInstanceOf(EventManager\Event::class)
            )
            ->will(
                $this->returnCallback(
                    function (string $eventName, EventManager\Event $event) {
                        // make sure that event is stopped for the rest of subscribers
                        $this->assertTrue($event->isStopped());

                        // modify the event's data
                        $event->setData('test content');
                    }
                )
            );

        $view = new View();
        $view->setEventManager(
            $eventManagerMock
        );

        $result = $view->{$method}();

        $this->assertEquals('test content', $result);
    }

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
        $view->setTemplatePath(
            __DIR__.'/fixtures/templates/test_template.phtml'
        );
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
        $view->setTemplatePath(
            __DIR__.'/fixtures/templates/test_template.phtml'
        );

        $content = $view->__toString();

        $this->assertEquals(
            '<template>test_value</template>',
            $content
        );
    }

    public function test_toStringMethodUsingEmptyTemplatePath()
    {
        $this->expectException(Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('Template file path is empty.');

        $view = new View();
        $view->__toString();
    }

}
