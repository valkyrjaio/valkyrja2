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

namespace Valkyrja\Orm\Retrievers;

use JsonException;
use Valkyrja\Orm\Constants\Statement;
use Valkyrja\Orm\Entity;
use Valkyrja\Type\Support\Arr;

/**
 * Class Retriever.
 *
 * @author   Melech Mizrachi
 */
class LocalCacheRetriever extends Retriever
{
    /**
     * Local cache for results in case same query is made multiple times within the same session.
     */
    protected static array $localCache = [];

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function getResult(): array
    {
        $localCacheKey = $this->getCacheKey();

        if (isset(self::$localCache[$localCacheKey])) {
            return self::$localCache[$localCacheKey];
        }

        $this->prepareResults();

        /** @var Entity[] $results */
        $results = $this->query->getResult();

        self::$localCache[$localCacheKey] = $results;

        return $results;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    public function getCount(): int
    {
        $this->columns([Statement::COUNT_ALL]);

        $localCacheKey = $this->getCacheKey();

        if (isset(self::$localCache[$localCacheKey])) {
            return self::$localCache[$localCacheKey];
        }

        $this->prepareResults();

        $count = $this->query->getCount();

        self::$localCache[$localCacheKey] = $count;

        return $count;
    }

    /**
     * Get the cache key.
     *
     * @throws JsonException
     */
    protected function getCacheKey(): string
    {
        return $this->queryBuilder->getQueryString() . Arr::toString($this->values);
    }
}
