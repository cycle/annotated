<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Postgres;

// phpcs:ignore
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\Tests\Functional\Driver\Common\GeneratedFieldsTestCase;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Registry;
use PHPUnit\Framework\Attributes\Group;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

#[Group('driver')]
#[Group('driver-postgres')]
final class GeneratedFieldsTest extends GeneratedFieldsTestCase
{
    public const DRIVER = 'postgres';

    public function testSerialGeneratedFields(): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures25/PostgreSQL'],
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
                'smallSerial' => SchemaInterface::GENERATED_DB,
                'serial' => SchemaInterface::GENERATED_DB,
                'bigSerial' => SchemaInterface::GENERATED_DB,
            ],
            $schema['generatedFieldsSerial'][SchemaInterface::GENERATED_FIELDS]
        );
    }
}
