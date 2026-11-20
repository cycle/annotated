<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Postgres;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Registry;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Test;

/**
 * Postgres serial columns (smallserial, serial, bigserial) are generated on insert.
 */
final class GeneratedFieldsTest extends BaseTestCase
{
    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testSerialGeneratedFields(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../Fixtures/Fixtures25/PostgreSQL'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
        ]);

        Assert::same($schema['withGeneratedSerial'][SchemaInterface::GENERATED_FIELDS], [
            'id' => GeneratedField::ON_INSERT,
            'smallSerial' => GeneratedField::ON_INSERT,
            'serial' => GeneratedField::ON_INSERT,
            'bigSerial' => GeneratedField::ON_INSERT,
        ]);
    }
}
