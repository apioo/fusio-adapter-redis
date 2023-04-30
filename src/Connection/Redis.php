<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Fusio\Adapter\Redis\Connection;

use Fusio\Engine\Connection\PingableInterface;
use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Predis\Client;

/**
 * Redis
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org/
 */
class Redis implements ConnectionInterface, PingableInterface
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
            } catch (\RedisException $e) {
            }
        }

        return false;
    }
}
