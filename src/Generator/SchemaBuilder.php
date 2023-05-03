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

namespace Fusio\Adapter\Redis\Generator;

/**
 * SchemaBuilder
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org/
 */
class SchemaBuilder
{
    public function getCollection(): array
    {
        return $this->readSchema(__DIR__ . '/schema/redis/collection.json');
    }

    public function getEntity(): array
    {
        return $this->readSchema(__DIR__ . '/schema/redis/entity.json');
    }

    public function getRequest(): array
    {
        return $this->readSchema(__DIR__ . '/schema/redis/request.json');
    }

    public function getResponse(): array
    {
        return $this->readSchema(__DIR__ . '/schema/redis/response.json');
    }

    private function readSchema(string $file): array
    {
        return \json_decode(\file_get_contents($file), true);
    }
}
