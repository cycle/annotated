# Cycle ORM - Annotated Entities
[![Latest Stable Version](https://poser.pugx.org/cycle/annotated/version)](https://packagist.org/packages/cycle/annotated)
[![Build Status](https://travis-ci.org/cycle/annotated.svg?branch=master)](https://travis-ci.org/cycle/annotated)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cycle/annotated/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/cycle/annotated/?branch=master)
[![Codecov](https://codecov.io/gh/cycle/annotated/graph/badge.svg)](https://codecov.io/gh/cycle/annotated)

Example:
--------
```php
/**
 * @entity(repository = "Repository/UserRepository")
 */
class User
{
    /**
     * @column(type=primary)
     * @var int
     */
    protected $id;
    
    /**
     * @hasOne(target=Profile)
     * @var Profile
     */
    protected $profile;
    
    /**
     * @hasMany(target=Post)
     * @var Post[]|Collection
     */
    protected $posts;
   
    /**
     * @manyToMany(target=Tag, though=TagMap)
     */
    protected $tags;
    
    ...
}
```
