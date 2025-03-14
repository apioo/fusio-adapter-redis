<?php
/*
 * Fusio is an open source API management platform which helps to create innovative API solutions.
 * For the current version and information visit <https://www.fusio-project.org/>
 *
 * Copyright 2015-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace Fusio\Adapter\Redis\Connection;

use Fusio\Engine\Connection\PingableInterface;
use Fusio\Engine\ConnectionAbstract;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Predis\Client;
use Predis\PredisException;

/**
 * Redis
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://www.fusio-project.org/
 */
class Redis extends ConnectionAbstract implements PingableInterface
{
    public function getName(): string
    {
        return 'Redis';
    }

    public function getConnection(ParametersInterface $config): Client
    {
        $port = $config->get('port');
        if (!empty($port)) {
            $port = (int) $port;
        } else {
            $port = 6379;
        }

        return new Client([
            'scheme' => 'tcp',
            'host'   => $config->get('host'),
            'port'   => $port,
        ]);
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newInput('host', 'Host', 'text', 'Host of the redis server'));
        $builder->add($elementFactory->newInput('port', 'Port', 'text', 'Port of the redis server'));
    }

    public function ping(mixed $connection): bool
    {
        if ($connection instanceof Client) {
            try {
                $connection->ping();

                return true;
            } catch (PredisException $e) {
            }
        }

        return false;
    }
}
