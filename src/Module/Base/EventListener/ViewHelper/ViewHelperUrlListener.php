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
use Tiny\Router\Router;
use Tiny\Skeleton\Module\Base\Utils\ViewHelperUtils;

class ViewHelperUrlListener
{

    /**
     * @var Router
     */
    private Router $router;

    /**
     * @var ViewHelperUtils
     */
    private ViewHelperUtils $viewHelperUtils;

    /**
     * ViewHelperUrlListener constructor.
     *
     * @param  Router           $router
     * @param  ViewHelperUtils  $viewHelperUtils
     */
    public function __construct(
        Router $router,
        ViewHelperUtils $viewHelperUtils
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
