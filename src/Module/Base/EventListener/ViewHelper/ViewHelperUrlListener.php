<?php

namespace Tiny\Skeleton\Module\Base\EventListener\ViewHelper;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\EventManager\Event;
use Tiny\Skeleton\Module\Base;
use Tiny\Router;

class ViewHelperUrlListener
{

    /**
     * @var Router\Router
     */
    private Router\Router $router;

    /**
     * @var Base\Utils\ViewHelperUtils
     */
    private Base\Utils\ViewHelperUtils $viewHelperUtils;

    /**
     * ViewHelperUrlListener constructor.
     *
     * @param  Router\Router               $router
     * @param  Base\Utils\ViewHelperUtils  $viewHelperUtils
     */
    public function __construct(
        Router\Router $router,
        Base\Utils\ViewHelperUtils $viewHelperUtils
    ) {
        $this->router = $router;
        $this->viewHelperUtils = $viewHelperUtils;
    }

    /**
     * @param  Event  $event
     */
    public function __invoke(Event $event)
    {
        $arguments = $event->getParams()['arguments'];
        list($controller, $action, $module) = $arguments;

        $event->setData(
            $this->router->assembleRequest(
                $this->viewHelperUtils->getControllerPath($controller, $module),
                $action,
                ($arguments[3] ?? [])
            )
        );
    }

}
