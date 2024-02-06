<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Registry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhp;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class GeneratedFieldsTestCase extends BaseTestCase
{
    protected readonly ReaderInterface $reader;

    public function setUp(): void
    {
        $this->reader = new AttributeReader();

        parent::setUp();
    }

    #[DataProvider('generatedFieldsDataProvider')]
    public function testGeneratedFields(string $role): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures25'],
            'exclude' => ['Php82', 'PostgreSQL'],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);
        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($locator, $this->reader), $this->reader),
        ]);

        $this->assertSame(
            [
                'id' => SchemaInterface::GENERATED_DB,
                'createdAt' => SchemaInterface::GENERATED_PHP_INSERT,
                'updatedAt' => SchemaInterface::GENERATED_PHP_INSERT | SchemaInterface::GENERATED_PHP_UPDATE,
            ],
            $schema[$role][SchemaInterface::GENERATED_FIELDS]
        );
    }

    #[RequiresPhp('^8.2')]
    public function testGeneratedFieldsEnumValues(): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures25/Php82'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($locator, $this->reader), $this->reader),
        ]);

        $this->assertSame(
            [
                'id' => SchemaInterface::GENERATED_DB,
                'createdAt' => SchemaInterface::GENERATED_PHP_INSERT,
                'updatedAt' => SchemaInterface::GENERATED_PHP_INSERT | SchemaInterface::GENERATED_PHP_UPDATE,
            ],
            $schema['generatedFieldsEnumValue'][SchemaInterface::GENERATED_FIELDS]
        );
    }

    public static function generatedFieldsDataProvider(): \Traversable
    {
        yield ['generatedFieldsEnum'];
        yield ['generatedFieldsInt'];
    }
}
