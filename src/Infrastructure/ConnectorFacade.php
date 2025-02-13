<?php

declare(strict_types=1);

namespace Raketa\BackendTestTask\Infrastructure;

use Redis;
use RedisException;

class ConnectorFacade
{
    protected string $host;
    protected int $port = 6379;
    protected ?string $password = null;
    protected ?int $dbindex = null;

    protected ?Connector $connector = null;

    public function __construct(
        string $host,
        int $port,
        ?string $password,
        ?int $dbindex
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->dbindex = $dbindex;
    }

    protected function build(): void
    {
        $redis = new Redis();
        try {
            // Попытка коннекта
            $isConnected = $redis->connect($this->host, $this->port);
            if ($isConnected && $this->password) {
                $redis->auth($this->password);
            }
            if ($isConnected && $this->dbindex !== null) {
                $redis->select($this->dbindex);
            }
            $this->connector = new Connector($redis);
        } catch (RedisException $e) {
            // Логируем или обрабатываем
            // Для упрощения оставим пустым
        }
    }

    public function getConnector(): ?Connector
    {
        return $this->connector;
    }
}
