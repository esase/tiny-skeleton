<?php

namespace Tiny\Skeleton\Module\User\Service;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\Http;

class UserService
{

    /**
     * @return array
     */
    public function getAllUsers(): array
    {
        return [
            ['id' => 1, 'name' => 'tester'],
            ['id' => 2, 'name' => 'tester2']
        ];
    }

}
