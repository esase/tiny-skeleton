<?php

namespace Tiny\Skeleton\Module\Core\EventManager;

use Tiny\EventManager\AbstractEvent;
use Tiny\EventManager\Event;
use Tiny\Skeleton\Module\Core\Exception;
use Tiny\Router;

class RouteEvent extends Event
{

    const EVENT_BEFORE_MATCHING = 'core.before.matching.route';

    const EVENT_AFTER_MATCHING = 'core.after.matching.route';

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
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Data must be instance of the "%s"',
                    Router\Route::class
                )
            );
        }
    }

}
