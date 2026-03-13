<?php

namespace AzozzALFiras\AAPanelAPI\Modules;

/**
 * Scheduled tasks (Cron jobs) management.
 *
 * Covers: /crontab endpoints.
 */
class Cron extends AbstractModule
{
    /**
     * Get list of cron jobs.
     * API: /crontab?action=GetCrontab
     */
    public function getList(int $page = 1, int $limit = 20): array
    {
        return $this->client->post('/crontab?action=GetCrontab', [
            'p'     => $page,
            'limit' => $limit,
        ]);
    }

    /**
     * Add a cron job.
     * API: /crontab?action=AddCrontab
     *
     * @param string $name     Task name
     * @param string $type     Task type: 'day'|'day-n'|'hour'|'hour-n'|'minute-n'|'week'|'month'
     * @param string $hour     Hour
     * @param string $minute   Minute
     * @param string $sBody    Script content or backup target
     * @param string $sType    Script type: 'toShell'|'toUrl'|'toFile'|'database'|'site'|'path'
     * @param string $backupTo Backup target: 'localhost'|'ftp'|...
     * @param string $sName    Name of backup target (database name, site name, etc.)
     * @param int    $save     Number of backups to keep
     * @param string $week     Day of week (for weekly type)
     * @param string $where1   Additional schedule parameter
     */
    public function create(
        string $name,
        string $type = 'day',
        string $hour = '0',
        string $minute = '0',
        string $sBody = '',
        string $sType = 'toShell',
        string $backupTo = 'localhost',
        string $sName = '',
        int $save = 3,
        string $week = '1',
        string $where1 = ''
    ): array {
        return $this->client->post('/crontab?action=AddCrontab', [
            'name'     => $name,
            'type'     => $type,
            'hour'     => $hour,
            'minute'   => $minute,
            'sBody'    => $sBody,
            'sType'    => $sType,
            'backupTo' => $backupTo,
            'sName'    => $sName,
            'save'     => $save,
            'week'     => $week,
            'where1'   => $where1,
        ]);
    }

    /**
     * Delete a cron job.
     * API: /crontab?action=DelCrontab
     */
    public function delete(int $id): array
    {
        return $this->client->post('/crontab?action=DelCrontab', [
            'id' => $id,
        ]);
    }

    /**
     * Start/run a cron job immediately.
     * API: /crontab?action=StartTask
     */
    public function startTask(int $id): array
    {
        return $this->client->post('/crontab?action=StartTask', [
            'id' => $id,
        ]);
    }

    /**
     * Get cron job logs.
     * API: /crontab?action=GetLogs
     */
    public function getLogs(int $id): array
    {
        return $this->client->post('/crontab?action=GetLogs', [
            'id' => $id,
        ]);
    }

    /**
     * Modify cron job remarks.
     * API: /crontab?action=set_cron_status
     */
    public function setStatus(int $id, bool $enabled): array
    {
        return $this->client->post('/crontab?action=set_cron_status', [
            'id'     => $id,
            'status' => $enabled ? '1' : '0',
        ]);
    }
}
