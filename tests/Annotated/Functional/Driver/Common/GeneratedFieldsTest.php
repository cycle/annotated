<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common;

use Cycle\Annotated\Entities;
use Cycle\ORM\Schema\GeneratedField;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Registry;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class GeneratedFieldsTest extends BaseTest
{
    /**
     * @dataProvider allReadersProvider
     */
    public function testGeneratedFields(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../Fixtures/Fixtures25'],
            'exclude' => ['PostgreSQL'],
        ]));

        $r = new Registry($this->dbal);
        $schema = (new Compiler())->compile($r, [
            new Entities($tokenizer->classLocator(), $reader),
        ]);

        $this->assertSame(
            [
                'id' => GeneratedField::ON_INSERT,
                'createdAt' => GeneratedField::BEFORE_INSERT,
                'createdAtGeneratedByDatabase' => GeneratedField::ON_INSERT,
                'updatedAt' => GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE,
            ],
            $schema['withGeneratedFields'][SchemaInterface::GENERATED_FIELDS]
        );
    }
}
