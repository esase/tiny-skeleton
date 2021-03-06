<?php

namespace Tiny\Skeleton\Application\Service;

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

class ConfigServiceTest extends TestCase
{

    public function testGetConfigMethod()
    {
        $service = new ConfigService();
        $service->setConfigs(
            [
                'test' => 'test_value',
            ]
        );
        $this->assertEquals(
            'test_value',
            $service->getConfig('test')
        );
    }

    public function testGetConfigMethodUsingDefaultValue()
    {
        $service = new ConfigService();
        $this->assertEquals(
            'test_value',
            $service->getConfig('test', 'test_value')
        );
    }

    public function testGetConfigMethodUsingMissingConfig()
    {
        $name = 'test';
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Unknown config "%s"',
                $name
            )
        );

        $service = new ConfigService();
        $service->getConfig($name);
    }

}
