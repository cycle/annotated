<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Postgres;

// phpcs:ignore
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\Tests\Functional\Driver\Common\GeneratedFieldsTestCase;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Registry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

#[Group('driver')]
#[Group('driver-postgres')]
final class GeneratedFieldsTest extends GeneratedFieldsTestCase
{
    public const DRIVER = 'postgres';

    #[DataProvider('allReadersProvider')]
    public function testSerialGeneratedFields(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures25/PostgreSQL'],
            'exclude' => [],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
        ]);

        $this->assertSame(
            [
                'id' => GeneratedField::ON_INSERT,
                'smallSerial' => GeneratedField::ON_INSERT,
                'serial' => GeneratedField::ON_INSERT,
                'bigSerial' => GeneratedField::ON_INSERT,
            ],
            $schema['withGeneratedSerial'][SchemaInterface::GENERATED_FIELDS],
        );
    }
}
