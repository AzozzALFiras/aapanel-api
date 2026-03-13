<?php

namespace AzozzALFiras\AAPanelAPI\Modules\Databases;

use AzozzALFiras\AAPanelAPI\HttpClient;
use AzozzALFiras\AAPanelAPI\Modules\AbstractModule;

/**
 * MySQL database management.
 *
 * Covers: /database?action=*, /data?action=getData&table=databases
 * Source: github.com/aaPanel/aaPanel/blob/master/class/database.py
 *
 * Includes: CRUD, passwords, permissions, backup/restore, sync,
 * table maintenance (repair/optimize/engine), server config, binlog,
 * slow logs, error logs, remote servers, SSL.
 */
class Mysql extends AbstractModule
{
    use DatabaseCommon;

    protected function getClient(): HttpClient
    {
        return $this->client;
    }

    protected function basePath(): string
    {
        return '/database';
    }

    // ─── List / Info ───────────────────────────────────────────

    /**
     * Get list of MySQL databases.
     * API: /data?action=getData&table=databases
     *
     * @param int $type Server ID filter (-1=all, 0=local)
     */
    public function getList(int $limit = 20, int $page = 1, ?string $search = null, string $order = 'id desc', int $type = -1): array
    {
        $data = [
            'table' => 'databases',
            'limit' => $limit,
            'p'     => $page,
            'order' => $order,
            'type'  => $type,
        ];
        if ($search !== null) {
            $data['search'] = $search;
        }
        return $this->client->post('/data?action=getData', $data);
    }

    /**
     * Get database details.
     * API: /database?action=GetInfo
     */
    public function getInfo(int $id): array
    {
        return $this->client->post('/database?action=GetInfo', [
            'id' => $id,
        ]);
    }

    /**
     * Get database overview info.
     * API: /database?action=GetdataInfo
     */
    public function getDataInfo(): array
    {
        return $this->client->post('/database?action=GetdataInfo');
    }

    /**
     * Get database sizes.
     * API: /database?action=get_database_size
     */
    public function getDatabaseSize(array $ids): array
    {
        return $this->client->post('/database?action=get_database_size', [
            'ids'    => json_encode($ids),
            'is_pid' => false,
        ]);
    }

    // ─── Create / Delete ───────────────────────────────────────

    /**
     * Create a MySQL database.
     * API: /database?action=AddDatabase
     *
     * @param string $name     Database name
     * @param string $username Database username
     * @param string $password Database password
     * @param string $codeing  Character set (utf8, utf8mb4, gbk, big5)
     * @param string $address  Access permission ('127.0.0.1'|'%'|specific IP)
     * @param int    $sid      Server ID (0=local)
     * @param string $remarks  Description
     */
    public function create(string $name, string $username, string $password, string $codeing = 'utf8mb4', string $address = '127.0.0.1', int $sid = 0, string $remarks = ''): array
    {
        return $this->client->post('/database?action=AddDatabase', [
            'name'       => $name,
            'db_user'    => $username,
            'password'   => $password,
            'codeing'    => $codeing,
            'address'    => $address,
            'dtype'      => 'MySQL',
            'sid'        => $sid,
            'ps'         => $remarks,
        ]);
    }

    /**
     * Delete a MySQL database.
     * API: /database?action=DeleteDatabase
     */
    public function delete(int $id, string $name): array
    {
        return $this->client->post('/database?action=DeleteDatabase', [
            'id'   => $id,
            'name' => $name,
        ]);
    }

    /**
     * Pre-delete analysis (sizes/scores).
     * API: /database?action=check_del_data
     */
    public function checkDeleteData(array $ids): array
    {
        return $this->client->post('/database?action=check_del_data', [
            'ids' => json_encode($ids),
        ]);
    }

    // ─── Password Management ───────────────────────────────────

    /**
     * Change MySQL root password.
     * API: /database?action=SetupPassword
     */
    public function setRootPassword(string $password, int $sid = 0): array
    {
        return $this->client->post('/database?action=SetupPassword', [
            'password' => $password,
            'sid'      => $sid,
        ]);
    }

    /**
     * Change database user password.
     * API: /database?action=ResDatabasePassword
     */
    public function setPassword(int $id, string $name, string $password): array
    {
        return $this->client->post('/database?action=ResDatabasePassword', [
            'id'       => $id,
            'name'     => $name,
            'password' => $password,
        ]);
    }

    // ─── Access Permissions ────────────────────────────────────

    /**
     * Get database access permissions.
     * API: /database?action=GetDatabaseAccess
     */
    public function getAccess(string $name): array
    {
        return $this->client->post('/database?action=GetDatabaseAccess', [
            'name' => $name,
        ]);
    }

    /**
     * Set database access permissions.
     * API: /database?action=SetDatabaseAccess
     *
     * @param string $name   Database/user name
     * @param string $access '127.0.0.1' (local), '%' (all), or specific IP
     * @param bool   $ssl    Force SSL connection
     */
    public function setAccess(string $name, string $access, bool $ssl = false): array
    {
        return $this->client->post('/database?action=SetDatabaseAccess', [
            'name'   => $name,
            'access' => $access,
            'ssl'    => $ssl ? '1' : '0',
        ]);
    }

    // ─── Table Maintenance ─────────────────────────────────────

    /**
     * Repair database tables.
     * API: /database?action=ReTable
     */
    public function repairTable(string $dbName): array
    {
        return $this->client->post('/database?action=ReTable', [
            'db_name' => $dbName,
        ]);
    }

    /**
     * Optimize database tables.
     * API: /database?action=OpTable
     */
    public function optimizeTable(string $dbName): array
    {
        return $this->client->post('/database?action=OpTable', [
            'db_name' => $dbName,
        ]);
    }

    /**
     * Convert table storage engine (InnoDB <-> MyISAM).
     * API: /database?action=AlTable
     */
    public function convertEngine(string $dbName, string $tableName, string $engine): array
    {
        return $this->client->post('/database?action=AlTable', [
            'db_name' => $dbName,
            'table'   => $tableName,
            'engine'  => $engine,
        ]);
    }

    // ─── MySQL Server Config ───────────────────────────────────

    /**
     * Get MySQL server info (version, datadir, port).
     * API: /database?action=GetMySQLInfo
     */
    public function getMySQLInfo(): array
    {
        return $this->client->post('/database?action=GetMySQLInfo');
    }

    /**
     * Get MySQL config parameters (memory, connections, etc.).
     * API: /database?action=GetDbStatus
     */
    public function getDbStatus(): array
    {
        return $this->client->post('/database?action=GetDbStatus');
    }

    /**
     * Set MySQL performance parameters.
     * API: /database?action=SetDbConf
     */
    public function setDbConf(array $config): array
    {
        return $this->client->post('/database?action=SetDbConf', $config);
    }

    /**
     * Get MySQL runtime stats (connections, queries).
     * API: /database?action=GetRunStatus
     */
    public function getRunStatus(): array
    {
        return $this->client->post('/database?action=GetRunStatus');
    }

    /**
     * Change MySQL listening port.
     * API: /database?action=SetMySQLPort
     */
    public function setPort(int $port): array
    {
        return $this->client->post('/database?action=SetMySQLPort', [
            'port' => $port,
        ]);
    }

    /**
     * Relocate MySQL data directory.
     * API: /database?action=SetDataDir
     */
    public function setDataDir(string $datadir): array
    {
        return $this->client->post('/database?action=SetDataDir', [
            'datadir' => $datadir,
        ]);
    }

    // ─── Binary Logs ───────────────────────────────────────────

    /**
     * Toggle binary logging or get status.
     * API: /database?action=BinLog
     */
    public function binLog(?string $status = null): array
    {
        $data = [];
        if ($status !== null) {
            $data['status'] = $status;
        }
        return $this->client->post('/database?action=BinLog', $data);
    }

    /**
     * List binary log files.
     * API: /database?action=GetMySQLBinlogs
     */
    public function getBinlogs(): array
    {
        return $this->client->post('/database?action=GetMySQLBinlogs');
    }

    /**
     * Purge binary logs older than N days.
     * API: /database?action=ClearMySQLBinlog
     */
    public function clearBinlogs(int $days): array
    {
        return $this->client->post('/database?action=ClearMySQLBinlog', [
            'days' => $days,
        ]);
    }

    // ─── Logs ──────────────────────────────────────────────────

    /**
     * Get MySQL error log.
     * API: /database?action=GetErrorLog
     *
     * @param bool $clear Pass true to clear the log
     */
    public function getErrorLog(bool $clear = false): array
    {
        $data = [];
        if ($clear) {
            $data['close'] = 1;
        }
        return $this->client->post('/database?action=GetErrorLog', $data);
    }

    /**
     * Get slow query logs.
     * API: /database?action=GetSlowLogs
     */
    public function getSlowLogs(): array
    {
        return $this->client->post('/database?action=GetSlowLogs');
    }

    // ─── SSL ───────────────────────────────────────────────────

    /**
     * Check MySQL SSL status.
     * API: /database?action=check_mysql_ssl_status
     */
    public function checkSslStatus(): array
    {
        return $this->client->post('/database?action=check_mysql_ssl_status');
    }

    /**
     * Enable SSL for MySQL.
     * API: /database?action=write_ssl_to_mysql
     */
    public function enableSsl(): array
    {
        return $this->client->post('/database?action=write_ssl_to_mysql');
    }

    // ─── User Info ─────────────────────────────────────────────

    /**
     * Get MySQL user details.
     * API: /database?action=get_mysql_user
     */
    public function getMysqlUser(): array
    {
        return $this->client->post('/database?action=get_mysql_user');
    }
}
