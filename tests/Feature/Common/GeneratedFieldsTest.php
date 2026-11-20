<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\Common;

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

final class GeneratedFieldsTest extends BaseTestCase
{
    #[DataProvider('allReadersProvider')]
    #[Test]
    public function testGeneratedFields(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../Fixtures/Fixtures25'],
            'exclude' => ['PostgreSQL'],
        ]));

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);
        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
        ]);

        Assert::same($schema['withGeneratedFields'][SchemaInterface::GENERATED_FIELDS], [
            'id' => GeneratedField::ON_INSERT,
            'createdAt' => GeneratedField::BEFORE_INSERT,
            'createdAtGeneratedByDatabase' => GeneratedField::ON_INSERT,
            'updatedAt' => GeneratedField::BEFORE_INSERT | GeneratedField::BEFORE_UPDATE,
        ]);
    }
}
