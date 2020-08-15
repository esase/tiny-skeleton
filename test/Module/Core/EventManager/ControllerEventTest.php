<?php

namespace Tiny\Skeleton\Module\Core\EventManager;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Tiny\Skeleton\Module\Core;
use Tiny\Http;
use stdClass;

class ControllerEventTest extends TestCase
{

    public function testCreation()
    {
        $this->expectException(Core\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Data must be instance of the "%s"',
                Http\AbstractResponse::class
            )
        );

        new ControllerEvent(new stdClass());
    }

    public function testSetDataMethod()
    {
        $this->expectException(Core\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Data must be instance of the "%s"',
                Http\AbstractResponse::class
            )
        );

        $configEvent = new ControllerEvent();
        $configEvent->setData(new stdClass());
    }

}
