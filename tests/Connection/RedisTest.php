<?php
/*
 * Fusio - Self-Hosted API Management for Builders.
 * For the current version and information visit <https://www.fusio-project.org/>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Fusio\Adapter\Redis\Tests\Connection;

use Fusio\Adapter\Redis\Connection\Redis;
use Fusio\Adapter\Redis\Tests\RedisTestCase;
use Fusio\Engine\Form\Builder;
use Fusio\Engine\Form\Container;
use Fusio\Engine\Form\Element\Input;
use Fusio\Engine\Parameters;
use Predis\Client;

/**
 * RedisTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://www.fusio-project.org/
 */
class RedisTest extends RedisTestCase
{
    public function testGetConnection()
    {
        $connectionFactory = $this->getConnectionFactory()->factory(Redis::class);

        $config = new Parameters([
            'host' => '127.0.0.1',
            'port' => '6379',
        ]);

        $connection = $connectionFactory->getConnection($config);

        $this->assertInstanceOf(Client::class, $connection);

        $connection->set('foo', 'bar');

        $this->assertEquals('bar', $connection->get('foo'));
    }

    public function testConfigure()
    {
        $connection = $this->getConnectionFactory()->factory(Redis::class);
        $builder    = new Builder();
        $factory    = $this->getFormElementFactory();

        $connection->configure($builder, $factory);

        $this->assertInstanceOf(Container::class, $builder->getForm());

        $elements = $builder->getForm()->getElements();
        $this->assertEquals(2, count($elements));
        $this->assertInstanceOf(Input::class, $elements[0]);
        $this->assertInstanceOf(Input::class, $elements[1]);
    }

    public function testPing()
    {
        /** @var Redis $connectionFactory */
        $connectionFactory = $this->getConnectionFactory()->factory(Redis::class);

        $config = new Parameters([
            'host' => '127.0.0.1',
            'port' => '6379',
        ]);

        $connection = $connectionFactory->getConnection($config);

        $this->assertTrue($connectionFactory->ping($connection));
    }
}
