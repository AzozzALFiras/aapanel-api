<?php

namespace AzozzALFiras\AAPanelAPI\Modules\Databases;

use AzozzALFiras\AAPanelAPI\HttpClient;
use AzozzALFiras\AAPanelAPI\Modules\AbstractModule;

/**
 * Redis database management.
 *
 * Covers: /database/redis/* endpoints.
 * Source: github.com/aaPanel/aaPanel/blob/master/class/databaseModel/redisModel.py
 *
 * Redis supports 16 databases (DB0-DB15), key-value operations,
 * backup/restore, and remote server management.
 */
class Redis extends AbstractModule
{
    use DatabaseCommon;

    protected function getClient(): HttpClient
    {
        return $this->client;
    }

    protected function basePath(): string
    {
        return '/database/redis';
    }

    // ─── List / Keys ───────────────────────────────────────────

    /**
     * Get list of Redis databases (with key counts).
     * API: /database/redis/get_list
     */
    public function getList(int $limit = 20, int $page = 1, ?string $search = null, int $sid = 0): array
    {
        $data = [
            'p'     => $page,
            'limit' => $limit,
            'sid'   => $sid,
        ];
        if ($search !== null) {
            $data['search'] = $search;
        }
        return $this->client->post('/database/redis/get_list', $data);
    }

    /**
     * Get paginated key list for a specific database.
     * API: /database/redis/get_db_keylist
     *
     * @param int $dbIndex Database index (0-15)
     */
    public function getKeys(int $dbIndex, int $page = 1, int $limit = 20, ?string $search = null, int $sid = 0): array
    {
        $data = [
            'db_idx' => $dbIndex,
            'p'      => $page,
            'limit'  => $limit,
            'sid'    => $sid,
        ];
        if ($search !== null) {
            $data['search'] = $search;
        }
        return $this->client->post('/database/redis/get_db_keylist', $data);
    }

    // ─── Key Operations ────────────────────────────────────────

    /**
     * Create or modify a key-value pair.
     * API: /database/redis/set_redis_val
     *
     * @param int         $dbIndex Database index (0-15)
     * @param string      $key     Key name
     * @param string      $value   Value content
     * @param string      $type    Data type: 'string', 'list', 'hash', 'set', 'zset'
     * @param int|null    $expiry  TTL in seconds (null = no expiry)
     */
    public function setKey(int $dbIndex, string $key, string $value, string $type = 'string', ?int $expiry = null): array
    {
        $data = [
            'db_idx' => $dbIndex,
            'key'    => $key,
            'value'  => $value,
            'type'   => $type,
        ];
        if ($expiry !== null) {
            $data['expiry'] = $expiry;
        }
        return $this->client->post('/database/redis/set_redis_val', $data);
    }

    /**
     * Delete a specific key.
     * API: /database/redis/del_redis_val
     */
    public function deleteKey(int $dbIndex, string $key, int $sid = 0): array
    {
        return $this->client->post('/database/redis/del_redis_val', [
            'db_idx' => $dbIndex,
            'key'    => $key,
            'sid'    => $sid,
        ]);
    }

    /**
     * Clear one or all Redis databases (FLUSHDB / FLUSHALL).
     * API: /database/redis/clear_flushdb
     *
     * @param int|string $dbIndex Database index (0-15) or 'all' for FLUSHALL
     */
    public function clearDatabase($dbIndex, int $sid = 0): array
    {
        return $this->client->post('/database/redis/clear_flushdb', [
            'db_idx' => $dbIndex,
            'sid'    => $sid,
        ]);
    }

    // ─── Backup ────────────────────────────────────────────────

    /**
     * Get backup file list.
     * API: /database/redis/get_backup_list
     */
    public function getBackupList(int $sid = 0): array
    {
        return $this->client->post('/database/redis/get_backup_list', [
            'sid' => $sid,
        ]);
    }

    // Override trait methods to pass sid
    public function createBackup(int $sid = 0): array
    {
        return $this->client->post('/database/redis/ToBackup', [
            'sid' => $sid,
        ]);
    }

    // ─── Config ────────────────────────────────────────────────

    /**
     * Get Redis configuration settings.
     * API: /database/redis/get_options
     */
    public function getOptions(): array
    {
        return $this->client->post('/database/redis/get_options');
    }
}
