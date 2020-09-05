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
use Tiny\Skeleton\Module\Base;
use Tiny\View\View;

class NotFoundServiceTest extends TestCase
{

    public function testGetContentMethodUsingCliContext()
    {
        $service = new NotFoundService(
            $this->createStub(
                Base\Utils\ViewHelperUtils::class
            ),
            $this->createStub(
                EventManager::class
            ),
            true
        );
        $content = $service->getContent();

        $this->assertEquals(
            'Not found',
            $content
        );
    }

    public function testGetContentMethodUsingHttpContext()
    {
        $viewHelperMock = $this->createMock(
            Base\Utils\ViewHelperUtils::class
        );
        $viewHelperMock->expects($this->exactly(2))
            ->method('getTemplatePath')
            ->withConsecutive(
                ['404', 'Base'],
                ['layout/base', 'Base'],
                )
            ->willReturn('test');

        $eventManagerStub = $this->createStub(
            EventManager::class
        );

        $service = new NotFoundService(
            $viewHelperMock,
            $eventManagerStub,
            false
        );
        /** @var View $content */
        $view = $service->getContent();

        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('test', $view->getTemplatePath());
        $this->assertEquals('test', $view->getLayoutPath());
        $this->assertSame($eventManagerStub, $view->getEventManager());
    }

}
