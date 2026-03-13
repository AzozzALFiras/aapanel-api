<?php

namespace AzozzALFiras\AAPanelAPI\Modules\Databases;

use AzozzALFiras\AAPanelAPI\HttpClient;

/**
 * Shared database features across all DB types (MySQL, PgSQL, MongoDB, SQLServer, Redis).
 *
 * Covers: backup/restore, sync, remote (cloud) server management, remarks.
 */
trait DatabaseCommon
{
    abstract protected function getClient(): HttpClient;

    /** Return the base endpoint path (e.g. '/database', '/database/pgsql') */
    abstract protected function basePath(): string;

    // ─── Backup & Restore ──────────────────────────────────────

    /**
     * Create database backup.
     */
    public function createBackup(int $id): array
    {
        return $this->getClient()->post($this->basePath() . '/ToBackup', [
            'id' => $id,
        ]);
    }

    /**
     * Delete a backup file.
     */
    public function deleteBackup(int $id): array
    {
        return $this->getClient()->post($this->basePath() . '/DelBackup', [
            'id' => $id,
        ]);
    }

    /**
     * Import/restore from file.
     *
     * @param string $name Database name
     * @param string $file Path to backup file on server
     */
    public function importFile(string $name, string $file): array
    {
        return $this->getClient()->post($this->basePath() . '/InputSql', [
            'name' => $name,
            'file' => $file,
        ]);
    }

    // ─── Sync ──────────────────────────────────────────────────

    /**
     * Sync aaPanel DB list to database server.
     *
     * @param int   $type 0=selected, 1=all
     * @param array $ids  Database IDs to sync (when type=0)
     */
    public function syncToDatabases(int $type = 1, array $ids = []): array
    {
        return $this->getClient()->post($this->basePath() . '/SyncToDatabases', [
            'type' => $type,
            'ids'  => json_encode($ids),
        ]);
    }

    /**
     * Import existing databases from server into aaPanel.
     *
     * @param int $sid Server ID (0 = local)
     */
    public function syncGetDatabases(int $sid = 0): array
    {
        return $this->getClient()->post($this->basePath() . '/SyncGetDatabases', [
            'sid' => $sid,
        ]);
    }

    // ─── Remote (Cloud) Server Management ──────────────────────

    /**
     * Register a remote database server.
     */
    public function addCloudServer(string $host, int $port, string $user, string $password, string $remarks = ''): array
    {
        return $this->getClient()->post($this->basePath() . '/AddCloudServer', [
            'db_host'     => $host,
            'db_port'     => $port,
            'db_user'     => $user,
            'db_password' => $password,
            'db_ps'       => $remarks,
        ]);
    }

    /**
     * List remote database servers.
     */
    public function getCloudServers(): array
    {
        return $this->getClient()->post($this->basePath() . '/GetCloudServer');
    }

    /**
     * Delete a remote server config.
     */
    public function removeCloudServer(int $id): array
    {
        return $this->getClient()->post($this->basePath() . '/RemoveCloudServer', [
            'id' => $id,
        ]);
    }

    /**
     * Update remote server config.
     */
    public function modifyCloudServer(int $id, string $host, int $port, string $user, string $password, string $remarks = ''): array
    {
        return $this->getClient()->post($this->basePath() . '/ModifyCloudServer', [
            'id'          => $id,
            'db_host'     => $host,
            'db_port'     => $port,
            'db_user'     => $user,
            'db_password' => $password,
            'db_ps'       => $remarks,
        ]);
    }

    /**
     * Test remote server connectivity.
     */
    public function checkCloudStatus(array $connConfig): array
    {
        return $this->getClient()->post($this->basePath() . '/check_cloud_database_status', [
            'conn_config' => json_encode($connConfig),
        ]);
    }

    // ─── Remarks ───────────────────────────────────────────────

    /**
     * Update database remarks.
     */
    public function setRemarks(int $id, string $remarks): array
    {
        return $this->getClient()->post('/data?action=setPs&table=databases', [
            'id' => $id,
            'ps' => $remarks,
        ]);
    }
}
