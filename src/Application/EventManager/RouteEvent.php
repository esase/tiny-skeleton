<?php

namespace Tiny\Skeleton\Application\EventManager;

use Tiny\EventManager;
use Tiny\Skeleton\Application\Exception\InvalidArgumentException;
use Tiny\Router\Route;

class RouteEvent extends EventManager\Event
{

    const EVENT_REGISTER_ROUTE = 'application.register.route';

    const EVENT_BEFORE_MATCHING_ROUTE = 'application.before.matching.route';

    const EVENT_AFTER_MATCHING_ROUTE = 'application.after.matching.route';

    const EVENT_ROUTE_EXCEPTION = 'application.route.exception';

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
    function setData($data): EventManager\AbstractEvent
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
        if (null !== $data && !($data instanceof Route)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Data must be instance of the "%s"',
                    Route::class
                )
            );
        }
    }

}
