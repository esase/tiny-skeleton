<?php

namespace Tiny\Skeleton\Module\Base\Service;

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
use Tiny\Skeleton\Application\Exception\Request\AbstractException;
use Tiny\Skeleton\Module\Base\Utils\ViewHelperUtils;
use Tiny\View\View;

class NotFoundService
{

    /**
     * @var ViewHelperUtils
     */
    private ViewHelperUtils $viewHelperUtils;

    /**
     * @var EventManager
     */
    private EventManager $eventManager;

    /**
     * @var bool
     */
    private bool $isCliContext;

    /**
     * NotFoundService constructor.
     *
     * @param  ViewHelperUtils  $viewHelperUtils
     * @param  EventManager     $eventManager
     * @param  bool             $isCliContext
     */
    public function __construct(
        ViewHelperUtils $viewHelperUtils,
        EventManager $eventManager,
        bool $isCliContext
    ) {
        $this->viewHelperUtils = $viewHelperUtils;
        $this->eventManager = $eventManager;
        $this->isCliContext = $isCliContext;
    }

    /**
     * @param  AbstractResponse  $response
     * @param  string            $type
     * @param  string            $message
     *
     * @return AbstractResponse
     */
    public function getContent(
        AbstractResponse $response,
        string $type,
        string $message = ''
    ): AbstractResponse {
        $errorMessage = $message ?: 'Not found';
        $response
            ->setCode(AbstractResponse::RESPONSE_NOT_FOUND);

        if ($this->isCliContext) {
            $response->setResponse($errorMessage)
                ->setResponseType(
                    AbstractResponse::RESPONSE_TYPE_TEXT
                );

            return $response;
        }

        switch ($type) {
            case AbstractException::TYPE_HTML :
                $view = new View(
                    [
                        'message' => $errorMessage,
                    ]
                );
                $view->setTemplatePath(
                    $this->viewHelperUtils->getTemplatePath(
                        '404', 'Base'
                    )
                )
                    ->setLayoutPath(
                        $this->viewHelperUtils->getTemplatePath(
                            'layout/base', 'Base'
                        )
                    )
                    ->setEventManager($this->eventManager);

                $response->setResponse($view)
                    ->setResponseType(
                        AbstractResponse::RESPONSE_TYPE_HTML
                    );

                return $response;

            case AbstractException::TYPE_JSON:
                $response->setResponse(
                    json_encode(
                        [
                            'error' => $errorMessage,
                            'code'  => AbstractResponse::RESPONSE_NOT_FOUND,
                        ]
                    )
                )
                    ->setCode(AbstractResponse::RESPONSE_OK)
                    ->setResponseType(
                        AbstractResponse::RESPONSE_TYPE_JSON
                    );

                return $response;

            case AbstractException::TYPE_TEXT:
            default :
                $response->setResponse($errorMessage)
                    ->setResponseType(
                        AbstractResponse::RESPONSE_TYPE_TEXT
                    );

                return $response;
        }
    }

}
