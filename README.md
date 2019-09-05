# Cycle ORM - Annotated Entities
[![Latest Stable Version](https://poser.pugx.org/cycle/annotated/version)](https://packagist.org/packages/cycle/annotated)
[![Build Status](https://travis-ci.org/cycle/annotated.svg?branch=master)](https://travis-ci.org/cycle/annotated)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cycle/annotated/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/cycle/annotated/?branch=master)
[![Codecov](https://codecov.io/gh/cycle/annotated/graph/badge.svg)](https://codecov.io/gh/cycle/annotated)

Example:
--------
```php
/**
 * @Entity(repository = "Repository/UserRepository")
 */
class User
{
    /** @Column(type="primary") */
    protected $id;
    
    /** @HasOne(target=Profile::class, load="eager") */
    protected $profile;
    
    /** @HasMany(target=Post::class, load="lazy") */
    protected $posts;
   
    /** @ManyToMany(target=Tag::class, though=TagMap::class, load="lazy") */
    protected $tags;
    
    ...
}
```
