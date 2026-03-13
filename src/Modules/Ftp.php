<?php

namespace AzozzALFiras\AAPanelAPI\Modules;

/**
 * FTP account management.
 *
 * Covers: /ftp, /data?table=ftps endpoints.
 */
class Ftp extends AbstractModule
{
    /**
     * Get list of FTP accounts.
     * API: /data?action=getData&table=ftps
     */
    public function getList(int $limit = 20, int $page = 1, ?string $search = null): array
    {
        $data = [
            'table' => 'ftps',
            'limit' => $limit,
            'p'     => $page,
            'type'  => '-1',
        ];
        if ($search !== null) {
            $data['search'] = $search;
        }
        return $this->client->post('/data?action=getData', $data);
    }

    /**
     * Add FTP account.
     * API: /ftp?action=AddUser
     */
    public function create(string $username, string $password, string $path, ?string $remarks = null): array
    {
        $data = [
            'ftp_username' => $username,
            'ftp_password' => $password,
            'path'         => $path,
        ];
        if ($remarks !== null) {
            $data['ps'] = $remarks;
        }
        return $this->client->post('/ftp?action=AddUser', $data);
    }

    /**
     * Delete FTP account.
     * API: /ftp?action=DeleteUser
     */
    public function delete(int $id, string $username): array
    {
        return $this->client->post('/ftp?action=DeleteUser', [
            'id'       => $id,
            'username' => $username,
        ]);
    }

    /**
     * Get FTP account details.
     * API: /ftp?action=GetUser
     */
    public function getDetails(string $username): array
    {
        return $this->client->post('/ftp?action=GetUser', [
            'user' => $username,
        ]);
    }

    /**
     * Change FTP password.
     * API: /ftp?action=SetUserPassword
     */
    public function setPassword(int $id, string $username, string $newPassword): array
    {
        return $this->client->post('/ftp?action=SetUserPassword', [
            'id'             => $id,
            'ftp_username'   => $username,
            'new_password'   => $newPassword,
        ]);
    }

    /**
     * Enable/Disable FTP account.
     * API: /ftp?action=SetStatus
     */
    public function setStatus(int $id, string $username, bool $enabled): array
    {
        return $this->client->post('/ftp?action=SetStatus', [
            'id'       => $id,
            'username' => $username,
            'status'   => $enabled ? '1' : '0',
        ]);
    }

    /**
     * Modify FTP remarks.
     * API: /data?action=setPs&table=ftps
     */
    public function setRemarks(int $id, string $remarks): array
    {
        return $this->client->post('/data?action=setPs&table=ftps', [
            'id' => $id,
            'ps' => $remarks,
        ]);
    }
}
