<?php

namespace AzozzALFiras\AAPanelAPI\Modules\Databases;

use AzozzALFiras\AAPanelAPI\HttpClient;
use AzozzALFiras\AAPanelAPI\Modules\AbstractModule;

/**
 * SQL Server (MSSQL) database management.
 *
 * Covers: /database/sqlserver/* endpoints.
 * Source: github.com/aaPanel/aaPanel/blob/master/class/databaseModel/sqlserverModel.py
 */
class SqlServer extends AbstractModule
{
    use DatabaseCommon;

    protected function getClient(): HttpClient
    {
        return $this->client;
    }

    protected function basePath(): string
    {
        return '/database/sqlserver';
    }

    // ─── List ──────────────────────────────────────────────────

    /**
     * Get list of SQL Server databases.
     * API: /database/sqlserver/get_list
     */
    public function getList(int $limit = 20, int $page = 1, ?string $search = null, string $order = 'id desc', int $sid = 0): array
    {
        $data = [
            'p'     => $page,
            'limit' => $limit,
            'order' => $order,
            'type'  => $sid,
            'sid'   => $sid,
        ];
        if ($search !== null) {
            $data['search'] = $search;
        }
        return $this->client->post('/database/sqlserver/get_list', $data);
    }

    // ─── Create / Delete ───────────────────────────────────────

    /**
     * Create a SQL Server database.
     * API: /database/sqlserver/AddDatabase
     */
    public function create(string $name, string $username, string $password, int $sid = 0, string $remarks = ''): array
    {
        return $this->client->post('/database/sqlserver/AddDatabase', [
            'name'     => $name,
            'db_user'  => $username,
            'password' => $password,
            'sid'      => $sid,
            'ps'       => $remarks,
        ]);
    }

    /**
     * Delete a SQL Server database.
     * API: /database/sqlserver/DeleteDatabase
     */
    public function delete(int $id, string $name): array
    {
        return $this->client->post('/database/sqlserver/DeleteDatabase', [
            'id'   => $id,
            'name' => $name,
        ]);
    }

    /**
     * Pre-delete analysis.
     * API: /database/sqlserver/check_del_data
     */
    public function checkDeleteData(array $ids): array
    {
        return $this->client->post('/database/sqlserver/check_del_data', [
            'ids' => json_encode($ids),
        ]);
    }

    // ─── Password ──────────────────────────────────────────────

    /**
     * Get SA (System Administrator) password.
     * API: /database/sqlserver/get_root_pwd
     */
    public function getRootPassword(): array
    {
        return $this->client->post('/database/sqlserver/get_root_pwd');
    }

    /**
     * Set SA password.
     * API: /database/sqlserver/set_root_pwd
     */
    public function setRootPassword(string $password): array
    {
        return $this->client->post('/database/sqlserver/set_root_pwd', [
            'password' => $password,
        ]);
    }

    /**
     * Change database user/login password.
     * API: /database/sqlserver/ResDatabasePassword
     */
    public function setPassword(int $id, string $name, string $password): array
    {
        return $this->client->post('/database/sqlserver/ResDatabasePassword', [
            'id'       => $id,
            'name'     => $name,
            'password' => $password,
        ]);
    }

    // ─── Size Info ─────────────────────────────────────────────

    /**
     * Get database file size.
     * API: /database/sqlserver/get_database_size_by_id
     */
    public function getDatabaseSize(int $id): array
    {
        return $this->client->post('/database/sqlserver/get_database_size_by_id', [
            'id' => $id,
        ]);
    }
}
