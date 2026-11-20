<?php

declare(strict_types=1);

namespace Cycle\Annotated\Tests\Unit\Utils;

use Cycle\Annotated\Entities;
use Cycle\Annotated\Exception\AnnotationException;
use Cycle\Annotated\Tests\Fixtures\Fixtures22\Attributed\Person;
use Cycle\Annotated\Utils\EntityUtils;
use Spiral\Attributes\AnnotationReader;
use Spiral\Attributes\AttributeReader;
use Spiral\Attributes\ReaderInterface;
use Testo\Assert;
use Testo\Data\DataProvider;
use Testo\Data\DataSet;
use Testo\Expect;
use Testo\Test;

#[Test]
final class EntityUtilsTest
{
    public static function findParentDataProvider(): iterable
    {
        $namespace = 'Cycle\Annotated\Tests\Fixtures\Fixtures22';
        $readers = [
            'Attributed' => new AttributeReader(),
            'Annotated' => new AnnotationReader(),
        ];

        foreach ($readers as $readerType => $reader) {
            yield "$readerType Child Root"
                => [$reader, "$namespace\\$readerType\\Person", true, null];
            yield "$readerType Child Not Root"
                => [$reader, "$namespace\\$readerType\\Person", false, null];

            yield "$readerType 1st Level Root"
                => [$reader, "$namespace\\$readerType\\Supplier", true, "$namespace\\$readerType\\Person"];
            yield "$readerType 1st Level Not Root"
                => [$reader, "$namespace\\$readerType\\Supplier", false, "$namespace\\$readerType\\Person"];

            yield "$readerType 2nd Level Root"
                => [$reader, "$namespace\\$readerType\\LocalSupplier", true, "$namespace\\$readerType\\Person"];
            yield "$readerType 2nd Level Root No Entity Attribute"
                => [$reader, "$namespace\\$readerType\\LocalSupplier", false, "$namespace\\$readerType\\Person"];

            yield "$readerType 3rd Level Root"
                => [$reader, "$namespace\\$readerType\\LocalManager", true, "$namespace\\$readerType\\Person"];
            yield "$readerType 3rd Level Not Root"
                => [$reader, "$namespace\\$readerType\\LocalManager", false, "$namespace\\$readerType\\LocalSupplier"];
        }
    }

    #[DataProvider('findParentDataProvider')]
    public function testFindParent(
        ReaderInterface $reader,
        string $child,
        bool $root,
        ?string $expectedParent,
    ): void {
        $entityUtils = new EntityUtils($reader);

        $actualParent = $entityUtils->findParent($child, $root);

        Assert::equals($actualParent, $expectedParent);
    }

    #[DataSet([Entities::TABLE_NAMING_PLURAL, 'blog_posts'])]
    #[DataSet([Entities::TABLE_NAMING_SINGULAR, 'blog_post'])]
    #[DataSet([Entities::TABLE_NAMING_NONE, 'blog_posts'])]
    public function testTableName(int $strategy, string $table): void
    {
        $entityUtils = new EntityUtils(new AttributeReader());

        Assert::same($entityUtils->tableName('blogPosts', $strategy), $table);
    }

    public function testGetParentException(): void
    {
        $entityUtils = new EntityUtils(new AttributeReader());

        Expect::exception(AnnotationException::class)->withMessageContaining(\sprintf('The parent class could not be found for the class `%s`.', Person::class));
        $entityUtils->getParent(Person::class);
    }
}
