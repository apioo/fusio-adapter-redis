<?php

use Fusio\Adapter\Redis\Action\RedisHashDelete;
use Fusio\Adapter\Redis\Action\RedisHashGet;
use Fusio\Adapter\Redis\Action\RedisHashGetAll;
use Fusio\Adapter\Redis\Action\RedisHashSet;
use Fusio\Adapter\Redis\Connection\Redis;
use Fusio\Adapter\Redis\Generator\RedisHash;
use Fusio\Engine\Adapter\ServiceBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = ServiceBuilder::build($container);
    $services->set(Redis::class);
    $services->set(RedisHashDelete::class);
    $services->set(RedisHashGet::class);
    $services->set(RedisHashGetAll::class);
    $services->set(RedisHashSet::class);
    $services->set(RedisHash::class);
};
