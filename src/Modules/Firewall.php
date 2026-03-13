<?php

namespace AzozzALFiras\AAPanelAPI\Modules;

/**
 * Firewall and security management.
 *
 * Covers: /firewall, /security endpoints.
 */
class Firewall extends AbstractModule
{
    /**
     * Get firewall status and rules.
     * API: /firewall?action=GetList
     */
    public function getList(int $page = 1, int $limit = 20): array
    {
        return $this->client->post('/firewall?action=GetList', [
            'p'     => $page,
            'limit' => $limit,
        ]);
    }

    /**
     * Add firewall port rule.
     * API: /firewall?action=AddDropAddress
     */
    public function addPortRule(string $port, string $type = 'accept', string $ps = ''): array
    {
        return $this->client->post('/firewall?action=AddDropAddress', [
            'port'   => $port,
            'type'   => $type,
            'ps'     => $ps,
        ]);
    }

    /**
     * Delete firewall port rule.
     * API: /firewall?action=DelDropAddress
     */
    public function deletePortRule(int $id): array
    {
        return $this->client->post('/firewall?action=DelDropAddress', [
            'id' => $id,
        ]);
    }

    /**
     * Add IP block/accept rule.
     * API: /firewall?action=AddAcceptPort
     */
    public function addIpRule(string $address, string $type = 'drop', string $ps = ''): array
    {
        return $this->client->post('/firewall?action=AddAcceptPort', [
            'address' => $address,
            'type'    => $type,
            'ps'      => $ps,
        ]);
    }

    /**
     * Set firewall status (on/off).
     * API: /firewall?action=SetFirewallStatus
     */
    public function setStatus(bool $enabled): array
    {
        return $this->client->post('/firewall?action=SetFirewallStatus', [
            'status' => $enabled ? '1' : '0',
        ]);
    }

    /**
     * Set SSH status.
     * API: /firewall?action=SetSshStatus
     */
    public function setSshStatus(bool $enabled): array
    {
        return $this->client->post('/firewall?action=SetSshStatus', [
            'status' => $enabled ? 'true' : 'false',
        ]);
    }

    /**
     * Set ping status.
     * API: /firewall?action=SetPing
     */
    public function setPingStatus(bool $enabled): array
    {
        return $this->client->post('/firewall?action=SetPing', [
            'status' => $enabled ? 'true' : 'false',
        ]);
    }
}
