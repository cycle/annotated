<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit;

use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\Locator\Embedding;
use Cycle\Annotated\Locator\EmbeddingLocatorInterface;
use Cycle\Annotated\Locator\TokenizerEmbeddingLocator;
use Cycle\Annotated\Tests\Fixtures\Configurator\Address as PostalAddress;
use Cycle\Annotated\Tests\Fixtures\Fixtures29\Address;
use Cycle\Annotated\Tests\Fixtures\Locator\FailingPropertyReader;
use Cycle\Database\Config\DatabaseConfig;
use Cycle\Database\DatabaseManager;
use Cycle\Schema\Registry;
use Cycle\Testing\ORM\Schema\ClassList;
use Spiral\Attributes\AttributeReader;
use Testo\Expect;
use Testo\Test;

#[Test]
final class EmbeddingsTest
{
    public function rejectsARelationInsideAnEmbeddable(): never
    {
        $reader = new AttributeReader();
        $embeddings = new Embeddings(new TokenizerEmbeddingLocator(new ClassList(Address::class), $reader), $reader);

        Expect::exception(AnnotationException::class)
            ->withMessage(\sprintf('Relations are not allowed within embeddable entities in `%s`', Address::class));

        $embeddings->run(new Registry(new DatabaseManager(new DatabaseConfig())));
    }

    public function wrapsAnExceptionOfTheReader(): never
    {
        $previous = new \RuntimeException('broken', 42);
        $locator = new class implements EmbeddingLocatorInterface {
            public function getEmbeddings(): array
            {
                return [new Embedding(new Embeddable(), new \ReflectionClass(PostalAddress::class))];
            }
        };
        $embeddings = new Embeddings($locator, new FailingPropertyReader($previous));

        Expect::exception(AnnotationException::class)->withMessage('broken')->withCode(42)->withPrevious($previous);

        $embeddings->run(new Registry(new DatabaseManager(new DatabaseConfig())));
    }
}
