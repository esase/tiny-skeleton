<?php

namespace Tiny\Skeleton\Module\Core\EventListener\Core;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Tiny\EventManager;
use Tiny\Skeleton\Module\Core;
use Tiny\Skeleton\Module\Core\EventListener\ViewHelper\ViewHelperPartialViewListener;
use Tiny\Skeleton\Module\Core\Utils\ViewHelperUtils;
use Tiny\View\View;

class ViewHelperPartialViewListenerTest extends TestCase
{

    public function testInvokeMethod()
    {
        $eventManagerStub = $this->createStub(
            EventManager\EventManager::class
        );

        $eventMock = $this->createMock(
            EventManager\Event::class
        );
        $eventMock->expects($this->once())
            ->method('getParams')
            ->willReturn(
                [
                    'arguments' => [
                        'test/test.phtml',
                        'Test',
                        [
                            'test' => 'testValue',
                        ],
                    ],
                ]
            );
        $eventMock->expects($this->once())
            ->method('setData')
            ->with(
                $this->isInstanceOf(View::class)
            )
            ->will(
                $this->returnCallback(
                    function (View $view) use ($eventManagerStub, $eventMock) {
                        // make sure the View is constructed properly
                        $this->assertEquals(
                            'test/test.phtml',
                            $view->getTemplatePath()
                        );

                        $this->assertEquals(
                            ['test' => 'testValue'],
                            $view->getVariables()
                        );

                        $this->assertSame(
                            $eventManagerStub,
                            $view->getEventManager()
                        );

                        return $eventMock;
                    }
                )
            );


        $viewHelperUtilsMock = $this->createMock(
            ViewHelperUtils::class
        );
        $viewHelperUtilsMock->expects($this->once())
            ->method('getTemplatePath')
            ->with('test/test.phtml', 'Test')
            ->willReturn('test/test.phtml');

        $listener = new ViewHelperPartialViewListener(
            $eventManagerStub,
            $viewHelperUtilsMock
        );

        $listener($eventMock);
    }

}
