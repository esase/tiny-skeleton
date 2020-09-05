<?php

namespace Tiny\Skeleton\Module\Base\Utils;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Tiny\Skeleton\Module\Base\Controller\NotFoundController;
use Tiny\Skeleton\Module\Base\Service\NotFoundService;
use Tiny\Http;

class NotFoundControllerTest extends TestCase
{

    public function testIndexMethod()
    {
        $notFoundServiceMock = $this->createMock(
            NotFoundService::class
        );
        $notFoundServiceMock->expects($this->once())
            ->method('getContent')
            ->willReturn(
                'test_content'
            );

        $responseMock = $this->createMock(
            Http\AbstractResponse::class
        );
        $responseMock->expects($this->once())
            ->method('setResponse')
            ->with('test_content')
            ->will($this->returnSelf());

        $responseMock->expects($this->once())
            ->method('setCode')
            ->with(404);

        $controller = new NotFoundController($notFoundServiceMock);
        $controller->index($responseMock);
    }

}
