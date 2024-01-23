<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\MySQL;

// phpcs:ignore
use Cycle\Annotated\Entities;
use Cycle\Annotated\Locator\TokenizerEntityLocator;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\Tests\Functional\Driver\Common\TableTestCase;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Registry;
use PHPUnit\Framework\Attributes\Group;
use Spiral\Attributes\AttributeReader;

#[Group('driver')]
#[Group('driver-mysql')]
final class TableTest extends TableTestCase
{
    public const DRIVER = 'mysql';

    public function testUnsigned(): void
    {
        $reader = new AttributeReader();
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        $schema = $r->getTableSchema($r->getEntity('label'));

        $this->assertTrue($schema->column('unsigned')->isUnsigned());
        $this->assertFalse($schema->column('simple')->isUnsigned());

        $schema->save();

        $this->assertTrue($this->dbal->database()->table('labels')->getSchema()->column('unsigned')->isUnsigned());
        $this->assertFalse($this->dbal->database()->table('labels')->getSchema()->column('simple')->isUnsigned());
    }

    public function testZerofill(): void
    {
        $reader = new AttributeReader();
        $r = new Registry($this->dbal);
        (new Entities(new TokenizerEntityLocator($this->locator, $reader), $reader))->run($r);
        (new MergeColumns($reader))->run($r);
        (new RenderTables())->run($r);

        $schema = $r->getTableSchema($r->getEntity('label'));

        $this->assertTrue($schema->column('zerofill')->isZerofill());
        $this->assertFalse($schema->column('simple')->isZerofill());

        $schema->save();

        $this->assertTrue($this->dbal->database()->table('labels')->getSchema()->column('zerofill')->isUnsigned());
        $this->assertFalse($this->dbal->database()->table('labels')->getSchema()->column('simple')->isUnsigned());
    }
}
