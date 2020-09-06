<?php

namespace Tiny\Skeleton\Module\Base\Service;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Tiny\EventManager\EventManager;
use Tiny\Http\AbstractResponse;
use Tiny\Skeleton\Module\Base\Utils\ViewHelperUtils;
use Tiny\View\View;

class NotFoundServiceTest extends TestCase
{

    public function testGetContentMethodUsingCliContext()
    {
        $responseMock = $this->createMock(AbstractResponse::class);
        $responseMock->expects($this->once())
            ->method('setCode')
            ->with(404)
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with('Not found')
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponseType')
            ->with('text/plain');

        $service = new NotFoundService(
            $this->createStub(
                ViewHelperUtils::class
            ),
            $this->createStub(
                EventManager::class
            ),
            true
        );
        $content = $service->getContent(
            $responseMock,
            'html'
        );

        $this->assertSame(
            $responseMock,
            $content
        );
    }

    public function testGetContentMethodUsingHttpContextAndHtmlType()
    {
        $eventManagerStub = $this->createStub(
            EventManager::class
        );

        $responseMock = $this->createMock(AbstractResponse::class);
        $responseMock->expects($this->once())
            ->method('setCode')
            ->with(404)
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with($this->isInstanceOf(View::class))
            ->will(
                $this->returnCallback(
                    function (View $view) use ($eventManagerStub, $responseMock
                    ) {
                        // make sure the View is constructed properly
                        $this->assertEquals(
                            'test',
                            $view->getTemplatePath()
                        );

                        $this->assertEquals(
                            'test',
                            $view->getLayoutPath()
                        );

                        $this->assertEquals(
                            ['message' => 'test_message'],
                            $view->getVariables()
                        );

                        $this->assertSame(
                            $eventManagerStub,
                            $view->getEventManager()
                        );

                        return $responseMock;
                    }
                )
            );
        $responseMock->expects($this->once())
            ->method('setResponseType')
            ->with('text/html');

        $viewHelperMock = $this->createMock(
            ViewHelperUtils::class
        );
        $viewHelperMock->expects($this->exactly(2))
            ->method('getTemplatePath')
            ->withConsecutive(
                ['404', 'Base'],
                ['layout/base', 'Base'],
                )
            ->willReturn('test');

        $service = new NotFoundService(
            $viewHelperMock,
            $eventManagerStub,
            false
        );

        $content = $service->getContent(
            $responseMock,
            'html',
            'test_message'
        );

        $this->assertSame(
            $responseMock,
            $content
        );
    }

    public function testGetContentMethodUsingHttpContextAndJsonType()
    {
        $eventManagerStub = $this->createStub(
            EventManager::class
        );

        $responseMock = $this->createMock(AbstractResponse::class);
        $responseMock->expects($this->exactly(2))
            ->method('setCode')
            ->withConsecutive([404], [200])
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with(json_encode([
                'error' => 'test_message',
                'code' => 404
            ]))
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponseType')
            ->with('application/json');

        $service = new NotFoundService(
            $this->createMock(
                ViewHelperUtils::class
            ),
            $eventManagerStub,
            false
        );

        $content = $service->getContent(
            $responseMock,
            'json',
            'test_message'
        );

        $this->assertSame(
            $responseMock,
            $content
        );
    }

    public function testGetContentMethodUsingHttpContextAndTextType()
    {
        $eventManagerStub = $this->createStub(
            EventManager::class
        );

        $responseMock = $this->createMock(AbstractResponse::class);
        $responseMock->expects($this->once())
            ->method('setCode')
            ->with(404)
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with('test_message')
            ->will($this->returnSelf());
        $responseMock->expects($this->once())
            ->method('setResponseType')
            ->with('text/plain');

        $service = new NotFoundService(
            $this->createMock(
                ViewHelperUtils::class
            ),
            $eventManagerStub,
            false
        );

        $content = $service->getContent(
            $responseMock,
            'text',
            'test_message'
        );

        $this->assertSame(
            $responseMock,
            $content
        );
    }

}
