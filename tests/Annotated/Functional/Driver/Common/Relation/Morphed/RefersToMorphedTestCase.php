<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common\Relation\Morphed;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\Fixtures\RefersToMorphed\MorphedParentInterface;
use Cycle\Annotated\Tests\Functional\Driver\Common\BaseTestCase;
use Cycle\ORM\Relation;
use Cycle\ORM\SchemaInterface as Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use PHPUnit\Framework\Attributes\DataProvider;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class RefersToMorphedTestCase extends BaseTestCase
{
    #[\Testo\Data\DataProvider('allReadersProvider')]
    #[\Testo\Test]
    public function testRelation(ReaderInterface $reader): void
    {
        $locator = (new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/../../../../../Fixtures/RefersToMorphed'],
            'exclude' => [],
        ])))->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities(new TokenizerEntityLocator($locator, $reader), $reader),
            new ResetTables(),
            new MergeColumns($reader),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($reader),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        \Testo\Assert::array($schema['comment'][Schema::RELATIONS])->hasKeys('parent');
        \Testo\Assert::same($schema['comment'][Schema::RELATIONS]['parent'][Relation::TYPE], Relation::REFERS_TO_MORPHED);
        \Testo\Assert::same($schema['comment'][Schema::RELATIONS]['parent'][Relation::TARGET], MorphedParentInterface::class);

        // Morphed refers-to stores the outer key and the target role on the source entity.
        \Testo\Assert::contains($schema['comment'][Schema::COLUMNS], 'parent_id');
        \Testo\Assert::contains($schema['comment'][Schema::COLUMNS], 'parent_role');
    }
}
