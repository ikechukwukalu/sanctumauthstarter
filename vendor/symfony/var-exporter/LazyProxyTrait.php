<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarExporter;

use Symfony\Component\VarExporter\Hydrator as PublicHydrator;
use Symfony\Component\VarExporter\Internal\Hydrator;
use Symfony\Component\VarExporter\Internal\LazyObjectRegistry as Registry;
use Symfony\Component\VarExporter\Internal\LazyObjectState;

/**
 * @property int    $lazyObjectId   This property must be declared in classes using this trait
 * @property parent $lazyObjectReal This property must be declared in classes using this trait;
 *                                  its type should match the type of the proxied object
 */
trait LazyProxyTrait
{
    /**
     * Creates a lazy-loading virtual proxy.
     *
     * @param \Closure():object $initializer Returns the proxied object
     */
    public static function createLazyProxy(\Closure $initializer, self $instance = null): static
    {
        if (self::class !== $class = $instance ? $instance::class : static::class) {
            $skippedProperties = ["\0".self::class."\0lazyObjectId" => true];
        } elseif (\defined($class.'::LAZY_OBJECT_PROPERTY_SCOPES')) {
            Hydrator::$propertyScopes[$class] ??= $class::LAZY_OBJECT_PROPERTY_SCOPES;
        }

        $instance ??= (Registry::$classReflectors[$class] ??= new \ReflectionClass($class))->newInstanceWithoutConstructor();
        $instance->lazyObjectId = $id = spl_object_id($instance);
        Registry::$states[$id] = new LazyObjectState($initializer);

        foreach (Registry::$classResetters[$class] ??= Registry::getClassResetters($class) as $reset) {
            $reset($instance, $skippedProperties ??= []);
        }

        return $instance;
    }

    /**
     * Returns whether the object is initialized.
     */
    public function isLazyObjectInitialized(): bool
    {
        if (0 >= ($this->lazyObjectId ?? 0)) {
            return true;
        }

        return \array_key_exists("\0".self::class."\0lazyObjectReal", (array) $this);
    }

    /**
     * Forces initialization of a lazy object and returns it.
     */
    public function initializeLazyObject(): parent
    {
        if (isset($this->lazyObjectReal)) {
            return $this->lazyObjectReal;
        }

        return $this;
    }

    /**
     * @return bool Returns false when the object cannot be reset, ie when it's not a lazy object
     */
    public function resetLazyObject(): bool
    {
        if (0 >= ($this->lazyObjectId ?? 0)) {
            return false;
        }

        if (\array_key_exists("\0".self::class."\0lazyObjectReal", (array) $this)) {
            unset($this->lazyObjectReal);
        }

        return true;
    }

    public function &__get($name): mixed
    {
        $propertyScopes = Hydrator::$propertyScopes[$this::class] ??= Hydrator::getPropertyScopes($this::class);
        $scope = null;
        $instance = $this;

        if ([$class, , $readonlyScope] = $propertyScopes[$name] ?? null) {
            $scope = Registry::getScope($propertyScopes, $class, $name);

            if (null === $scope || isset($propertyScopes["\0$scope\0$name"])) {
                if (isset($this->lazyObjectId)) {
                    if ('lazyObjectReal' === $name && self::class === $scope) {
                        $state = Registry::$states[$this->lazyObjectId] ?? null;
                        $this->lazyObjectReal = $state ? ($state->initializer)() : null;

                        return $this->lazyObjectReal;
                    }
                    if (isset($this->lazyObjectReal)) {
                        $instance = $this->lazyObjectReal;
                    }
                }
                $parent = 2;
                goto get_in_scope;
            }
        }
        $parent = (Registry::$parentMethods[self::class] ??= Registry::getParentMethods(self::class))['get'];

        if (isset($this->lazyObjectReal)) {
            $instance = $this->lazyObjectReal;
        } else {
            if (2 === $parent) {
                return parent::__get($name);
            }
            $value = parent::__get($name);

            return $value;
        }

        if (!$parent && null === $class && !\array_key_exists($name, (array) $instance)) {
            $frame = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
            trigger_error(sprintf('Undefined property: %s::$%s in %s on line %s', $instance::class, $name, $frame['file'], $frame['line']), \E_USER_NOTICE);
        }

        get_in_scope:

        if (null === $scope) {
            if (null === $readonlyScope && 1 !== $parent) {
                return $instance->$name;
            }
            $value = $instance->$name;

            return $value;
        }
        $accessor = Registry::$classAccessors[$scope] ??= Registry::getClassAccessors($scope);

        return $accessor['get']($instance, $name, null !== $readonlyScope || 1 === $parent);
    }

    public function __set($name, $value): void
    {
        $propertyScopes = Hydrator::$propertyScopes[$this::class] ??= Hydrator::getPropertyScopes($this::class);
        $scope = null;
        $instance = $this;

        if ([$class, , $readonlyScope] = $propertyScopes[$name] ?? null) {
            $scope = Registry::getScope($propertyScopes, $class, $name, $readonlyScope);

            if ($readonlyScope === $scope || isset($propertyScopes["\0$scope\0$name"])) {
                if (isset($this->lazyObjectId)) {
                    if ('lazyObjectReal' === $name && self::class === $scope) {
                        $this->lazyObjectReal = $value;

                        return;
                    }
                    if (isset($this->lazyObjectReal)) {
                        $instance = $this->lazyObjectReal;
                    }
                }
                goto set_in_scope;
            }
        }

        if (isset($this->lazyObjectReal)) {
            $instance = $this->lazyObjectReal;
        } elseif ((Registry::$parentMethods[self::class] ??= Registry::getParentMethods(self::class))['set']) {
            parent::__set($name, $value);

            return;
        }

        set_in_scope:

        if (null === $scope) {
            $instance->$name = $value;
        } else {
            $accessor = Registry::$classAccessors[$scope] ??= Registry::getClassAccessors($scope);
            $accessor['set']($instance, $name, $value);
        }
    }

    public function __isset($name): bool
    {
        $propertyScopes = Hydrator::$propertyScopes[$this::class] ??= Hydrator::getPropertyScopes($this::class);
        $scope = null;
        $instance = $this;

        if ([$class] = $propertyScopes[$name] ?? null) {
            $scope = Registry::getScope($propertyScopes, $class, $name);

            if (null === $scope || isset($propertyScopes["\0$scope\0$name"])) {
                if (isset($this->lazyObjectId)) {
                    if ('lazyObjectReal' === $name && self::class === $scope) {
                        $state = Registry::$states[$this->lazyObjectId] ?? null;

                        return null !== $this->lazyObjectReal = $state ? ($state->initializer)() : null;
                    }
                    if (isset($this->lazyObjectReal)) {
                        $instance = $this->lazyObjectReal;
                    }
                }
                goto isset_in_scope;
            }
        }

        if (isset($this->lazyObjectReal)) {
            $instance = $this->lazyObjectReal;
        } elseif ((Registry::$parentMethods[self::class] ??= Registry::getParentMethods(self::class))['isset']) {
            return parent::__isset($name);
        }

        isset_in_scope:

        if (null === $scope) {
            return isset($instance->$name);
        }
        $accessor = Registry::$classAccessors[$scope] ??= Registry::getClassAccessors($scope);

        return $accessor['isset']($instance, $name);
    }

    public function __unset($name): void
    {
        $propertyScopes = Hydrator::$propertyScopes[$this::class] ??= Hydrator::getPropertyScopes($this::class);
        $scope = null;
        $instance = $this;

        if ([$class, , $readonlyScope] = $propertyScopes[$name] ?? null) {
            $scope = Registry::getScope($propertyScopes, $class, $name, $readonlyScope);

            if ($readonlyScope === $scope || isset($propertyScopes["\0$scope\0$name"])) {
                if (isset($this->lazyObjectId)) {
                    if ('lazyObjectReal' === $name && self::class === $scope) {
                        unset($this->lazyObjectReal);

                        return;
                    }
                    if (isset($this->lazyObjectReal)) {
                        $instance = $this->lazyObjectReal;
                    }
                }
                goto unset_in_scope;
            }
        }

        if (isset($this->lazyObjectReal)) {
            $instance = $this->lazyObjectReal;
        } elseif ((Registry::$parentMethods[self::class] ??= Registry::getParentMethods(self::class))['unset']) {
            parent::__unset($name);

            return;
        }

        unset_in_scope:

        if (null === $scope) {
            unset($instance->$name);
        } else {
            $accessor = Registry::$classAccessors[$scope] ??= Registry::getClassAccessors($scope);
            $accessor['unset']($instance, $name);
        }
    }

    public function __clone(): void
    {
        if (!isset($this->lazyObjectId)) {
            if ((Registry::$parentMethods[self::class] ??= Registry::getParentMethods(self::class))['clone']) {
                parent::__clone();
            }

            return;
        }

        if (\array_key_exists("\0".self::class."\0lazyObjectReal", (array) $this)) {
            $this->lazyObjectReal = clone $this->lazyObjectReal;
        }
        if ($state = Registry::$states[$this->lazyObjectId] ?? null) {
            Registry::$states[$this->lazyObjectId = spl_object_id($this)] = clone $state;
        }
    }

    public function __serialize(): array
    {
        $class = self::class;

        if (!isset($this->lazyObjectReal) && (Registry::$parentMethods[$class] ??= Registry::getParentMethods($class))['serialize']) {
            $properties = parent::__serialize();
        } else {
            $properties = (array) $this;
        }
        unset($properties["\0$class\0lazyObjectId"]);

        if (isset($this->lazyObjectReal) || Registry::$parentMethods[$class]['serialize'] || !Registry::$parentMethods[$class]['sleep']) {
            return $properties;
        }

        $scope = get_parent_class($class);
        $data = [];

        foreach (parent::__sleep() as $name) {
            $value = $properties[$k = $name] ?? $properties[$k = "\0*\0$name"] ?? $properties[$k = "\0$scope\0$name"] ?? $k = null;

            if (null === $k) {
                trigger_error(sprintf('serialize(): "%s" returned as member variable from __sleep() but does not exist', $name), \E_USER_NOTICE);
            } else {
                $data[$k] = $value;
            }
        }

        return $data;
    }

    public function __unserialize(array $data): void
    {
        $class = self::class;

        if (isset($data["\0$class\0lazyObjectReal"])) {
            foreach (Registry::$classResetters[$class] ??= Registry::getClassResetters($class) as $reset) {
                $reset($this, $data);
            }

            if (1 < \count($data)) {
                PublicHydrator::hydrate($this, $data);
            } else {
                $this->lazyObjectReal = $data["\0$class\0lazyObjectReal"];
            }
            Registry::$states[-1] ??= new LazyObjectState(static fn () => throw new \LogicException('Lazy proxy has no initializer.'));
            $this->lazyObjectId = -1;
        } elseif ((Registry::$parentMethods[$class] ??= Registry::getParentMethods($class))['unserialize']) {
            parent::__unserialize($data);
        } else {
            PublicHydrator::hydrate($this, $data);

            if (Registry::$parentMethods[$class]['wakeup']) {
                parent:__wakeup();
            }
        }
    }

    public function __destruct()
    {
        if (isset($this->lazyObjectId)) {
            if (0 < $this->lazyObjectId) {
                unset(Registry::$states[$this->lazyObjectId]);
            }

            return;
        }

        if ((Registry::$parentMethods[self::class] ??= Registry::getParentMethods(self::class))['destruct']) {
            parent::__destruct();
        }
    }
}
