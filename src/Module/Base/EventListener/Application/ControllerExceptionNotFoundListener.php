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
use Tiny\Http\AbstractResponse;
use Tiny\Router\Route;
use Tiny\Skeleton\Application\EventManager\ControllerEvent;
use Tiny\Skeleton\Application\Exception\Request\NotFoundException;
use Tiny\Skeleton\Module\Base\Utils\ViewHelperUtils;
use Tiny\View\View;

class ControllerExceptionNotFoundListener
    extends AbstractControllerExceptionListener
{

    /**
     * @var AbstractResponse
     */
    private AbstractResponse $response;

    /**
     * @var EventManager
     */
    private EventManager $eventManager;

    /**
     * @var ViewHelperUtils
     */
    private ViewHelperUtils $viewHelperUtils;

    /**
     * ControllerExceptionNotFoundListener constructor.
     *
     * @param  AbstractResponse  $response
     * @param  EventManager      $eventManager
     * @param  ViewHelperUtils   $viewHelperUtils
     */
    public function __construct(
        AbstractResponse $response,
        EventManager $eventManager,
        ViewHelperUtils $viewHelperUtils
    ) {
        $this->response = $response;
        $this->eventManager = $eventManager;
        $this->viewHelperUtils = $viewHelperUtils;
    }

    /**
     * @param  ControllerEvent  $event
     */
    public function __invoke(ControllerEvent $event)
    {
        $eventParams = $event->getParams();
        $exception = $eventParams['exception'] ?? null;

        /** @var Route $route */
        $route = $eventParams['route'] ?? null;

        if ($exception && $route && $exception instanceof NotFoundException) {
            $errorMessage = $exception->getMessage() ?: 'Not found';

            if ($this->isJsonErrorResponse($route->getContext())) {
                $this->jsonErrorResponse(
                    $this->response,
                    $errorMessage,
                    AbstractResponse::RESPONSE_NOT_FOUND
                );
            } else {
                $this->viewErrorResponse(
                    $this->response,
                    $this->getView($errorMessage),
                    AbstractResponse::RESPONSE_NOT_FOUND
                );
            }

            $event->setData($this->response);
        }
    }

    /**
     * @param  string  $errorMessage
     *
     * @return View
     */
    private function getView(string $errorMessage): View
    {
        $view = new View(
            [
                'message' => $errorMessage,
            ]
        );
        $view->setTemplatePath(
            $this->viewHelperUtils->getTemplatePath(
                'NotFoundController/index', 'Base'
            )
        )
            ->setLayoutPath(
                $this->viewHelperUtils->getTemplatePath(
                    'layout/base', 'Base'
                )
            )
            ->setEventManager($this->eventManager);

        return $view;
    }

}
