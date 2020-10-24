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

use Tiny\Router\Route;
use Tiny\Skeleton\Application\EventManager\ControllerEvent;
use Tiny\Skeleton\Module\Base\Controller\NotFoundController;
use Tiny\Skeleton\Module\Base\Service\AuthService;
use Tiny\Http\AbstractResponse;

class BeforeCallingControllerAuthListener
{

    /**
     * @var AuthService
     */
    private AuthService $authService;

    /**
     * @var AbstractResponse
     */
    private AbstractResponse $response;

    /**
     * @var bool
     */
    private bool $isCliContext;

    /**
     * BeforeCallingControllerAuthListener constructor.
     *
     * @param AuthService      $authService
     * @param AbstractResponse $response
     * @param bool             $isCliContext
     */
    public function __construct(
        AuthService $authService,
        AbstractResponse $response,
        bool $isCliContext
    ) {
        $this->authService = $authService;
        $this->response = $response;
        $this->isCliContext = $isCliContext;
    }

    /**
     * @param ControllerEvent $event
     */
    public function __invoke(ControllerEvent $event)
    {
        /** @var Route $route */
        $route = $event->getParams()['route'];

        if ($this->isCliContext || $route->getController() === NotFoundController::class) {
            return;
        }

        // globally check the authorization token
        $authToken = $_SERVER['HTTP_TOKEN'] ?? null;

        if ($authToken) {
            $tokenData = $this->authService->getTokenData($authToken);

            if ($tokenData) {
                // check the token permissions
                if ($tokenData['permission'] == AuthService::PERMISSION_READ
                    && in_array(
                        $_SERVER['REQUEST_METHOD'],
                        ['POST', 'PUT', 'DELETE', 'PATH']
                    )) {

                    // the action is not allowed
                    $this->sendResponse($event, AbstractResponse::NOT_ALLOWED);
                    return;
                }

                return;
            }
        }

        // token is not found
        $this->sendResponse($event, AbstractResponse::NOT_UNAUTHORIZED);
    }

    /**
     * @param ControllerEvent $event
     * @param int             $code
     */
    private function sendResponse(ControllerEvent $event, int $code)
    {
        $this->response->setCode($code); // not Unauthorized

        $event->setData(
            $this->response
        );
    }

}
