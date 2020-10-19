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

use Tiny\Http\AbstractResponse;

class UserController extends AbstractUserController
{

    /**
     * @param  AbstractResponse  $response
     */
    public function list(AbstractResponse $response)
    {
        $this->viewResponse($response, [
            'users' => $this->userService->findAll()
        ]);
    }

}
