<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests;

use Cycle\Annotated\Columns;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Generator;
use Cycle\Annotated\Indexes;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\GenerateTypecast;
use Cycle\Schema\Generator\RenderRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ResetTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class InvalidTest extends BaseTest
{
    /**
     * @expectedException \Cycle\Schema\Exception\RelationException
     */
    public function testInvalidRelation()
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/Fixtures3'],
            'exclude'     => [],
        ]));

        $locator = $tokenizer->classLocator();

        $p = Generator::getDefaultParser();
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $p),
            new ResetTables(),
            new Columns($p),
            new GenerateRelations(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new Indexes($p),
            new SyncTables(),
            new GenerateTypecast(),
        ]);
    }

    /**
     * @expectedException \Cycle\Annotated\Exception\AnnotationException
     */
    public function testInvalidColumn()
    {
        $tokenizer = new Tokenizer(new TokenizerConfig([
            'directories' => [__DIR__ . '/Fixtures4'],
            'exclude'     => [],
        ]));

        $locator = $tokenizer->classLocator();

        $p = Generator::getDefaultParser();
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $p),
            new ResetTables(),
            new Columns($p),
            new GenerateRelations(),
            new ValidateEntities(),
            new RenderTables(),
            new RenderRelations(),
            new Indexes($p),
            new SyncTables(),
            new GenerateTypecast(),
        ]);
    }
}