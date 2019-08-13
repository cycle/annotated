<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Tests\Fixtures\Child;
use Cycle\Annotated\Tests\Fixtures\Simple;
use Cycle\Annotated\Tests\Fixtures\Third;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Registry;
use Doctrine\Common\Annotations\AnnotationReader;

abstract class ChildTest extends BaseTest
{
    public function testSimpleSchema()
    {
        $r = new Registry($this->dbal);
        (new Entities($this->locator, new AnnotationReader()))->run($r);

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