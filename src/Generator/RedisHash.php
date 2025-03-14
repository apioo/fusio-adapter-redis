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

namespace Fusio\Adapter\Redis\Generator;

use Fusio\Adapter\Redis\Action\RedisHashDelete;
use Fusio\Adapter\Redis\Action\RedisHashGet;
use Fusio\Adapter\Redis\Action\RedisHashGetAll;
use Fusio\Adapter\Redis\Action\RedisHashSet;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\Generator\ProviderInterface;
use Fusio\Engine\Generator\SetupInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\Schema\SchemaName;
use Fusio\Model\Backend\ActionConfig;
use Fusio\Model\Backend\ActionCreate;
use Fusio\Model\Backend\OperationCreate;
use Fusio\Model\Backend\SchemaCreate;
use Fusio\Model\Backend\SchemaSource;
use PSX\Json\Parser;

/**
 * RedisHash
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://www.fusio-project.org/
 */
class RedisHash implements ProviderInterface
{
    private const SCHEMA_GET_ALL = 'Redis_GetAll';
    private const SCHEMA_GET = 'Redis_Get';
    private const SCHEMA_SET = 'Redis_Set';
    private const ACTION_GET_ALL = 'Redis_GetAll';
    private const ACTION_GET = 'Redis_Get';
    private const ACTION_SET = 'Redis_Set';
    private const ACTION_DELETE = 'Redis_Delete';

    public function getName(): string
    {
        return 'Redis-Hash';
    }

    public function setup(SetupInterface $setup, ParametersInterface $configuration): void
    {
        $setup->addSchema($this->makeGetAllSchema());
        $setup->addSchema($this->makeGetSchema());
        $setup->addSchema($this->makeSetSchema());

        $setup->addAction($this->makeGetAllAction($configuration));
        $setup->addAction($this->makeGetAction($configuration));
        $setup->addAction($this->makeSetAction($configuration));
        $setup->addAction($this->makeDeleteAction($configuration));

        $setup->addOperation($this->makeGetAllOperation());
        $setup->addOperation($this->makeGetOperation());
        $setup->addOperation($this->makeSetOperation());
        $setup->addOperation($this->makeDeleteOperation());
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newConnection('connection', 'Connection', 'The SQL connection which should be used'));
        $builder->add($elementFactory->newInput('key', 'Key', 'text', 'Name of the key'));
    }

    private function makeGetAllSchema(): SchemaCreate
    {
        $schema = new SchemaCreate();
        $schema->setName(self::SCHEMA_GET_ALL);
        $schema->setSource(SchemaSource::fromObject(Parser::decodeAsObject((string) \file_get_contents(__DIR__ . '/schema/get_all.json'))));
        return $schema;
    }

    private function makeGetSchema(): SchemaCreate
    {
        $schema = new SchemaCreate();
        $schema->setName(self::SCHEMA_GET);
        $schema->setSource(SchemaSource::fromObject(Parser::decodeAsObject((string) \file_get_contents(__DIR__ . '/schema/get.json'))));
        return $schema;
    }

    private function makeSetSchema(): SchemaCreate
    {
        $schema = new SchemaCreate();
        $schema->setName(self::SCHEMA_SET);
        $schema->setSource(SchemaSource::fromObject(Parser::decodeAsObject((string) \file_get_contents(__DIR__ . '/schema/set.json'))));
        return $schema;
    }

    private function makeGetAllAction(ParametersInterface $configuration): ActionCreate
    {
        $action = new ActionCreate();
        $action->setName(self::ACTION_GET_ALL);
        $action->setClass(RedisHashGetAll::class);
        $action->setConfig(ActionConfig::fromArray([
            'connection' => $configuration->get('connection'),
            'key' => $configuration->get('key'),
        ]));
        return $action;
    }

    private function makeGetAction(ParametersInterface $configuration): ActionCreate
    {
        $action = new ActionCreate();
        $action->setName(self::ACTION_GET);
        $action->setClass(RedisHashGet::class);
        $action->setConfig(ActionConfig::fromArray([
            'connection' => $configuration->get('connection'),
            'key' => $configuration->get('key'),
        ]));
        return $action;
    }

    private function makeSetAction(ParametersInterface $configuration): ActionCreate
    {
        $action = new ActionCreate();
        $action->setName(self::ACTION_SET);
        $action->setClass(RedisHashSet::class);
        $action->setConfig(ActionConfig::fromArray([
            'connection' => $configuration->get('connection'),
            'key' => $configuration->get('key'),
        ]));
        return $action;
    }

    private function makeDeleteAction(ParametersInterface $configuration): ActionCreate
    {
        $action = new ActionCreate();
        $action->setName(self::ACTION_DELETE);
        $action->setClass(RedisHashDelete::class);
        $action->setConfig(ActionConfig::fromArray([
            'connection' => $configuration->get('connection'),
            'key' => $configuration->get('key'),
        ]));
        return $action;
    }

    private function makeGetAllOperation(): OperationCreate
    {
        $operation = new OperationCreate();
        $operation->setName('getAll');
        $operation->setDescription('Returns a collection of fields');
        $operation->setHttpMethod('GET');
        $operation->setHttpPath('/');
        $operation->setHttpCode(200);
        $operation->setOutgoing(self::SCHEMA_GET_ALL);
        $operation->setAction(self::ACTION_GET_ALL);
        return $operation;
    }

    private function makeGetOperation(): OperationCreate
    {
        $operation = new OperationCreate();
        $operation->setName('get');
        $operation->setDescription('Returns a single field');
        $operation->setHttpMethod('GET');
        $operation->setHttpPath('/:field');
        $operation->setHttpCode(200);
        $operation->setOutgoing(self::SCHEMA_GET);
        $operation->setAction(self::ACTION_GET);
        return $operation;
    }

    private function makeSetOperation(): OperationCreate
    {
        $operation = new OperationCreate();
        $operation->setName('set');
        $operation->setDescription('Updates an existing field');
        $operation->setHttpMethod('PUT');
        $operation->setHttpPath('/:field');
        $operation->setHttpCode(200);
        $operation->setIncoming(self::SCHEMA_SET);
        $operation->setOutgoing(SchemaName::MESSAGE);
        $operation->setAction(self::ACTION_SET);
        return $operation;
    }

    private function makeDeleteOperation(): OperationCreate
    {
        $operation = new OperationCreate();
        $operation->setName('delete');
        $operation->setDescription('Deletes an existing field');
        $operation->setHttpMethod('DELETE');
        $operation->setHttpPath('/:field');
        $operation->setHttpCode(200);
        $operation->setOutgoing(SchemaName::MESSAGE);
        $operation->setAction(self::ACTION_DELETE);
        return $operation;
    }
}
