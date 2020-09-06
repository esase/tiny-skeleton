<?php

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tiny\Skeleton\Application\Exception\Request;

use PHPUnit\Framework\TestCase;

class AbstractExceptionTest extends TestCase
{

    public function testGetTypeMethod()
    {
        $exception = $this->getMockForAbstractClass(
            AbstractException::class, [
            'html',
        ]
        );
        $this->assertEquals('html', $exception->getType());
    }

}
