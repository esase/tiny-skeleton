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
use Tiny\Skeleton\Module\Core;
use Tiny\Skeleton\View;

class ViewHelperPartialViewListener
{

    /**
     * @var EventManager
     */
    private EventManager $eventManager;

    /**
     * ViewHelperPartialViewListener constructor.
     *
     * @param  EventManager  $eventManager
     */
    public function __construct(
        EventManager $eventManager
    ) {
        $this->eventManager = $eventManager;
    }

    /**
     * @param  Event  $event
     */
    public function __invoke(Event $event)
    {
        $arguments = $event->getParams()['arguments'];
        list($templatePath) = $arguments;

        $view = new View(($arguments[1] ?? []), $templatePath);
        $view->setEventManager($this->eventManager);

        $event->setData(
            $view
        );
    }

}
