# Cacher

Cacher is a plugin for CakePHP that allows you to easily cache find results.
While most solutions for caching queries force you to overwrite `Model::find()`
in your AppModel, Cacher only requires adding a behavior to your model.

Have settings that hardly change? Have a database list of states or something
that never change but you still want them in the db? Just like caching your
results? Use Cacher!

## Usage

    var $actsAs = array(
        'Cacher.Cache'
    );

You can send any options you would normally use in `Cache::config()`. By default
Cacher caches results for 6 hours. You can change this by passing `duration`
with your duration as specified in `Cache::config()`. If you already have a
Cache configuration that you'd like to use:

    var $actsAs = array(
        'Cacher.Cache' => array(
            'config' => 'myCacheConfiguration'
        )
    );

## How it works

Cacher caches the query results under the cache configuration's path, under the
model's alias. The default path is `CACHE.'cacher'`, so if you cache a find
result for your Post model, it would store under `app/tmp/cache/cacher/post`.

It does this by intercepting any find query and changing the datasource to one
that handle's the database read. Your datasource is reset after the find is
complete.

You can always disable Cacher by using `Behavior::detach()` or
`Behavior::disable()`.

## Features

* Quick and easy caching by just attaching the behavior to a model
* Clear cache for a specific model on the fly using `$this->Post->clearCache()`
* Clear a specific query by passing the conditions to `clearCache()`

## Todo

* I'd like to add other caching functionality to make it more all-in-one
* Need to add tests that show it works with any DataSource
* Make it hash based on more than just the conditions (i.e., if you cached
  pagination results it wouldn't know the difference because limit isn't taken
  into account)