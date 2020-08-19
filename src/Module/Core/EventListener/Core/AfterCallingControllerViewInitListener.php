<?php

namespace Tiny\Skeleton\Module\Core\EventListener\Core;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\EventManager\EventManager;
use Tiny\Skeleton\Module\Core;
use Tiny\Http;
use Tiny\Skeleton\View;
use Tiny\Router;

class AfterCallingControllerViewInitListener
{

    /**
     * @var Core\Service\ConfigService
     */
    private Core\Service\ConfigService $configService;

    /**
     * @var EventManager
     */
    private EventManager $eventManager;

    /**
     * @var Core\Utils\ViewHelperUtils
     */
    private Core\Utils\ViewHelperUtils $viewHelperUtils;

    /**
     * AfterCallingControllerViewInitListener constructor.
     *
     * @param  Core\Service\ConfigService  $configService
     * @param  EventManager                $eventManager
     * @param  Core\Utils\ViewHelperUtils  $viewHelperUtils
     */
    public function __construct(
        Core\Service\ConfigService $configService,
        EventManager $eventManager,
        Core\Utils\ViewHelperUtils $viewHelperUtils
    ) {
        $this->configService = $configService;
        $this->eventManager = $eventManager;
        $this->viewHelperUtils = $viewHelperUtils;
    }

    /**
     * @param  Core\EventManager\ControllerEvent  $event
     */
    public function __invoke(Core\EventManager\ControllerEvent $event)
    {
        /** @var Http\AbstractResponse $response */
        $response = $event->getData();
        $controllerResponse = $response->getResponse();

        // initialize the view with additional settings
        if ($controllerResponse instanceof View) {
            // get the View's configs
            $viewConfig = $this->configService->getConfig('view', []);

            // set both layout and template path
            $controllerResponse->setLayoutPath(
                $this->viewHelperUtils->getTemplatePath(
                    $viewConfig['base_layout_path'],
                    'Core'
                )
            )
                ->setTemplatePath(
                    $this->getTemplatePath($event->getParams()['route'])
                )
                ->setEventManager($this->eventManager);

            // return the modified response
            $response->setResponse(
                $controllerResponse
            );

            // replace the data in event
            $event->setData($response);
        }
    }

    /**
     * @param  Router\Route  $route
     *
     * @return string
     */
    private function getTemplatePath(
        Router\Route $route
    ): string {
        // extract the controller's name and action from the route
        $template = vsprintf(
            '%s/%s', [
                substr(strrchr($route->getController(), '\\'), 1),
                $route->getMatchedAction(),
            ]
        );

        // build the template path based on the received controller and action
        return $this->viewHelperUtils->getTemplatePath(
            $template,
            $this->viewHelperUtils->extractModuleName(
                $route->getController()
            )
        );
    }

}
