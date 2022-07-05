# Cycle ORM - Annotated Entities

[![Latest Stable Version](https://poser.pugx.org/cycle/annotated/version)](https://packagist.org/packages/cycle/annotated)
[![Build Status](https://github.com/cycle/annotated/workflows/build/badge.svg)](https://github.com/cycle/annotated/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cycle/annotated/badges/quality-score.png?b=3.x)](https://scrutinizer-ci.com/g/cycle/annotated/?branch=3.x)
[![Codecov](https://codecov.io/gh/cycle/annotated/graph/badge.svg)](https://codecov.io/gh/cycle/annotated)

## Simple example:

#### Annotation definition

```php
/**
 * @Entity(
 *     role="user",
 *     repository="Repository/UserRepository",
 *     typecast="Typecast\AutoTypecaster"
 * )
 */
class User
{
    /** @Column(type="primary") */
    protected $id;
    
    /** @HasOne(target=Profile::class, load="eager") */
    protected $profile;
    
    /** @HasMany(target=Post::class, load="lazy") */
    protected $posts;
   
    /** 
     * @ManyToMany(
     *     target=Tag::class, 
     *     through=TagMap::class, 
     *     load="lazy", 
     *     collection="Collection\BaseCollection"
     * )
     */
    protected $tags;
    
    ...
}
```

#### Attribute definition

```php
#[Entity(
    role: "user", 
    repository: Repository/UserRepository::class, 
    typecast: Typecast\Typecaster::class
)]
class User
{
    #[Column(type: 'primary')]
    protected $id;
    
    #[HasOne(target: Profile::class, load: "eager")]
    protected $profile;
    
    #[HasMany(target: Post::class, load: "lazy")]
    protected $posts;
   
    #[ManyToMany(
        target: Tag::class, 
        through: TagMap::class, 
        load: "lazy", 
        collection: Collection\BaseCollection::class
    )]
    protected $tags;
    
    ...
}
```

## STI/JTI:

#### Single Table Inheritance

```php
#[Entity]
#[DiscriminatorColumn(name: 'type')] // Discriminator column (required)
class Person
{
    #[Column(type: 'primary', primary: true)]
    protected int $id;

    #[Column(type: 'string')]
    protected string $name;
}

#[Entity]
#[InheritanceSingleTable]
class Employee extends Person
{
    #[Column(type: 'int')]
    protected int $salary;
}

#[Entity]
#[InheritanceSingleTable(value: 'foo_customer')]
class Customer extends Person
{
    #[Column(type: 'json')]
    protected array $preferences;
}
```

#### Joined Table Inheritance

```php
#[Entity]
class Person
{
    #[Column(primary: true)]
    protected int $id;
    
    #[Column()]
    protected int $fooId;

    #[Column(type: 'string')]
    protected string $name;
}

#[Entity]
#[InheritanceJoinedTable(outerKey: 'fooId')]
class Employee extends Person
{
    #[Column(type: 'int')]
    protected int $salary;
}

#[Entity]
#[InheritanceJoinedTable(outerKey: 'id')]
class Customer extends Person
{
    #[Column(type: 'json')]
    protected array $preferences;
}
```

#### Combined example

```php
#[Entity]
#[DiscriminatorColumn(name: 'type')]
class Person
{
    #[Column(type: 'primary', primary: true)]
    protected int $id;

    #[Column(type: 'string')]
    protected string $name;
}

#[Entity]
#[InheritanceSingleTable]
class Employee extends Person
{
    #[Column(type: 'int')]
    protected int $salary;
}

#[Entity]
#[InheritanceSingleTable(value: 'foo_customer')]
class Customer extends Person
{
    #[Column(type: 'json')]
    protected array $preferences;
}

#[Entity]
#[InheritanceJoinedTable(outerKey: 'foo_id')]
class Executive extends Employee
{
    #[Column(type: 'int')]
    protected int $bonus;
}
```

## Schema modifiers:

#### Schema modifier example

```php
namespace App\SchemaModifiers;

use Cycle\ORM\SchemaInterface;
use Cycle\Schema\Registry;
use Cycle\Schema\SchemaModifierInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
class MapperSegmentSchemaModifier implements SchemaModifierInterface
{
    private string $role;
        
    public function __construct(
        private string $class
    ) {
    }

    public function withRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function compute(Registry $registry): void 
    {
        // ...
    }

    public function render(Registry $registry): void 
    {
        // ...
    }

    public function modifySchema(array &$schema): void
    {
        $schema[SchemaInterface::MAPPER] = $this->class;
    }
}
```

#### Usage

```php
namespace App\Entities;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use App\SchemaModifiers\MapperSegmentSchemaModifier;

#[Entity]
#[MapperSegmentSchemaModifier(class: SuperMapper::class)]
class Post
{
    #[Column(type: 'integer', primary: true)]
    protected int $id;

    #[Column(type: 'string')]
    protected string $name;
}
```

License:
--------
The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information. Maintained
by [Spiral Scout](https://spiralscout.com).
