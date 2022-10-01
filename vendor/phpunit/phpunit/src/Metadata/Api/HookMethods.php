<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata\Api;

use function array_unshift;
use function class_exists;
use PHPUnit\Metadata\Parser\Registry;
use PHPUnit\Util\Reflection;
use ReflectionClass;
use ReflectionException;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class HookMethods
{
    private static array $hookMethods = [];

    /**
     * @psalm-param class-string $className
     */
    public function hookMethods(string $className): array
    {
        if (!class_exists($className, false)) {
            return self::emptyHookMethodsArray();
        }

        if (isset(self::$hookMethods[$className])) {
            return self::$hookMethods[$className];
        }

        self::$hookMethods[$className] = self::emptyHookMethodsArray();

        try {
            foreach ((new Reflection)->methodsInTestClass(new ReflectionClass($className)) as $method) {
                $metadata = Registry::parser()->forMethod($className, $method->getName());

                if ($method->isStatic()) {
                    if ($metadata->isBeforeClass()->isNotEmpty()) {
                        array_unshift(
                            self::$hookMethods[$className]['beforeClass'],
                            $method->getName()
                        );
                    }

                    if ($metadata->isAfterClass()->isNotEmpty()) {
                        self::$hookMethods[$className]['afterClass'][] = $method->getName();
                    }
                }

                if ($metadata->isBefore()->isNotEmpty()) {
                    array_unshift(
                        self::$hookMethods[$className]['before'],
                        $method->getName()
                    );
                }

                if ($metadata->isPreCondition()->isNotEmpty()) {
                    array_unshift(
                        self::$hookMethods[$className]['preCondition'],
                        $method->getName()
                    );
                }

                if ($metadata->isPostCondition()->isNotEmpty()) {
                    self::$hookMethods[$className]['postCondition'][] = $method->getName();
                }

                if ($metadata->isAfter()->isNotEmpty()) {
                    self::$hookMethods[$className]['after'][] = $method->getName();
                }
            }
        } catch (ReflectionException $e) {
        }

        return self::$hookMethods[$className];
    }

    private static function emptyHookMethodsArray(): array
    {
        return [
            'beforeClass'   => ['setUpBeforeClass'],
            'before'        => ['setUp'],
            'preCondition'  => ['assertPreConditions'],
            'postCondition' => ['assertPostConditions'],
            'after'         => ['tearDown'],
            'afterClass'    => ['tearDownAfterClass'],
        ];
    }
}
