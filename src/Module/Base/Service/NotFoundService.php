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
use Tiny\Skeleton\Module\Base;
use Tiny\View\View;

class NotFoundService
{

    /**
     * @var Base\Utils\ViewHelperUtils
     */
    private Base\Utils\ViewHelperUtils $viewHelperUtils;

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
     * @param  Base\Utils\ViewHelperUtils  $viewHelperUtils
     * @param  EventManager                $eventManager
     * @param  bool                        $isCliContext
     */
    public function __construct(
        Base\Utils\ViewHelperUtils $viewHelperUtils,
        EventManager $eventManager,
        bool $isCliContext
    ) {
        $this->viewHelperUtils = $viewHelperUtils;
        $this->eventManager = $eventManager;
        $this->isCliContext = $isCliContext;
    }

    /**
     * @return View|string
     */
    public function getContent()
    {
        if ($this->isCliContext) {
            return 'Not found';
        }

        $view = new View();
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

        return $view;
    }

}
