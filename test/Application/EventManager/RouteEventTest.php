<?php

namespace Tiny\Skeleton\Application\EventManager;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Tiny\Skeleton\Application\Exception\InvalidArgumentException;
use Tiny\Router\Route;
use stdClass;

class RouteEventTest extends TestCase
{

    public function testCreation()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Data must be instance of the "%s"',
                Route::class
            )
        );

        new RouteEvent(new stdClass());
    }

    public function testSetDataMethod()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Data must be instance of the "%s"',
                Route::class
            )
        );

        $configEvent = new RouteEvent();
        $configEvent->setData(new stdClass());
    }

}
