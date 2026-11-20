<div align="center">
    <a href="https://cycle-orm.dev" target="_blank">
        <picture>
            <source media="(prefers-color-scheme: dark)" srcset="https://github.com/cycle/.github/blob/main/logo/words-vector-dark.svg?raw=true">
            <img width="50%" align="center" src="https://github.com/cycle/.github/blob/main/logo/words-vector-light.svg?raw=true" alt="CycleORM Logo">
        </picture>
    </a>
</div>

<p align="center">Cycle ORM schema from PHP attributes</p>

<div align="center">

[![Documentation](https://img.shields.io/badge/Documentation-blue?style=for-the-badge&logo=gitbook&logoColor=white)](https://cycle-orm.dev/docs/annotated-prerequisites)
[![Sponsor](https://img.shields.io/static/v1?style=for-the-badge&label=&message=Sponsor&logo=githubsponsors&logoColor=white&color=%23EA4AAA)](https://github.com/sponsors/cycle)
[![Discord](https://img.shields.io/discord/538114875570913290?style=for-the-badge&logo=discord&labelColor=7289d9&logoColor=white&color=39456d)](https://discord.gg/spiralphp)

[![Psalm Level](https://shepherd.dev/github/cycle/annotated/level.svg)](https://shepherd.dev/github/cycle/annotated)
[![Type Coverage](https://shepherd.dev/github/cycle/annotated/coverage.svg)](https://shepherd.dev/github/cycle/annotated)
[![Codecov](https://codecov.io/gh/cycle/annotated/branch/4.x/graph/badge.svg)](https://codecov.io/gh/cycle/annotated)

</div>

<br />

The package lets you define the [Cycle ORM](https://github.com/cycle/orm) schema with PHP attributes: entities, columns, indexes, relations, embeddings and table inheritance are declared on the classes themselves, and the package compiles them into the ORM schema.

## Get Started

### Installation

```bash
composer require cycle/annotated
```

[![PHP](https://img.shields.io/packagist/php-v/cycle/annotated.svg?style=flat-square&logo=php)](https://packagist.org/packages/cycle/annotated)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/cycle/annotated.svg?style=flat-square&logo=packagist)](https://packagist.org/packages/cycle/annotated)
[![License](https://img.shields.io/packagist/l/cycle/annotated.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/cycle/annotated.svg?style=flat-square)](https://packagist.org/packages/cycle/annotated/stats)

The package requires PHP 8.2+.

### Usage

```php
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;

#[Entity]
class User
{
    #[Column(type: 'primary')]
    private int $id;

    #[Column(type: 'string(32)')]
    private string $login;

    #[Column(type: 'enum(active,disabled)')]
    private string $status;

    #[Column(type: 'decimal(5,5)')]
    private $balance;
}
```

### Relations

#### HasOne

```php
use Cycle\Annotated\Annotation\Relation\HasOne;
use Cycle\Annotated\Annotation\Entity;

#[Entity]
class User
{
    // ...

    #[HasOne(target: Address::class)]
    public ?Address $address;
}

```

> **Note**
> Read more about [HasOne](https://cycle-orm.dev/docs/relation-has-one).

#### HasMany

```php
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\HasMany;

#[Entity]
class User
{
    // ...

    #[HasMany(target: Post::class)]
    private array $posts;
}
```

> **Note**
> Read more about [HasMany](https://cycle-orm.dev/docs/relation-has-many).

#### BelongsTo

```php
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

#[Entity]
class Post
{
    // ...

    #[BelongsTo(target: User::class)]
    private User $user;
}
```

> **Note**
> Read more about [BelongsTo](https://cycle-orm.dev/docs/relation-belongs-to).

#### RefersTo

```php
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\RefersTo;
use Cycle\Annotated\Annotation\Relation\HasMany;

#[Entity]
class User
{
    // ...

    #[RefersTo(target: Comment::class)]
    private ?Comment $lastComment;

    #[HasMany(target: Comment::class)]
    public array $comments;

    // ...

    public function addComment(Comment $c): void
    {
        $this->lastComment = $c;
        $this->comments[] = $c;
    }
    
    public function removeLastComment(): void
    {
        $this->lastComment = null;
    }
    
    public function getLastComment(): ?Comment
    {
        return $this->lastComment;
    }
}
```

> **Note**
> Read more about [RefersTo](https://cycle-orm.dev/docs/relation-refers-to).

#### ManyToMany

```php
use Cycle\Annotated\Annotation\Relation\ManyToMany;
use Cycle\Annotated\Annotation\Entity;

#[Entity]
class User
{
    // ...

    #[ManyToMany(target: Tag::class, through: UserTag::class)]
    protected array $tags;
    
    public function getTags(): array
    {
        return $this->tags;
    }
    
    public function addTag(Tag $tag): void
    {
        $this->tags[] = $tag;
    }
    
    public function removeTag(Tag $tag): void
    {
        $this->tags = array_filter($this->tags, static fn(Tag $t) => $t !== $tag);
    }
}
```

> **Note**
> Read more about [ManyToMany](https://cycle-orm.dev/docs/relation-many-to-many).

#### Embedded Entities

```php
use Cycle\Annotated\Annotation\Embeddable;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Embedded;

#[Embeddable]
class UserCredentials
{
    #[Column(type: 'string(255)')]
    public string $username;

    #[Column(type: 'string')]
    public string $password;
}

#[Entity]
class User
{
    #[Column(type: 'primary')]
    public int $id;

    #[Embedded(target: 'UserCredentials')]
    public UserCredentials $credentials;

    public function __construct()
    {
        $this->credentials = new UserCredentials();
    }
}
```

> **Note**
> Read more about [Embedded Entities](https://cycle-orm.dev/docs/relation-embedded).

#### BelongsToMorphed

```php
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Morphed\BelongsToMorphed;

#[Entity]
class Image
{
    // ...

    #[BelongsToMorphed(target: ImageHolderInterface::class)]
    public ImageHolderInterface $imageHolder;
}
```

> **Note**
> Read more about [BelongsToMorphed](https://cycle-orm.dev/docs/relation-morphed#belongstomorphed).

#### MorphedHasOne

```php
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Morphed\MorphedHasOne;

#[Entity]
class User
{
    // ...

    #[MorphedHasOne(target: Image::class)]
    public $image;
}
```

> **Note**
> Read more about [MorphedHasOne](https://cycle-orm.dev/docs/relation-morphed#morphedhasone).

#### MorphedHasMany

```php
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Relation\Morphed\MorphedHasMany;

#[Entity]
class User
{
    // ...

    #[MorphedHasMany(target: Image::class)]
    public $images;
}
```

> **Note**
> Read more about [MorphedHasMany](https://cycle-orm.dev/docs/relation-morphed#morphedhasmany).

### Single Table Inheritance

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

> **Note**
> Read more about [Single Table Inheritance](https://cycle-orm.dev/docs/advanced-single-table-inheritance).

### Joined Table Inheritance

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

> **Note**
> Read more about [Joined Table Inheritance](https://cycle-orm.dev/docs/advanced-joined-table-inheritance).

## Agent skills

Cycle Annotated comes with the `cycle-orm-attributes` [Agent Skill](https://agentskills.io) from [cycle/skills](https://github.com/cycle/skills): it teaches coding agents such as Claude Code and Codex to map PHP classes to tables with attributes — entities, columns and typecasts, relations including polymorphic ones, STI/JTI inheritance, embeddables, indexes and foreign keys, and entity behaviors such as timestamps, soft delete, optimistic lock, hooks and UUIDs. Install the [llm/skills](https://github.com/roxblnfk/skills) Composer plugin, and it copies the skill into `.agents/skills/` on every `composer install` and `update`:

```bash
composer require --dev llm/skills
```

Querying and persisting the mapped entities is covered by the `cycle-orm` skill that comes with [cycle/orm](https://github.com/cycle/orm).

## Testing

The tests run on [Testo](https://github.com/php-testo/testo) through the shared [cycle/testing](https://github.com/cycle/testing) harness: `tests/Annotated/Unit` needs no database, and `tests/Annotated/Feature` runs once per database driver.

```bash
composer test:unit         # no database
composer test:sqlite       # in-memory SQLite, no docker
composer db:up             # MySQL, Postgres and SQL Server in docker
composer test              # every suite; a driver whose server is down is skipped
composer db:down
```

`composer test:mysql`, `test:postgres` and `test:sqlserver` run one driver.
