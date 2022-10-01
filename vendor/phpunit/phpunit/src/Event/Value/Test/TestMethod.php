<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Event\Code;

use function class_exists;
use function is_int;
use function is_numeric;
use function method_exists;
use function sprintf;
use PHPUnit\Event\DataFromDataProvider;
use PHPUnit\Event\TestDataCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Metadata\MetadataCollection;
use PHPUnit\Metadata\Parser\Registry as MetadataRegistry;
use ReflectionException;
use ReflectionMethod;
use SebastianBergmann\Exporter\Exporter;

/**
 * @psalm-immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class TestMethod extends Test
{
    /**
     * @psalm-var class-string
     */
    private readonly string $className;
    private readonly string $methodName;
    private readonly int $line;
    private readonly MetadataCollection $metadata;
    private readonly TestDataCollection $testData;

    public static function fromTestCase(TestCase $testCase): self
    {
        $location = self::sourceLocationFor($testCase::class, $testCase->name());

        return new self(
            $testCase::class,
            $testCase->name(),
            $location['file'],
            $location['line'],
            self::metadataFor($testCase::class, $testCase->name()),
            self::dataFor($testCase),
        );
    }

    /**
     * @psalm-param class-string $className
     */
    public function __construct(string $className, string $methodName, string $file, int $line, MetadataCollection $metadata, TestDataCollection $testData)
    {
        parent::__construct($file);

        $this->className  = $className;
        $this->methodName = $methodName;
        $this->line       = $line;
        $this->metadata   = $metadata;
        $this->testData   = $testData;
    }

    /**
     * @psalm-return class-string
     */
    public function className(): string
    {
        return $this->className;
    }

    public function methodName(): string
    {
        return $this->methodName;
    }

    public function line(): int
    {
        return $this->line;
    }

    public function metadata(): MetadataCollection
    {
        return $this->metadata;
    }

    public function testData(): TestDataCollection
    {
        return $this->testData;
    }

    /**
     * @psalm-assert-if-true TestMethod $this
     */
    public function isTestMethod(): bool
    {
        return true;
    }

    public function id(): string
    {
        $buffer = $this->className . '::' . $this->methodName;

        if ($this->testData()->hasDataFromDataProvider()) {
            $buffer .= '#' . $this->testData->dataFromDataProvider()->dataSetName();
        }

        return $buffer;
    }

    public function nameWithClass(): string
    {
        return $this->className . '::' . $this->name();
    }

    public function name(): string
    {
        if (!$this->testData->hasDataFromDataProvider()) {
            return $this->methodName;
        }

        $dataSetName = $this->testData->dataFromDataProvider()->dataSetName();

        if (is_int($dataSetName)) {
            $dataSetName = sprintf(
                ' with data set #%d',
                $dataSetName
            );
        } else {
            $dataSetName = sprintf(
                ' with data set "%s"',
                $dataSetName
            );
        }

        return $this->methodName . $dataSetName;
    }

    private static function dataFor(TestCase $testCase): TestDataCollection
    {
        $testData = [];

        if ($testCase->usesDataProvider()) {
            $dataSetName = $testCase->dataName();

            if (is_numeric($dataSetName)) {
                $dataSetName = (int) $dataSetName;
            }

            $testData[] = DataFromDataProvider::from(
                $dataSetName,
                (new Exporter)->export($testCase->providedData())
            );
        }

        return TestDataCollection::fromArray($testData);
    }

    private static function metadataFor(string $className, string $methodName): MetadataCollection
    {
        if (class_exists($className)) {
            if (method_exists($className, $methodName)) {
                return MetadataRegistry::parser()->forClassAndMethod($className, $methodName);
            }

            return MetadataRegistry::parser()->forClass($className);
        }

        return MetadataCollection::fromArray([]);
    }

    /**
     * @psalm-param class-string $className
     *
     * @psalm-return array{file: string, line: int}
     */
    private static function sourceLocationFor(string $className, string $methodName): array
    {
        try {
            $reflector = new ReflectionMethod($className, $methodName);

            $file = $reflector->getFileName();
            $line = $reflector->getStartLine();
        } catch (ReflectionException) {
            $file = 'unknown';
            $line = 0;
        }

        return [
            'file' => $file,
            'line' => $line,
        ];
    }
}
