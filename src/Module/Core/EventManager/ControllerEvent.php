<?php

namespace Tiny\Skeleton\Module\Core\EventManager;

use Tiny\EventManager\AbstractEvent;
use Tiny\EventManager\Event;
use Tiny\Skeleton\Module\Core\Exception;
use Tiny\Http;

class ControllerEvent extends Event
{

    const EVENT_BEFORE_CALLING_CONTROLLER = 'core.before.calling.controller';

    const EVENT_AFTER_CALLING_CONTROLLER = 'core.after.calling.controller';

    /**
     * RouteEvent constructor.
     *
     * @param  mixed  $data
     * @param  array  $params
     */
    public function __construct(
        $data = null,
        array $params = []
    ) {
        $this->checkData($data);

        parent::__construct($data, $params);
    }

    /**
     * @param $data
     *
     * @return $this
     */
    function setData($data): AbstractEvent
    {
        $this->checkData($data);
        $this->data = $data;

        return $this;
    }

    /**
     * @param  mixed  $data
     */
    private function checkData($data)
    {
        if (null !== $data && !($data instanceof Http\AbstractResponse)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Data must be instance of the "%s"',
                    Http\AbstractResponse::class
                )
            );
        }
    }

}
