<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Relation;

use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\Tests\BaseTest;
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
use Cycle\Schema\Relation\ManyToMany as ManyToManyRelation;

abstract class ManyToManyTest extends BaseTest
{
    public function testRelation(): void
    {
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($this->locator),
            new ResetTables(),
            new MergeColumns(),
            new GenerateRelations(),
            new RenderTables(),
            new RenderRelations(),
            new MergeIndexes(),
            new SyncTables(),
            new GenerateTypecast(),
        ]);

        $this->assertArrayHasKey('tags', $schema['withTable'][Schema::RELATIONS]);

        $this->assertSame(
            Relation::MANY_TO_MANY,
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::TYPE]
        );

        $this->assertSame(
            'tag',
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::TARGET]
        );

        $this->assertSame(
            'tagContext',
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::THROUGH_ENTITY]
        );

        $this->assertSame(
            ["id" => [">=" => "1"]],
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::WHERE]
        );
        $this->assertSame(
            ["id" => "DESC"],
            $schema['withTable'][Schema::RELATIONS]['tags'][Relation::SCHEMA][Relation::ORDER_BY]
        );
    }
}
