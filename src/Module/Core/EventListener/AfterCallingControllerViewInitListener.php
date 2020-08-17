<?php

namespace Tiny\Skeleton\Module\Core\EventListener;

/*
 * This file is part of the Tiny package.
 *
 * (c) Alex Ermashev <alexermashev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ReflectionClass;
use ReflectionException;
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
     * AfterCallingControllerViewInitListener constructor.
     *
     * @param  Core\Service\ConfigService  $configService
     */
    public function __construct(
        Core\Service\ConfigService $configService
    ) {
        $this->configService = $configService;
    }

    /**
     * @param  Core\EventManager\ControllerEvent  $event
     *
     * @throws ReflectionException
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
                ($viewConfig['base_layout_path'] ?? '')
            )
                ->setTemplatePath(
                    $this->getTemplatePath(
                        $event->getParams()['route'],
                        $viewConfig
                    )
                );

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
     * @param  array         $viewConfigs
     *
     * @return string
     * @throws ReflectionException
     */
    private function getTemplatePath(
        Router\Route $route,
        array $viewConfigs
    ): string {
        $reflector = new ReflectionClass($route->getController());

        return str_replace(
            [
                '{module}',
                '{controller_name}',
                '{action}',
            ], [
            dirname($reflector->getFileName(), 2),
            substr(strrchr($route->getController(), '\\'), 1),
            $route->getMatchedAction(),
        ], $viewConfigs['template_path_mask']
        );
    }

}
