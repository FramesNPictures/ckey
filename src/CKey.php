<?php

namespace Fnp\CKey;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

class CKey
{
    private $dependencies = [];

    public function __construct(...$dependencies)
    {
        $this->dependencies = $dependencies;
    }

    public function key()
    {
        return $this->dKey() . CKeyManager::make()->get(get_called_class());
    }

    public static function forget()
    {
        CKeyManager::make()->reset(get_called_class());
    }

    public function __invoke()
    {
        return $this->key();
    }

    public function dKey()
    {
        $dKey = get_called_class();

        foreach ($this->dependencies as $dependency) {

            if ($dependency instanceof Model)
                $dependency = $dependency->getAttribute($dependency->getRouteKeyName());

            if ($dependency instanceof Arrayable)
                $dependency = $dependency->toArray();

            if ($dependency instanceof CKey)
                $dependency = $dependency->key();

            if ($dependency instanceof \stdClass)
                $dependency = get_object_vars($dependency);

            if (is_object($dependency))
                $dependency = get_object_vars($dependency);

            if (is_array($dependency))
                $dependency = json_encode($dependency);

            $dKey .= $dependency;
        }

        return hash('sha256', $dKey);
    }
}