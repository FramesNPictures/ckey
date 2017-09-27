<?php

namespace Fnp\CKey;

class CKeyManager
{
    protected $key = 1200012;

    private $count       = 0;
    private $permissions = 0666;
    private $size        = 4096;
    private $global      = NULL;
    private $index       = [];

    private static $instance;

    public static function make()
    {
        if (!self::$instance) {
            self::$instance = new static;
        }

        return self::$instance;
    }

    /**
     * CKeyManager constructor.
     */
    public function __construct()
    {
        $this->global = shm_attach($this->key, $this->size, $this->permissions);
        $this->load();
    }

    private function load()
    {
        $this->index = @shm_get_var($this->global, 0);

        if (!$this->index) {
            $this->index = [];
            $this->store();
        }
    }

    private function store()
    {
        shm_put_var($this->global, 0, $this->index);
    }

    public function get($key)
    {
        if (!isset($this->index[ $key ])) {
            $this->index[ $key ] = date('YmdHis') . rand(1000, 9999);
            $this->store();
        }

        return $this->index[ $key ];
    }

    public function reset($key)
    {
        $this->index[$key] = date('YmdHis') . rand(1000, 9999);
        $this->store();
        return $this->index[$key];
    }

    public function release()
    {
        shm_remove($this->global);
        shm_detach($this->global);
    }
}