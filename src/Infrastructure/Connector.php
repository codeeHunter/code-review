<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure;

use Raketa\BackendTestTask\Domain\Cart;
use Redis;
use RedisException;

class Connector
{
    private Redis $redis;
    // При необходимости вынести в отдельный словарь/константы
    private const ONE_DAY_IN_SECONDS = 24 * 60 * 60;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Получить Cart по ключу
     * @throws ConnectorException
     */
    public function get(string $key): ?Cart
    {
        try {
            $data = $this->redis->get($key);
            if ($data === false || $data === null) {
                return null;
            }
            return unserialize($data);
        } catch (RedisException $e) {
            throw new ConnectorException('Connector error on get()', $e->getCode(), $e);
        }
    }

    /**
     * Сохранить Cart в Redis c TTL=1 день
     * @throws ConnectorException
     */
    public function set(string $key, Cart $value): void
    {
        try {
            $this->redis->setex($key, self::ONE_DAY_IN_SECONDS, serialize($value));
        } catch (RedisException $e) {
            throw new ConnectorException('Connector error on set()', $e->getCode(), $e);
        }
    }

    public function has(string $key): bool
    {
        return $this->redis->exists($key) > 0;
    }
}
