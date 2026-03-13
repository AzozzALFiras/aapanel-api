<?php

namespace AzozzALFiras\AAPanelAPI\Modules\Databases;

use AzozzALFiras\AAPanelAPI\HttpClient;
use AzozzALFiras\AAPanelAPI\Modules\AbstractModule;

/**
 * MongoDB database management.
 *
 * Covers: /database/mongodb/* endpoints.
 * Source: github.com/aaPanel/aaPanel/blob/master/class/databaseModel/mongodbModel.py
 */
class MongoDb extends AbstractModule
{
    use DatabaseCommon;

    protected function getClient(): HttpClient
    {
        return $this->client;
    }

    protected function basePath(): string
    {
        return '/database/mongodb';
    }

    // ─── List ──────────────────────────────────────────────────

    /**
     * Get list of MongoDB databases.
     * API: /database/mongodb/get_list
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
        return $this->client->post('/database/mongodb/get_list', $data);
    }

    // ─── Create / Delete ───────────────────────────────────────

    /**
     * Create a MongoDB database.
     * API: /database/mongodb/AddDatabase
     */
    public function create(string $name, string $password, int $sid = 0, string $remarks = ''): array
    {
        return $this->client->post('/database/mongodb/AddDatabase', [
            'name'     => $name,
            'password' => $password,
            'sid'      => $sid,
            'ps'       => $remarks,
        ]);
    }

    /**
     * Delete a MongoDB database.
     * API: /database/mongodb/DeleteDatabase
     */
    public function delete(int $id, string $name): array
    {
        return $this->client->post('/database/mongodb/DeleteDatabase', [
            'id'   => $id,
            'name' => $name,
        ]);
    }

    // ─── Password & Auth ───────────────────────────────────────

    /**
     * Get MongoDB root/admin password.
     * API: /database/mongodb/get_root_pwd
     */
    public function getRootPassword(): array
    {
        return $this->client->post('/database/mongodb/get_root_pwd');
    }

    /**
     * Change database user password.
     * API: /database/mongodb/ResDatabasePassword
     */
    public function setPassword(int $id, string $name, string $password): array
    {
        return $this->client->post('/database/mongodb/ResDatabasePassword', [
            'id'       => $id,
            'name'     => $name,
            'password' => $password,
        ]);
    }

    /**
     * Enable or disable MongoDB authentication.
     * API: /database/mongodb/set_auth_status
     *
     * @param bool   $enabled  Enable/disable auth
     * @param string $password Admin password (required when enabling)
     */
    public function setAuthStatus(bool $enabled, string $password = ''): array
    {
        return $this->client->post('/database/mongodb/set_auth_status', [
            'status'   => $enabled ? 'enable' : 'disable',
            'password' => $password,
        ]);
    }

    /**
     * Check if a database exists.
     * API: /database/mongodb/exists_databases
     */
    public function exists(string $dbName): array
    {
        return $this->client->post('/database/mongodb/exists_databases', [
            'db_name' => $dbName,
        ]);
    }
}
