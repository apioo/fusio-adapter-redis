<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace Fusio\Adapter\Redis\Routes;

use Fusio\Adapter\Redis\Action\RedisHashDelete;
use Fusio\Adapter\Redis\Action\RedisHashGet;
use Fusio\Adapter\Redis\Action\RedisHashGetAll;
use Fusio\Adapter\Redis\Action\RedisHashSet;
use Fusio\Engine\Factory\Resolver\PhpClass;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\Routes\ProviderInterface;
use Fusio\Engine\Routes\SetupInterface;

/**
 * RedisHash
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org/
 */
class RedisHash implements ProviderInterface
{
    private SchemaBuilder $schemaBuilder;

    public function __construct()
    {
        $this->schemaBuilder = new SchemaBuilder();
    }

    public function getName(): string
    {
        return 'Redis-Hash';
    }

    public function setup(SetupInterface $setup, string $basePath, ParametersInterface $configuration): void
    {
        $prefix = $this->getPrefix($basePath);

        $schemaCollection = $setup->addSchema('Redis_Hash_Collection', $this->schemaBuilder->getCollection());
        $schemaEntity = $setup->addSchema('Redis_Hash_Entity', $this->schemaBuilder->getEntity());
        $schemaRequest = $setup->addSchema('Redis_Hash_Request', $this->schemaBuilder->getRequest());
        $schemaResponse = $setup->addSchema('Redis_Hash_Response', $this->schemaBuilder->getResponse());

        $fetchAllAction = $setup->addAction($prefix . '_Redis_All', RedisHashGetAll::class, PhpClass::class, [
            'connection' => $configuration->get('connection'),
            'key' => $configuration->get('key'),
        ]);

        $fetchRowAction = $setup->addAction($prefix . '_Redis_Row', RedisHashGet::class, PhpClass::class, [
            'connection' => $configuration->get('connection'),
            'key' => $configuration->get('key'),
        ]);

        $deleteAction = $setup->addAction($prefix . '_Delete', RedisHashDelete::class, PhpClass::class, [
            'connection' => $configuration->get('connection'),
            'key' => $configuration->get('key'),
        ]);

        $updateAction = $setup->addAction($prefix . '_Update', RedisHashSet::class, PhpClass::class, [
            'connection' => $configuration->get('connection'),
            'key' => $configuration->get('key'),
        ]);

        $setup->addRoute(1, '/', 'Fusio\Impl\Controller\SchemaApiController', [], [
            [
                'version' => 1,
                'methods' => [
                    'GET' => [
                        'active' => true,
                        'public' => true,
                        'description' => 'Returns a collection of fields',
                        'responses' => [
                            200 => $schemaCollection,
                        ],
                        'action' => $fetchAllAction,
                    ],
                ],
            ]
        ]);

        $setup->addRoute(1, '/:id', 'Fusio\Impl\Controller\SchemaApiController', [], [
            [
                'version' => 1,
                'methods' => [
                    'GET' => [
                        'active' => true,
                        'public' => true,
                        'description' => 'Returns a single field',
                        'responses' => [
                            200 => $schemaEntity,
                        ],
                        'action' => $fetchRowAction,
                    ],
                    'PUT' => [
                        'active' => true,
                        'public' => false,
                        'description' => 'Updates an existing field',
                        'request' => $schemaRequest,
                        'responses' => [
                            200 => $schemaResponse,
                        ],
                        'action' => $updateAction,
                    ],
                    'DELETE' => [
                        'active' => true,
                        'public' => false,
                        'description' => 'Deletes an existing field',
                        'responses' => [
                            200 => $schemaResponse,
                        ],
                        'action' => $deleteAction,
                    ]
                ],
            ]
        ]);
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newConnection('connection', 'Connection', 'The SQL connection which should be used'));
        $builder->add($elementFactory->newInput('key', 'Key', 'text', 'Name of the key'));
    }

    private function getPrefix(string $path): string
    {
        return implode('_', array_map('ucfirst', array_filter(explode('/', $path))));
    }
}
