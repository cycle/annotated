<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\MySQL;

// phpcs:ignore
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\Tests\Functional\Driver\Common\TableTestCase;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use PHPUnit\Framework\Attributes\Group;
use Spiral\Attributes\AttributeReader;

#[\Testo\Filter\Group('driver', 'driver-mysql')]
final class TableTest extends TableTestCase
{
    public const DRIVER = 'mysql';
    #[\Testo\Test]
    public function testUnsigned(): void
    {
        $reader = new AttributeReader();
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        $schema = $r->getTableSchema($r->getEntity('label'));

        \Testo\Assert::true($schema->column('unsigned')->isUnsigned());
        \Testo\Assert::false($schema->column('simple')->isUnsigned());

        $schema->save();

        \Testo\Assert::true($this->dbal->database()->table('labels')->getSchema()->column('unsigned')->isUnsigned());
        \Testo\Assert::false($this->dbal->database()->table('labels')->getSchema()->column('simple')->isUnsigned());
    }
    #[\Testo\Test]
    public function testZerofill(): void
    {
        $reader = new AttributeReader();
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        $schema = $r->getTableSchema($r->getEntity('label'));

        \Testo\Assert::true($schema->column('zerofill')->isZerofill());
        \Testo\Assert::false($schema->column('simple')->isZerofill());

        $schema->save();

        \Testo\Assert::true($this->dbal->database()->table('labels')->getSchema()->column('zerofill')->isUnsigned());
        \Testo\Assert::false($this->dbal->database()->table('labels')->getSchema()->column('simple')->isUnsigned());
    }
    #[\Testo\Test]
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

        \Testo\Assert::same($attributes['charset'], 'ascii');
        \Testo\Assert::same($attributes['collation'], 'ascii_bin');
    }
}
