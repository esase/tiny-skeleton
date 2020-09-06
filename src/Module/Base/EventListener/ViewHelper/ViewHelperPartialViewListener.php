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

use Tiny\EventManager;
use Tiny\Skeleton\Module\Base\Utils\ViewHelperUtils;
use Tiny\View\View;

class ViewHelperPartialViewListener
{

    /**
     * @var EventManager\EventManager
     */
    private EventManager\EventManager $eventManager;

    /**
     * @var ViewHelperUtils
     */
    private ViewHelperUtils $viewHelperUtils;

    /**
     * ViewHelperPartialViewListener constructor.
     *
     * @param  EventManager\EventManager  $eventManager
     * @param  ViewHelperUtils            $viewHelperUtils
     */
    public function __construct(
        EventManager\EventManager $eventManager,
        ViewHelperUtils $viewHelperUtils
    ) {
        $this->eventManager = $eventManager;
        $this->viewHelperUtils = $viewHelperUtils;
    }

    /**
     * @param  EventManager\Event  $event
     */
    public function __invoke(EventManager\Event $event)
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
