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
use Tiny\EventManager\EventManager;
use Tiny\Skeleton\Module\Core\Utils\ViewHelperUtils;
use Tiny\Skeleton\View;

class ViewHelperPartialViewListener
{

    /**
     * @var EventManager
     */
    private EventManager $eventManager;

    /**
     * @var ViewHelperUtils
     */
    private ViewHelperUtils $viewHelperUtils;

    /**
     * ViewHelperPartialViewListener constructor.
     *
     * @param  EventManager     $eventManager
     * @param  ViewHelperUtils  $viewHelperUtils
     */
    public function __construct(
        EventManager $eventManager,
        ViewHelperUtils $viewHelperUtils
    ) {
        $this->eventManager = $eventManager;
        $this->viewHelperUtils = $viewHelperUtils;
    }

    /**
     * @param  Event  $event
     */
    public function __invoke(Event $event)
    {
        $arguments = $event->getParams()['arguments'];
        list($templatePath, $module) = $arguments;

        $view = new View(
            ($arguments[2] ?? []), // variables
            $this->viewHelperUtils->getTemplatePath($templatePath, $module)
        );
        $view->setEventManager($this->eventManager);

        $event->setData(
            $view
        );
    }

}
