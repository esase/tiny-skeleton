<?php

namespace Tiny\Skeleton\Application\EventManager;

use Tiny\EventManager;
use Tiny\Skeleton\Application\Exception\InvalidArgumentException;

class ConfigEvent extends EventManager\Event
{

    const EVENT_SET_CONFIGS = 'application.set.configs';

    /**
     * ConfigEvent constructor.
     *
     * @param  null   $data
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
        if (null !== $data && !is_array($data)) {
            throw new InvalidArgumentException('Data must be array');
        }
    }

}
