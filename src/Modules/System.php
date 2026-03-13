<?php

namespace AzozzALFiras\AAPanelAPI\Modules;

/**
 * System status and panel management.
 *
 * Covers: /system, /ajax endpoints.
 */
class System extends AbstractModule
{
    /**
     * Get system basic statistics (OS, CPU, memory, uptime, panel version).
     * API: /system?action=GetSystemTotal
     */
    public function getSystemTotal(): array
    {
        return $this->client->post('/system?action=GetSystemTotal');
    }

    /**
     * Get disk partition information.
     * API: /system?action=GetDiskInfo
     */
    public function getDiskInfo(): array
    {
        return $this->client->post('/system?action=GetDiskInfo');
    }

    /**
     * Get real-time status (CPU, memory, network, load).
     * API: /system?action=GetNetWork
     */
    public function getNetWork(): array
    {
        return $this->client->post('/system?action=GetNetWork');
    }

    /**
     * Check running installation tasks count.
     * API: /ajax?action=GetTaskCount
     */
    public function getTaskCount(): array
    {
        return $this->client->post('/ajax?action=GetTaskCount');
    }

    /**
     * Check for panel updates or perform update.
     * API: /ajax?action=UpdatePanel
     *
     * @param bool $check  Force check for updates
     * @param bool $force  Perform the update
     */
    public function updatePanel(bool $check = true, bool $force = false): array
    {
        return $this->client->post('/ajax?action=UpdatePanel', [
            'check' => $check ? 'true' : 'false',
            'force' => $force ? 'true' : 'false',
        ]);
    }

    /**
     * Get panel logs.
     * API: /data?action=getData&table=logs
     *
     * @param int         $limit  Number of rows
     * @param int         $page   Page number
     * @param string|null $search Search filter
     */
    public function getLogs(int $limit = 10, int $page = 1, ?string $search = null): array
    {
        $data = [
            'table'  => 'logs',
            'limit'  => $limit,
            'p'      => $page,
            'tojs'   => 'get_logs',
        ];
        if ($search !== null) {
            $data['search'] = $search;
        }
        return $this->client->post('/data?action=getData', $data);
    }

    /**
     * Get/set server configuration.
     * API: /server?action=getConfig / /server?action=setConfig
     */
    public function getConfig(): array
    {
        return $this->client->post('/server?action=getConfig');
    }

    public function setConfig(array $configData): array
    {
        return $this->client->post('/server?action=setConfig', [
            'config' => json_encode($configData),
        ]);
    }
}
