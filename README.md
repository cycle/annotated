# Cycle ORM - Annotated Entities
[![Latest Stable Version](https://poser.pugx.org/cycle/annotated/version)](https://packagist.org/packages/cycle/annotated)
[![Build Status](https://github.com/cycle/annotated/workflows/build/badge.svg)](https://github.com/cycle/annotated/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cycle/annotated/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/cycle/annotated/?branch=master)
[![Codecov](https://codecov.io/gh/cycle/annotated/graph/badge.svg)](https://codecov.io/gh/cycle/annotated)

Example:
--------

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
   
    /** @ManyToMany(target=Tag::class, through=TagMap::class, load="lazy", collection="Collection\BaseCollection") */
    protected $tags;
    
    ...
}
```

#### Attribute definition

```php
#[Entity(role: "user", repository: Repository/UserRepository::class, typecast: Typecast\Typecaster::class)]
class User
{
    #[Column(type: 'primary')]
    protected $id;
    
    #[HasOne(target: Profile::class, load: "eager")]
    protected $profile;
    
    #[HasMany(target: Post::class, load: "lazy")]
    protected $posts;
   
    #[ManyToMany(target: Tag::class, through: TagMap::class, load: "lazy", collection: Collection\BaseCollection::class)]
    protected $tags;
    
    ...
}
```

License:
--------
The MIT License (MIT). Please see [`LICENSE`](./LICENSE) for more information. Maintained by [Spiral Scout](https://spiralscout.com).
