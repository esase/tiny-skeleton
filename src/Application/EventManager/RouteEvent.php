<?php

namespace Tiny\Skeleton\Application\EventManager;

use Tiny\EventManager\AbstractEvent;
use Tiny\EventManager\Event;
use Tiny\Skeleton\Application\Exception\InvalidArgumentException;
use Tiny\Router;

class RouteEvent extends Event
{

    const EVENT_REGISTER_ROUTE = 'application.register.route';

    const EVENT_BEFORE_MATCHING_ROUTE = 'application.before.matching.route';

    const EVENT_AFTER_MATCHING_ROUTE = 'application.after.matching.route';

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
        if (null !== $data && !($data instanceof Router\Route)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Data must be instance of the "%s"',
                    Router\Route::class
                )
            );
        }
    }

}
