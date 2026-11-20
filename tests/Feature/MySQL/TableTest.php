<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Feature\MySQL;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\Tests\Feature\BaseTestCase;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Spiral\Attributes\AttributeReader;
use Testo\Assert;
use Testo\Test;

/**
 * MySQL-only column options: unsigned, zerofill, charset and collation.
 */
final class TableTest extends BaseTestCase
{
    #[Test]
    public function testUnsigned(): void
    {
        $reader = new AttributeReader();
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        $schema = $r->getTableSchema($r->getEntity('label'));

        Assert::true($schema->column('unsigned')->isUnsigned());
        Assert::false($schema->column('simple')->isUnsigned());

        $schema->save();

        Assert::true($this->dbal->database()->table('labels')->getSchema()->column('unsigned')->isUnsigned());
        Assert::false($this->dbal->database()->table('labels')->getSchema()->column('simple')->isUnsigned());
    }

    #[Test]
    public function testZerofill(): void
    {
        $reader = new AttributeReader();
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        $schema = $r->getTableSchema($r->getEntity('label'));

        Assert::true($schema->column('zerofill')->isZerofill());
        Assert::false($schema->column('simple')->isZerofill());

        $schema->save();

        Assert::true($this->dbal->database()->table('labels')->getSchema()->column('zerofill')->isUnsigned());
        Assert::false($this->dbal->database()->table('labels')->getSchema()->column('simple')->isUnsigned());
    }

    #[Test]
    public function testColumnCustomAttributes(): void
    {
        $reader = new AttributeReader();
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);
        (new SyncTables())->run($r);

        $dbSchema = $this->dbal->database()->table('labels')->getSchema();
        $attributes = $dbSchema->column('charset_column')->getAttributes();

        Assert::same($attributes['charset'], 'ascii');
        Assert::same($attributes['collation'], 'ascii_bin');
    }
}
