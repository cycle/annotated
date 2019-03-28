<?php declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Cycle\Annotated\Tests;

use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Generator;
use Cycle\Schema\Registry;
use Spiral\Annotations\Parser;

abstract class GeneratorTest extends BaseTest
{
    public function testLocateClasses()
    {
        $p = new Parser();
        $p->register(new Entity());

        $r = new Registry($this->dbal);
        $g = new Generator($this->locator, $p);
        $g->run($r);
    }
}