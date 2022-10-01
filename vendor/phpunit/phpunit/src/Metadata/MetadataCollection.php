<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Metadata;

use function array_filter;
use function array_merge;
use function count;
use Countable;
use IteratorAggregate;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @psalm-immutable
 */
final class MetadataCollection implements Countable, IteratorAggregate
{
    /**
     * @psalm-var list<Metadata>
     */
    private readonly array $metadata;

    /**
     * @psalm-param list<Metadata> $metadata
     */
    public static function fromArray(array $metadata): self
    {
        return new self(...$metadata);
    }

    private function __construct(Metadata ...$metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @psalm-return list<Metadata>
     */
    public function asArray(): array
    {
        return $this->metadata;
    }

    public function count(): int
    {
        return count($this->metadata);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return $this->count() > 0;
    }

    public function getIterator(): MetadataCollectionIterator
    {
        return new MetadataCollectionIterator($this);
    }

    public function mergeWith(self $other): self
    {
        return new self(
            ...array_merge(
                $this->asArray(),
                $other->asArray()
            )
        );
    }

    public function isClassLevel(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isClassLevel();
                }
            )
        );
    }

    public function isMethodLevel(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isMethodLevel();
                }
            )
        );
    }

    public function isAfter(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isAfter();
                }
            )
        );
    }

    public function isAfterClass(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isAfterClass();
                }
            )
        );
    }

    public function isBackupGlobals(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isBackupGlobals();
                }
            )
        );
    }

    public function isBackupStaticProperties(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isBackupStaticProperties();
                }
            )
        );
    }

    public function isBeforeClass(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isBeforeClass();
                }
            )
        );
    }

    public function isBefore(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isBefore();
                }
            )
        );
    }

    public function isCodeCoverageIgnore(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isCodeCoverageIgnore();
                }
            )
        );
    }

    public function isCovers(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isCovers();
                }
            )
        );
    }

    public function isCoversClass(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isCoversClass();
                }
            )
        );
    }

    public function isCoversDefaultClass(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isCoversDefaultClass();
                }
            )
        );
    }

    public function isCoversFunction(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isCoversFunction();
                }
            )
        );
    }

    public function isExcludeGlobalVariableFromBackup(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isExcludeGlobalVariableFromBackup();
                }
            )
        );
    }

    public function isExcludeStaticPropertyFromBackup(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isExcludeStaticPropertyFromBackup();
                }
            )
        );
    }

    public function isCoversNothing(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isCoversNothing();
                }
            )
        );
    }

    public function isDataProvider(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isDataProvider();
                }
            )
        );
    }

    public function isDepends(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isDependsOnClass() || $metadata->isDependsOnMethod();
                }
            )
        );
    }

    public function isDependsOnClass(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isDependsOnClass();
                }
            )
        );
    }

    public function isDependsOnMethod(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isDependsOnMethod();
                }
            )
        );
    }

    public function isDoesNotPerformAssertions(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isDoesNotPerformAssertions();
                }
            )
        );
    }

    public function isGroup(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isGroup();
                }
            )
        );
    }

    public function isRunClassInSeparateProcess(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isRunClassInSeparateProcess();
                }
            )
        );
    }

    public function isRunInSeparateProcess(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isRunInSeparateProcess();
                }
            )
        );
    }

    public function isRunTestsInSeparateProcesses(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isRunTestsInSeparateProcesses();
                }
            )
        );
    }

    public function isTest(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isTest();
                }
            )
        );
    }

    public function isPreCondition(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isPreCondition();
                }
            )
        );
    }

    public function isPostCondition(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isPostCondition();
                }
            )
        );
    }

    public function isPreserveGlobalState(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isPreserveGlobalState();
                }
            )
        );
    }

    public function isRequiresMethod(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isRequiresMethod();
                }
            )
        );
    }

    public function isRequiresFunction(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isRequiresFunction();
                }
            )
        );
    }

    public function isRequiresOperatingSystem(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isRequiresOperatingSystem();
                }
            )
        );
    }

    public function isRequiresOperatingSystemFamily(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isRequiresOperatingSystemFamily();
                }
            )
        );
    }

    public function isRequiresPhp(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isRequiresPhp();
                }
            )
        );
    }

    public function isRequiresPhpExtension(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isRequiresPhpExtension();
                }
            )
        );
    }

    public function isRequiresPhpunit(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isRequiresPhpunit();
                }
            )
        );
    }

    public function isRequiresSetting(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isRequiresSetting();
                }
            )
        );
    }

    public function isTestDox(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isTestDox();
                }
            )
        );
    }

    public function isTestWith(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isTestWith();
                }
            )
        );
    }

    public function isUses(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isUses();
                }
            )
        );
    }

    public function isUsesClass(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isUsesClass();
                }
            )
        );
    }

    public function isUsesDefaultClass(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isUsesDefaultClass();
                }
            )
        );
    }

    public function isUsesFunction(): self
    {
        return new self(
            ...array_filter(
                $this->metadata,
                static function (Metadata $metadata): bool
                {
                    return $metadata->isUsesFunction();
                }
            )
        );
    }
}
