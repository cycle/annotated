<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests;

use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Registry;
use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Spiral\Attributes\ReaderInterface;

abstract class TypecastTest extends BaseTest
{
    /**
     * @dataProvider allReadersProvider
     */
    public function testEntityWithDefinedTypecastAsString(ReaderInterface $reader)
    {
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($this->locator, $reader),
            new MergeColumns($reader),
        ]);

        $this->assertSame(
            Fixtures\Typecast\Typecaster::class,
            $schema['simple'][Schema::TYPECAST_HANDLER]
        );
    }

    /**
     * @dataProvider allReadersProvider
     */
    public function testEntityWithDefinedTypecastAsArray(ReaderInterface $reader)
    {
        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($this->locator, $reader),
            new MergeColumns($reader),
        ]);

        $this->assertSame(
            [
                Fixtures\Typecast\Typecaster::class,
                Fixtures\Typecast\UuidTypecaster::class,
                'foo',
            ],
            $schema['tag'][Schema::TYPECAST_HANDLER]
        );
    }
}
