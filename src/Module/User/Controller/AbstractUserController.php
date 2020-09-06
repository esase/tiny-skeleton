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

use Tiny\Skeleton\Module\Base\Controller\AbstractController;
use Tiny\Skeleton\Module\User\Service\UserService;

abstract class AbstractUserController extends AbstractController
{

    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * AbstractUserController constructor.
     *
     * @param  UserService  $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

}
