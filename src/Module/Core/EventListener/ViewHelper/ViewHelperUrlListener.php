<?php

namespace Tiny\Skeleton\Module\Core\EventListener\ViewHelper;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\EventManager\Event;
use Tiny\Skeleton\Module\Core;
use Tiny\Router;

class ViewHelperUrlListener
{

    /**
     * @var Router\Router
     */
    private Router\Router $router;

    /**
     * @var Core\Utils\ViewHelperUtils
     */
    private Core\Utils\ViewHelperUtils $viewHelperUtils;

    /**
     * ViewHelperUrlListener constructor.
     *
     * @param  Router\Router               $router
     * @param  Core\Utils\ViewHelperUtils  $viewHelperUtils
     */
    public function __construct(
        Router\Router $router,
        Core\Utils\ViewHelperUtils $viewHelperUtils
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
