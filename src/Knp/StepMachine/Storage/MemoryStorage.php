<?php

namespace Knp\StepMachine\Storage;

use Knp\StepMachine\Storage;

class MemoryStorage implements Storage
{
    private $bag = [];

    public function set($key, $value)
    {
        $this->bag[$key] = $value;
    }

    public function get($key, $default = null)
    {
        if (isset($this->bag[$key])) {
            return $this->bag[$key];
        }

        return $default;
    }

    public function has($key)
    {
        return isset($this->bag[$key]);
    }

    public function remove($key)
    {
        $value = isset($this->bag[$key]) ? $this->bag[$key] : null;
        unset($this->bag[$key]);

        return $value;
    }

    public function clear()
    {
        $this->bag = [];
    }
}
