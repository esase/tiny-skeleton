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
use Tiny\Http\AbstractResponse;
use Tiny\Skeleton\Module\Base\Controller\NotFoundController;
use Tiny\Skeleton\Module\Base\Service\NotFoundService;

class NotFoundControllerTest extends TestCase
{

    public function testIndexMethod()
    {
        $responseStub = $this->createStub(
            AbstractResponse::class
        );
        $notFoundServiceMock = $this->createMock(
            NotFoundService::class
        );
        $notFoundServiceMock->expects($this->once())
            ->method('getContent')
            ->with(
                $responseStub,
                'html'
            )
            ->willReturn(
                $responseStub
            );

        $controller = new NotFoundController($notFoundServiceMock);
        $controller->index($responseStub);
    }

}
