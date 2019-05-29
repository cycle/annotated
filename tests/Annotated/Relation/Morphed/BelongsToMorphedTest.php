<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests\Relation\Morphed;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Morphed\BelongsToMorphed;
use Cycle\Annotated\Annotation\Table;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\BaseTest;
use Cycle\Annotated\Tests\Fixtures\LabelledInterface;
use Cycle\ORM\Relation;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Cycle\Schema\Relation\Morphed\BelongsToMorphed as BelongsToMorphedRelation;
use Spiral\Annotations\Parser;

abstract class BelongsToMorphedTest extends BaseTest
{
    public function testRelation()
    {
        $p = new Parser();
        $p->register(new Entity());
        $p->register(new Column());
        $p->register(new Table());
        $p->register(new BelongsToMorphed());

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($this->locator, $p),
            new ResetTables(),
            new MergeColumns($p),
            new GenerateRelations(['belongsToMorphed' => new BelongsToMorphedRelation()]),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes($p),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('owner', $schema['label'][Schema::RELATIONS]);
        $this->assertSame(
            Relation::BELONGS_TO_MORPHED,
            $schema['label'][Schema::RELATIONS]['owner'][Relation::TYPE]
        );

        $this->assertSame(
            LabelledInterface::class,
            $schema['label'][Schema::RELATIONS]['owner'][Relation::TARGET]
        );
    }
}