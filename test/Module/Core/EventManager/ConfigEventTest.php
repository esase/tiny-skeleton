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
use stdClass;
use Tiny\Skeleton\Module\Core;

class ConfigEventTest extends TestCase
{

    public function testCreation()
    {
        $this->expectException(Core\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Data must be array'
        );

        new ConfigEvent(new stdClass());
    }

    public function testSetDataMethod()
    {
        $this->expectException(Core\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Data must be array'
        );

        $configEvent = new ConfigEvent();
        $configEvent->setData(new stdClass());
    }

}
