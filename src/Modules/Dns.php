<?php

namespace AzozzALFiras\AAPanelAPI\Modules;

/**
 * DNS management (via dns_manager plugin).
 *
 * Covers: /plugin?name=dns_manager endpoints.
 */
class Dns extends AbstractModule
{
    /**
     * Add a DNS record.
     * API: /plugin?action=a&name=dns_manager&s=act_resolve
     *
     * @param string $host   Subdomain/host name
     * @param string $domain Main domain
     * @param string $value  IP address or target
     * @param string $type   Record type (A, AAAA, CNAME, MX, TXT, NS)
     * @param int    $ttl    TTL in seconds
     */
    public function addRecord(string $host, string $domain, string $value, string $type = 'A', int $ttl = 600): array
    {
        return $this->client->post('/plugin?action=a&name=dns_manager&s=act_resolve', [
            'host'   => $host,
            'domain' => $domain,
            'value'  => $value,
            'type'   => $type,
            'ttl'    => $ttl,
            'act'    => 'add',
        ]);
    }

    /**
     * Delete a DNS record.
     * API: /plugin?action=a&name=dns_manager&s=act_resolve
     */
    public function deleteRecord(string $host, string $domain, string $value, string $type = 'A', int $ttl = 600): array
    {
        return $this->client->post('/plugin?action=a&name=dns_manager&s=act_resolve', [
            'host'   => $host,
            'domain' => $domain,
            'value'  => $value,
            'type'   => $type,
            'ttl'    => $ttl,
            'act'    => 'delete',
        ]);
    }

    /**
     * Modify a DNS record.
     */
    public function modifyRecord(string $host, string $domain, string $value, string $type = 'A', int $ttl = 600): array
    {
        return $this->client->post('/plugin?action=a&name=dns_manager&s=act_resolve', [
            'host'   => $host,
            'domain' => $domain,
            'value'  => $value,
            'type'   => $type,
            'ttl'    => $ttl,
            'act'    => 'modify',
        ]);
    }
}
