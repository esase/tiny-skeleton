<?php

namespace Tiny\Skeleton\Module\User\Controller;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Tiny\Skeleton\Module\User\Service\UserService;

class UserServiceTest extends TestCase
{

    public function testGetAllUsersMethod()
    {
        $service = new UserService();

        $this->assertEquals([
            ['id' => 1, 'name' => 'tester'],
            ['id' => 2, 'name' => 'tester2']
        ], $service->getAllUsers());
    }

}
