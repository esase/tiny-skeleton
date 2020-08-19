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

use Tiny\Http;
use Tiny\Skeleton\Module\User;
use Tiny\View\View;

class UserController
{

    /**
     * @var User\Service\UserService
     */
    private User\Service\UserService $userService;

    /**
     * UserCliController constructor.
     *
     * @param  User\Service\UserService  $userService
     */
    public function __construct(User\Service\UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param  Http\AbstractResponse  $response
     */
    public function list(Http\AbstractResponse $response)
    {
        $response->setResponse(
            new View([
                'users' => $this->userService->getAllUsers()
            ])
        );
    }

    public function create()
    {
    }

}
