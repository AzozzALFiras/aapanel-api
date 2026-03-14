<?php

namespace AzozzALFiras\AAPanelAPI\Modules;

use AzozzALFiras\AAPanelAPI\Modules\Databases\Mysql;
use AzozzALFiras\AAPanelAPI\Modules\Databases\PostgreSql;
use AzozzALFiras\AAPanelAPI\Modules\Databases\MongoDb;
use AzozzALFiras\AAPanelAPI\Modules\Databases\Redis;
use AzozzALFiras\AAPanelAPI\Modules\Databases\SqlServer;

/**
 * Database management facade.
 *
 * Provides access to all 5 database types:
 *   - MySQL      → $panel->database()->mysql()
 *   - PostgreSQL → $panel->database()->pgsql()
 *   - MongoDB    → $panel->database()->mongodb()
 *   - Redis      → $panel->database()->redis()
 *   - SQL Server → $panel->database()->sqlserver()
 *
 * Backward-compatible: MySQL methods are available directly
 * (e.g. $panel->database()->getList() delegates to mysql()->getList()).
 */
class Database extends AbstractModule
{
    private $mysql;
    private $pgsql;
    private $mongodb;
    private $redis;
    private $sqlserver;

    /** MySQL management */
    public function mysql(): Mysql
    {
        if ($this->mysql === null) {
            $this->mysql = new Mysql($this->client);
        }
        return $this->mysql;
    }

    /** PostgreSQL management */
    public function pgsql(): PostgreSql
    {
        if ($this->pgsql === null) {
            $this->pgsql = new PostgreSql($this->client);
        }
        return $this->pgsql;
    }

    /** MongoDB management */
    public function mongodb(): MongoDb
    {
        if ($this->mongodb === null) {
            $this->mongodb = new MongoDb($this->client);
        }
        return $this->mongodb;
    }

    /** Redis management */
    public function redis(): Redis
    {
        if ($this->redis === null) {
            $this->redis = new Redis($this->client);
        }
        return $this->redis;
    }

    /** SQL Server management */
    public function sqlserver(): SqlServer
    {
        if ($this->sqlserver === null) {
            $this->sqlserver = new SqlServer($this->client);
        }
        return $this->sqlserver;
    }

    // ─── Backward-compatible shortcuts (delegate to MySQL) ────

    public function getList(int $limit = 20, int $page = 1, ?string $search = null, string $order = 'id desc'): array
    {
        return $this->mysql()->getList($limit, $page, $search, $order);
    }

    public function create(string $name, string $username, string $password, string $codeing = 'utf8mb4', string $address = '127.0.0.1'): array
    {
        return $this->mysql()->create($name, $username, $password, $codeing, $address);
    }

    public function delete(int $id, string $name): array
    {
        return $this->mysql()->delete($id, $name);
    }

    public function setPassword(int $id, string $name, string $password): array
    {
        return $this->mysql()->setPassword($id, $name, $password);
    }

    public function setAccess(string $name, string $access, bool $ssl = false): array
    {
        return $this->mysql()->setAccess($name, $access, $ssl);
    }

    public function getBackups(int $dbId, int $limit = 5, int $page = 1): array
    {
        return $this->client->post('/data?action=getData&table=backup', [
            'search' => $dbId,
            'limit'  => $limit,
            'p'      => $page,
            'type'   => 1,
        ]);
    }

    public function createBackup(int $id): array
    {
        return $this->mysql()->createBackup($id);
    }

    public function deleteBackup(int $id): array
    {
        return $this->mysql()->deleteBackup($id);
    }

    public function importSql(string $file, string $name): array
    {
        return $this->mysql()->importFile($name, $file);
    }

    public function setRemarks(int $id, string $remarks): array
    {
        return $this->mysql()->setRemarks($id, $remarks);
    }
}
