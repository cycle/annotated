<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Entities;
use Cycle\Annotated\Tests\Fixtures\Child;
use Cycle\Annotated\Tests\Fixtures\Simple;
use Cycle\Annotated\Tests\Fixtures\Third;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Registry;
use Spiral\Annotations\Parser;

abstract class ChildTest extends BaseTest
{
    public function testSimpleSchema()
    {
        $p = new Parser();
        $p->register(new Entity());
        $p->register(new Column());

        $r = new Registry($this->dbal);
        (new Entities($this->locator, $p))->run($r);

        $this->assertTrue($r->hasEntity(Simple::class));
        $this->assertTrue($r->hasEntity('simple'));

        $this->assertTrue($r->getEntity('simple')->getFields()->has('id'));

        $this->assertTrue($r->getEntity('simple')->getFields()->has('name'));
        $this->assertTrue($r->getEntity('simple')->getFields()->has('email'));

        $schema = (new Compiler())->compile($r);

        $this->assertSame([Schema::ROLE => 'simple'], $schema[Child::class]);
        $this->assertSame([Schema::ROLE => 'simple'], $schema[Third::class]);
    }
}