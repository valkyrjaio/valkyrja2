<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja Framework package.
 *
 * (c) Melech Mizrachi <melechmizrachi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\Api;

use Exception;
use Valkyrja\Http\JsonResponse;
use Valkyrja\Orm\Entity;

/**
 * Interface Api.
 *
 * @author Melech Mizrachi
 */
interface Api
{
    /**
     * Make a new JSON model from an exception.
     */
    public function jsonFromException(Exception $exception): Json;

    /**
     * Make a new JSON response from an exception.
     */
    public function jsonResponseFromException(Exception $exception): JsonResponse;

    /**
     * Make a new JSON model from an object.
     */
    public function jsonFromObject(object $object): Json;

    /**
     * Make a new JSON model from an object.
     */
    public function jsonResponseFromObject(object $object): JsonResponse;

    /**
     * Make a new JSON model from an array of objects.
     */
    public function jsonFromObjects(object ...$objects): Json;

    /**
     * Make a new JSON response from an array of objects.
     */
    public function jsonResponseFromObjects(object ...$objects): JsonResponse;

    /**
     * Make a new JSON model from an array.
     */
    public function jsonFromArray(array $array): Json;

    /**
     * Make a new JSON model from an array.
     */
    public function jsonResponseFromArray(array $array): JsonResponse;

    /**
     * Make a new JSON model from an entity.
     */
    public function jsonFromEntity(Entity $entity): Json;

    /**
     * Make a new JSON response from an entity.
     */
    public function jsonResponseFromEntity(Entity $entity): JsonResponse;

    /**
     * Make a new JSON model from an array of entities.
     */
    public function jsonFromEntities(Entity ...$entities): Json;

    /**
     * Make a new JSON response from an array of entities.
     */
    public function jsonResponseFromEntities(Entity ...$entities): JsonResponse;
}
