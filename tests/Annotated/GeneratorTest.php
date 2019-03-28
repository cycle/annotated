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
use Cycle\Annotated\GenerateEntities;
use Cycle\Annotated\Tests\Fixtures\Complete;
use Cycle\Annotated\Tests\Fixtures\CompleteMapper;
use Cycle\Annotated\Tests\Fixtures\Constrain\SomeConstrain;
use Cycle\Annotated\Tests\Fixtures\Repository\CompleteRepository;
use Cycle\Annotated\Tests\Fixtures\Simple;
use Cycle\Annotated\Tests\Fixtures\Source\TestSource;
use Cycle\ORM\Mapper\Mapper;
use Cycle\ORM\Select\Repository;
use Cycle\Schema\Registry;
use Spiral\Annotations\Parser;

abstract class GeneratorTest extends BaseTest
{
    public function testSimpleSchema()
    {
        $p = new Parser();
        $p->register(new Entity());
        $p->register(new Column());

        $r = new Registry($this->dbal);
        $g = new GenerateEntities($this->locator, $p);
        $g->run($r);

        $this->assertTrue($r->hasEntity(Simple::class));
        $this->assertTrue($r->hasEntity('simple'));


        $this->assertSame(Mapper::class, $r->getEntity('simple')->getMapper());
        $this->assertSame(Repository::class, $r->getEntity('simple')->getRepository());

        $this->assertTrue($r->hasTable($r->getEntity('simple')));
        $this->assertSame('default', $r->getDatabase($r->getEntity('simple')));
        $this->assertSame('simples', $r->getTable($r->getEntity('simple')));

        $this->assertTrue($r->getEntity('simple')->getFields()->has('id'));
        $this->assertSame('id', $r->getEntity('simple')->getFields()->get('id')->getColumn());
    }

    public function testCompleteSchema()
    {
        $p = new Parser();
        $p->register(new Entity());
        $p->register(new Column());

        $r = new Registry($this->dbal);
        $g = new GenerateEntities($this->locator, $p);
        $g->run($r);

        $this->assertTrue($r->hasEntity(Complete::class));
        $this->assertTrue($r->hasEntity('eComplete'));

        $this->assertSame(CompleteMapper::class, $r->getEntity('eComplete')->getMapper());
        $this->assertSame(CompleteRepository::class, $r->getEntity('eComplete')->getRepository());
        $this->assertSame(TestSource::class, $r->getEntity('eComplete')->getSource());
        $this->assertSame(SomeConstrain::class, $r->getEntity('eComplete')->getConstrain());

        $this->assertTrue($r->hasTable($r->getEntity('eComplete')));
        $this->assertSame('secondary', $r->getDatabase($r->getEntity('eComplete')));
        $this->assertSame('complete_data', $r->getTable($r->getEntity('eComplete')));

        $this->assertTrue($r->getEntity('eComplete')->getFields()->has('id'));
        $this->assertTrue($r->getEntity('eComplete')->getFields()->has('name'));

        $this->assertSame(
            'username',
            $r->getEntity('eComplete')->getFields()->get('name')->getColumn()
        );

        $this->assertFalse($r->getEntity('eComplete')->getFields()->has('ignored'));
    }
}