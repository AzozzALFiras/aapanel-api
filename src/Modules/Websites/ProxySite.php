<?php

namespace AzozzALFiras\AAPanelAPI\Modules\Websites;

use AzozzALFiras\AAPanelAPI\HttpClient;
use AzozzALFiras\AAPanelAPI\Modules\AbstractModule;

/**
 * Reverse Proxy project management.
 *
 * Covers: /project/proxy/* endpoints.
 * Route pattern: /project/proxy/{method_name}/1
 *
 * Proxy projects forward requests to backend services (Docker, Node, etc.)
 * Currently only supports Nginx as the web server.
 *
 * Features: target URL, caching, gzip, WebSocket, IP restrictions,
 * content replacement, custom headers, SSL, redirects, hotlink protection.
 */
class ProxySite extends AbstractModule
{
    use SiteCommon;

    private const BASE = '/project/proxy';

    protected function getClient(): HttpClient
    {
        return $this->client;
    }

    private function endpoint(string $method): string
    {
        return self::BASE . "/{$method}/1";
    }

    // ─── Project List / Info ───────────────────────────────────

    /**
     * Get list of Proxy projects.
     * API: /project/proxy/get_project_list/1
     */
    public function getList(int $page = 1, int $limit = 20, ?string $search = null): array
    {
        $data = [
            'p'     => $page,
            'limit' => $limit,
        ];
        if ($search !== null) {
            $data['search'] = $search;
        }
        return $this->client->post($this->endpoint('get_project_list'), $data);
    }

    /**
     * Get project info.
     * API: /project/proxy/get_project_info/1
     */
    public function getProjectInfo(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_info'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Get project config.
     * API: /project/proxy/get_project_find/1
     */
    public function getProjectConfig(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_find'), [
            'project_name' => $projectName,
        ]);
    }

    // ─── Create / Delete / Modify ──────────────────────────────

    /**
     * Create a new reverse proxy project.
     * API: /project/proxy/create_project/1
     *
     * @param string $projectName  Project name
     * @param string $domain       Domain to bind
     * @param string $targetUrl    Backend target URL (e.g. http://127.0.0.1:3000)
     * @param string $proxyDir     Proxy directory ('/' for entire site or '/path/')
     * @param string $sendHost     Host header to send ($host, custom domain)
     * @param int    $cache        Enable caching (0=off, 1=on)
     * @param int    $websocket    Enable WebSocket support (0=off, 1=on)
     * @param string $remarks      Project remarks
     */
    public function create(
        string $projectName,
        string $domain,
        string $targetUrl,
        string $proxyDir = '/',
        string $sendHost = '$host',
        int $cache = 0,
        int $websocket = 0,
        string $remarks = ''
    ): array {
        return $this->client->post($this->endpoint('create_project'), [
            'project_name' => $projectName,
            'domain'       => $domain,
            'target_url'   => $targetUrl,
            'proxy_dir'    => $proxyDir,
            'send_host'    => $sendHost,
            'cache'        => $cache,
            'websocket'    => $websocket,
            'ps'           => $remarks,
        ]);
    }

    /**
     * Modify proxy project settings.
     * API: /project/proxy/modify_project/1
     */
    public function modify(string $projectName, array $settings): array
    {
        return $this->client->post($this->endpoint('modify_project'), array_merge(
            ['project_name' => $projectName],
            $settings
        ));
    }

    /**
     * Delete a proxy project.
     * API: /project/proxy/remove_project/1
     */
    public function delete(string $projectName): array
    {
        return $this->client->post($this->endpoint('remove_project'), [
            'project_name' => $projectName,
        ]);
    }

    // ─── Proxy URL Rules ───────────────────────────────────────

    /**
     * Get proxy URL rules.
     * API: /project/proxy/get_proxy_list/1
     */
    public function getProxyRules(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_proxy_list'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Add a proxy URL rule.
     * API: /project/proxy/add_proxy/1
     *
     * @param string $projectName Project name
     * @param string $proxyDir    Directory to proxy ('/' or '/api/')
     * @param string $targetUrl   Backend target URL
     * @param string $sendHost    Host header ($host)
     * @param int    $cache       Enable caching
     * @param int    $websocket   Enable WebSocket
     */
    public function addProxyRule(
        string $projectName,
        string $proxyDir,
        string $targetUrl,
        string $sendHost = '$host',
        int $cache = 0,
        int $websocket = 0
    ): array {
        return $this->client->post($this->endpoint('add_proxy'), [
            'project_name' => $projectName,
            'proxy_dir'    => $proxyDir,
            'target_url'   => $targetUrl,
            'send_host'    => $sendHost,
            'cache'        => $cache,
            'websocket'    => $websocket,
        ]);
    }

    /**
     * Modify a proxy URL rule.
     * API: /project/proxy/modify_proxy/1
     */
    public function modifyProxyRule(string $projectName, string $proxyDir, array $settings): array
    {
        return $this->client->post($this->endpoint('modify_proxy'), array_merge(
            [
                'project_name' => $projectName,
                'proxy_dir'    => $proxyDir,
            ],
            $settings
        ));
    }

    /**
     * Delete a proxy URL rule.
     * API: /project/proxy/remove_proxy/1
     */
    public function deleteProxyRule(string $projectName, string $proxyDir): array
    {
        return $this->client->post($this->endpoint('remove_proxy'), [
            'project_name' => $projectName,
            'proxy_dir'    => $proxyDir,
        ]);
    }

    // ─── Content Replacement ───────────────────────────────────

    /**
     * Set content replacement rules (e.g. replace http:// with https://).
     * API: /project/proxy/set_content_replace/1
     *
     * @param string $projectName Project name
     * @param array  $rules       Array of ['from' => '...', 'to' => '...']
     */
    public function setContentReplace(string $projectName, array $rules): array
    {
        return $this->client->post($this->endpoint('set_content_replace'), [
            'project_name' => $projectName,
            'rules'        => json_encode($rules),
        ]);
    }

    // ─── Custom Headers ────────────────────────────────────────

    /**
     * Set custom proxy headers.
     * API: /project/proxy/set_proxy_header/1
     *
     * @param string $projectName Project name
     * @param string $proxyDir    Proxy directory
     * @param array  $headers     ['Header-Name' => 'value']
     */
    public function setProxyHeaders(string $projectName, string $proxyDir, array $headers): array
    {
        return $this->client->post($this->endpoint('set_proxy_header'), [
            'project_name' => $projectName,
            'proxy_dir'    => $proxyDir,
            'headers'      => json_encode($headers),
        ]);
    }

    // ─── Cache ─────────────────────────────────────────────────

    /**
     * Toggle cache for a proxy.
     * API: /project/proxy/set_proxy_cache/1
     */
    public function setCache(string $projectName, string $proxyDir, bool $enabled): array
    {
        return $this->client->post($this->endpoint('set_proxy_cache'), [
            'project_name' => $projectName,
            'proxy_dir'    => $proxyDir,
            'cache'        => $enabled ? 1 : 0,
        ]);
    }

    /**
     * Clear proxy cache.
     * API: /project/proxy/clear_proxy_cache/1
     */
    public function clearCache(string $projectName): array
    {
        return $this->client->post($this->endpoint('clear_proxy_cache'), [
            'project_name' => $projectName,
        ]);
    }

    // ─── Gzip Compression ──────────────────────────────────────

    /**
     * Set gzip compression settings.
     * API: /project/proxy/set_gzip/1
     *
     * @param string $projectName Project name
     * @param bool   $enabled     Enable/disable
     * @param string $types       File types to compress (e.g. 'text/html text/css application/json')
     */
    public function setGzip(string $projectName, bool $enabled, string $types = ''): array
    {
        return $this->client->post($this->endpoint('set_gzip'), [
            'project_name' => $projectName,
            'gzip'         => $enabled ? 1 : 0,
            'gzip_types'   => $types,
        ]);
    }

    // ─── IP Restrictions ───────────────────────────────────────

    /**
     * Get IP restriction rules.
     * API: /project/proxy/get_ip_restriction/1
     */
    public function getIpRestrictions(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_ip_restriction'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Set IP blacklist (deny).
     * API: /project/proxy/set_ip_blacklist/1
     *
     * @param string $projectName Project name
     * @param string $ips         IP addresses or ranges (newline separated)
     */
    public function setIpBlacklist(string $projectName, string $ips): array
    {
        return $this->client->post($this->endpoint('set_ip_blacklist'), [
            'project_name' => $projectName,
            'ips'          => $ips,
        ]);
    }

    /**
     * Set IP whitelist (allow only).
     * API: /project/proxy/set_ip_whitelist/1
     *
     * @param string $projectName Project name
     * @param string $ips         IP addresses or ranges (newline separated)
     */
    public function setIpWhitelist(string $projectName, string $ips): array
    {
        return $this->client->post($this->endpoint('set_ip_whitelist'), [
            'project_name' => $projectName,
            'ips'          => $ips,
        ]);
    }

    // ─── Domain Management ─────────────────────────────────────

    /**
     * Get project domains.
     * API: /project/proxy/project_get_domain/1
     */
    public function getProjectDomains(string $projectName): array
    {
        return $this->client->post($this->endpoint('project_get_domain'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Add domain to project.
     * API: /project/proxy/project_add_domain/1
     */
    public function addProjectDomain(string $projectName, string $domain): array
    {
        return $this->client->post($this->endpoint('project_add_domain'), [
            'project_name' => $projectName,
            'domain'       => $domain,
        ]);
    }

    /**
     * Remove domain from project.
     * API: /project/proxy/project_remove_domain/1
     */
    public function removeProjectDomain(string $projectName, string $domain, int $port = 80): array
    {
        return $this->client->post($this->endpoint('project_remove_domain'), [
            'project_name' => $projectName,
            'domain'       => $domain,
            'port'         => $port,
        ]);
    }

    // ─── Logs ──────────────────────────────────────────────────

    /**
     * Get proxy access logs.
     * API: /project/proxy/get_project_log/1
     */
    public function getProjectLog(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_log'), [
            'project_name' => $projectName,
        ]);
    }
}
