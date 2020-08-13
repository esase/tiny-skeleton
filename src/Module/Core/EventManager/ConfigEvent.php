<?php

namespace Tiny\Skeleton\Module\Core\EventManager;

use Tiny\EventManager\AbstractEvent;
use Tiny\EventManager\Event;
use Tiny\Skeleton\Module\Core\Exception;

class ConfigEvent extends Event
{

    const EVENT_SET_CONFIGS = 'core.set.configs';

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
        if (null !== $data && !is_array($data)) {
            throw new Exception\InvalidArgumentException('Data must be array');
        }
    }

}
