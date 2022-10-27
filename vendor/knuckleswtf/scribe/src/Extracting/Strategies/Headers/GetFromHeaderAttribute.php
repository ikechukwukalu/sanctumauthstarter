<?php

namespace Knuckles\Scribe\Extracting\Strategies\Headers;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Attributes\Header;
use Knuckles\Scribe\Extracting\Strategies\PhpAttributeStrategy;

/**
 * @extends PhpAttributeStrategy<Header>
 */
class GetFromHeaderAttribute extends PhpAttributeStrategy
{
    protected array $attributeNames = [Header::class];

    protected function extractFromAttributes(
        array $attributesOnMethod, array $attributesOnController,
        ExtractedEndpointData $endpointData
    ): ?array
    {
        $headers = [];
        foreach ([...$attributesOnController, ...$attributesOnMethod] as $attributeInstance) {
            $data = $attributeInstance->toArray();
            $data['example'] ??= $this->generateDummyValue('string');
            $headers[$data["name"]] = $data["example"];
        }
        return $headers;
    }

}
