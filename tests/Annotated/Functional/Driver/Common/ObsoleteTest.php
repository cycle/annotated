<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Functional\Driver\Common;

use Cycle\Annotated\Entities;
use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\SyncTables;
use Cycle\Schema\Registry;
use Spiral\Attributes\ReaderInterface;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

abstract class ObsoleteTest extends BaseTest
{
    /**
     * @dataProvider allReadersProvider
     */
    public function testObsoleteColumn(ReaderInterface $reader): void
    {
        $tokenizer = new Tokenizer(
            new TokenizerConfig([
                'directories' => [__DIR__ . '/../../../Fixtures/Fixtures7'],
                'exclude' => [],
            ])
        );

        $locator = $tokenizer->classLocator();

        $r = new Registry($this->dbal);

        $schema = (new Compiler())->compile($r, [
            new Entities($locator, $reader),
            new RenderTables(),
            new SyncTables(),
        ]);

        $this->assertArrayNotHasKey('skype', $schema['post'][SchemaInterface::COLUMNS]);
        $this->assertArrayNotHasKey('skype', $schema['post'][SchemaInterface::TYPECAST]);
    }
}
