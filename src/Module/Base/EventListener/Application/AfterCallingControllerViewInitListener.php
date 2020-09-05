<?php

namespace Tiny\Skeleton\Module\Base\EventListener\Application;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Tiny\EventManager\EventManager;
use Tiny\Skeleton\Application\EventManager\ControllerEvent;
use Tiny\Skeleton\Application\Service\ConfigService;
use Tiny\Skeleton\Module\Base;
use Tiny\Http;
use Tiny\View\View;
use Tiny\Router;

class AfterCallingControllerViewInitListener
{

    /**
     * @var ConfigService
     */
    private ConfigService $configService;

    /**
     * @var EventManager
     */
    private EventManager $eventManager;

    /**
     * @var Base\Utils\ViewHelperUtils
     */
    private Base\Utils\ViewHelperUtils $viewHelperUtils;

    /**
     * AfterCallingControllerViewInitListener constructor.
     *
     * @param  ConfigService  $configService
     * @param  EventManager                $eventManager
     * @param  Base\Utils\ViewHelperUtils  $viewHelperUtils
     */
    public function __construct(
        ConfigService $configService,
        EventManager $eventManager,
        Base\Utils\ViewHelperUtils $viewHelperUtils
    ) {
        $this->configService = $configService;
        $this->eventManager = $eventManager;
        $this->viewHelperUtils = $viewHelperUtils;
    }

    /**
     * @param  ControllerEvent  $event
     */
    public function __invoke(ControllerEvent $event)
    {
        /** @var Http\AbstractResponse $response */
        $response = $event->getData();
        $controllerResponse = $response->getResponse();

        // initialize the view with additional settings
        if ($controllerResponse instanceof View) {
            // set both layout and template path (if they are missing)
            if (!$controllerResponse->getTemplatePath()) {
                $controllerResponse->setTemplatePath(
                    $this->getTemplatePath($event->getParams()['route'])
                );
            }

            if (!$controllerResponse->getLayoutPath()) {
                // get the View's configs
                $viewConfig = $this->configService->getConfig('view', []);

                $controllerResponse->setLayoutPath(
                    $this->viewHelperUtils->getTemplatePath(
                        $viewConfig['base_layout_path'],
                        'Base'
                    )
                );
            }

            $controllerResponse->setEventManager($this->eventManager);

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
