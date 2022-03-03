<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Fusio\Adapter\Redis\Action;

use Fusio\Engine\ContextInterface;
use Fusio\Engine\Exception\ConfigurationException;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;
use PSX\Http\Environment\HttpResponseInterface;
use PSX\Http\Exception\BadRequestException;

/**
 * RedisHashDelete
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/gpl-3.0
 * @link    https://www.fusio-project.org/
 */
class RedisHashDelete extends RedisAbstract
{
    public function getName(): string
    {
        return 'Redis-Hash-Delete';
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): HttpResponseInterface
    {
        $connection = $this->getConnection($configuration);

        $key = $request->get('key');
        if (empty($key)) {
            throw new ConfigurationException('No key provided');
        }

        $field = $request->get('field');
        if (empty($field)) {
            throw new BadRequestException('No field provided');
        }

        if (!is_array($field)) {
            $field = [$field];
        }

        $return = $connection->hdel($key, $field);

        return $this->response->build(200, [], [
            'success' => true,
            'message' => 'Field successfully deleted',
            'return' => $return,
        ]);
    }
}
