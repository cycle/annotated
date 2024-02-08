<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Postgres;

// phpcs:ignore
use Cycle\Annotated\Entities;
use Cycle\Annotated\Tests\Functional\Driver\Common\GeneratedFieldsTest as CommonClass;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Registry;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

/**
 * @group driver
 * @group driver-postgres
 */
final class GeneratedFieldsTest extends CommonClass
{
    public const DRIVER = 'postgres';

    /**
     * @dataProvider allReadersProvider
     */
    public function testSerialGeneratedFields(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures25/PostgreSQL'],
            'exclude' => [],
        ]));

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($tokenizer->classLocator(), $reader),
        ]);

        $this->assertSame(
            [
                'id' => GeneratedField::ON_INSERT,
                'smallSerial' => GeneratedField::ON_INSERT,
                'serial' => GeneratedField::ON_INSERT,
                'bigSerial' => GeneratedField::ON_INSERT,
            ],
            $schema['withGeneratedSerial'][SchemaInterface::GENERATED_FIELDS]
        );
    }
}
