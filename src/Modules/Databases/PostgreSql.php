<?php

namespace AzozzALFiras\AAPanelAPI\Modules\Databases;

use AzozzALFiras\AAPanelAPI\HttpClient;
use AzozzALFiras\AAPanelAPI\Modules\AbstractModule;

/**
 * PostgreSQL database management.
 *
 * Covers: /database/pgsql/* endpoints.
 * Source: github.com/aaPanel/aaPanel/blob/master/class/databaseModel/pgsqlModel.py
 */
class PostgreSql extends AbstractModule
{
    use DatabaseCommon;

    protected function getClient(): HttpClient
    {
        return $this->client;
    }

    protected function basePath(): string
    {
        return '/database/pgsql';
    }

    // ─── List ──────────────────────────────────────────────────

    /**
     * Get list of PostgreSQL databases.
     * API: /database/pgsql/get_list
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
        return $this->client->post('/database/pgsql/get_list', $data);
    }

    // ─── Create / Delete ───────────────────────────────────────

    /**
     * Create a PostgreSQL database.
     * API: /database/pgsql/AddDatabase
     */
    public function create(string $name, string $username, string $password, string $codeing = 'UTF8', string $address = '127.0.0.1', int $sid = 0, string $remarks = ''): array
    {
        return $this->client->post('/database/pgsql/AddDatabase', [
            'name'    => $name,
            'db_user' => $username,
            'password' => $password,
            'codeing' => $codeing,
            'address' => $address,
            'sid'     => $sid,
            'ps'      => $remarks,
        ]);
    }

    /**
     * Delete a PostgreSQL database.
     * API: /database/pgsql/DeleteDatabase
     */
    public function delete(int $id, string $name): array
    {
        return $this->client->post('/database/pgsql/DeleteDatabase', [
            'id'   => $id,
            'name' => $name,
        ]);
    }

    // ─── Password ──────────────────────────────────────────────

    /**
     * Get postgres root password.
     * API: /database/pgsql/get_root_pwd
     */
    public function getRootPassword(): array
    {
        return $this->client->post('/database/pgsql/get_root_pwd');
    }

    /**
     * Set postgres root password.
     * API: /database/pgsql/set_root_pwd
     */
    public function setRootPassword(string $password): array
    {
        return $this->client->post('/database/pgsql/set_root_pwd', [
            'password' => $password,
        ]);
    }

    /**
     * Change database user password.
     * API: /database/pgsql/ResDatabasePassword
     */
    public function setPassword(int $id, string $name, string $password): array
    {
        return $this->client->post('/database/pgsql/ResDatabasePassword', [
            'id'       => $id,
            'name'     => $name,
            'password' => $password,
        ]);
    }

    // ─── Server Config ─────────────────────────────────────────

    /**
     * Get PostgreSQL config (port, listen_addresses, etc.).
     * API: /database/pgsql/get_options
     */
    public function getOptions(): array
    {
        return $this->client->post('/database/pgsql/get_options');
    }
}
