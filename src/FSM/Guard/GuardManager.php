<?php

namespace FSM\Guard;

use FSM\Container\ContainerInterface;

/**
 * Guards manager
 *
 * @package FSM\Guard
 */
class GuardManager implements GuardManagerInterface
{
    /** @var string  */
    private $methodCallable = 'isSatisfied';

    /** @var  ContainerInterface */
    private $container;


    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Gets named guard object
     *
     * @param string $name
     * @return GuardInterface
     */
    public function getGuard($name)
    {
        if(!$guard = $this->container->has($name))
        {
            $message = sprintf(
                '"%s" guard is not found in DI container',
                $name
            );
            throw new Exception\GuardNotFoundException($message);
        }

        $guard = $this->container->get($name);

        if(!($guard instanceof GuardInterface))
        {
            $message = sprintf(
                'Guard "%s" must be object and instance of GuardInterface',
                is_object($guard) ? get_class($guard) : gettype($guard)
            );
            throw new Exception\InvalidGuardException($message);
        }

        return $guard;
    }

    /**
     * Gets callable for named guard object
     *
     * @param string $name
     * @return callable
     */
    public function getGuardCallable($name)
    {
        return [$this->getGuard($name), $this->methodCallable];
    }

}
