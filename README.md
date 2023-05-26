# Cycle ORM - Annotated Entities

[![PHP Version Require](https://poser.pugx.org/cycle/annotated/require/php)](https://packagist.org/packages/cycle/annotated)
[![Latest Stable Version](https://poser.pugx.org/cycle/annotated/v/stable)](https://packagist.org/packages/cycle/annotated)
[![phpunit](https://github.com/cycle/annotated/actions/workflows/main.yml/badge.svg)](https://github.com/cycle/annotated/actions)
[![psalm](https://github.com/cycle/annotated/actions/workflows/psalm.yml/badge.svg)](https://github.com/cycle/annotated/actions)
[![psalm-level](https://shepherd.dev/github/cycle/annotated/level.svg)](https://shepherd.dev/github/cycle/annotated)
[![Codecov](https://codecov.io/gh/cycle/annotated/branch/4.x/graph/badge.svg)](https://codecov.io/gh/cycle/annotated/)
[![Total Downloads](https://poser.pugx.org/cycle/annotated/downloads)](https://packagist.org/packages/cycle/annotated)
<a href="https://discord.gg/8bZsjYhVVk"><img src="https://img.shields.io/badge/discord-chat-magenta.svg"></a>

<b>[Documentation](https://cycle-orm.dev/docs/annotated-prerequisites)</b> | [Cycle ORM](https://github.com/cycle/orm)

The package provides the ability to define Cycle ORM schema using PHP attributes.

## Usage

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

    #[BelongsToMorphed(taget: ImageHolderInterface::class)]
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

## License

The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information. Maintained
by [Spiral Scout](https://spiralscout.com).
