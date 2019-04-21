<?php

namespace Integreat\Gemeindeverzeichnis;

use Integreat\Gemeindeverzeichnis\Container\NotFoundException;
use Integreat\Gemeindeverzeichnis\Container\RuntimeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    /**
     * @var callable[]
     */
    protected $factories = [];

    /**
     * @var object[]
     */
    protected $services = [];

    public function __construct()
    {
        $this->factories = include dirname(__DIR__) . '/config/services.php';
    }

    public static function getInstance() : self
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException();
        }

        if (isset($this->services[$id])) {
            return $this->services[$id];
        }

        try {
            return $this->services[$id] = $this->factories[$id]($this);
        } catch (\Throwable $e) {
            throw new RuntimeException(
                sprintf('Could not create service %s', $id),
                0,
                $e
            );
        }
    }

    public function has($id)
    {
        return isset($this->factories[$id]);
    }
}
