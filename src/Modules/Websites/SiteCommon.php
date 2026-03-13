<?php

namespace AzozzALFiras\AAPanelAPI\Modules\Websites;

use AzozzALFiras\AAPanelAPI\HttpClient;

/**
 * Shared website features across all project types (PHP, Node, Python, Proxy).
 *
 * Covers: SSL, domains, logs, redirects, hotlink protection, password access,
 * traffic limits, default documents, rewrite rules, backups, site config files.
 */
trait SiteCommon
{
    abstract protected function getClient(): HttpClient;

    // ─── SSL ───────────────────────────────────────────────────

    /**
     * Get SSL details for a site.
     * API: /site?action=GetSSL
     */
    public function getSSL(string $siteName): array
    {
        return $this->getClient()->post('/site?action=GetSSL', [
            'siteName' => $siteName,
        ]);
    }

    /**
     * Set/deploy SSL certificate.
     * API: /site?action=SetSSL
     *
     * @param int    $type 1=custom, 2=Let's Encrypt
     */
    public function setSSL(string $siteName, string $key, string $csr, int $type = 1): array
    {
        return $this->getClient()->post('/site?action=SetSSL', [
            'type'     => $type,
            'siteName' => $siteName,
            'key'      => $key,
            'csr'      => $csr,
        ]);
    }

    /**
     * Close/remove SSL.
     * API: /site?action=CloseSSLConf
     */
    public function closeSSL(int $siteId, string $siteName): array
    {
        return $this->getClient()->post('/site?action=CloseSSLConf', [
            'updateOf' => $siteId,
            'siteName' => $siteName,
        ]);
    }

    /**
     * Enable HTTPS forced redirect.
     * API: /site?action=HttpToHttps
     */
    public function enableHttps(string $siteName): array
    {
        return $this->getClient()->post('/site?action=HttpToHttps', [
            'siteName' => $siteName,
        ]);
    }

    /**
     * Disable HTTPS forced redirect.
     * API: /site?action=HttpsToHttp
     */
    public function disableHttps(string $siteName): array
    {
        return $this->getClient()->post('/site?action=HttpsToHttp', [
            'siteName' => $siteName,
        ]);
    }

    // ─── Domains ───────────────────────────────────────────────

    /**
     * Get list of domains for a site.
     * API: /data?action=getData&table=domain
     */
    public function getDomains(int $siteId): array
    {
        return $this->getClient()->post('/data?action=getData&table=domain', [
            'search' => $siteId,
            'list'   => 'true',
        ]);
    }

    /**
     * Add domain to a site.
     * API: /site?action=AddDomain
     */
    public function addDomain(int $id, string $webname, string $domain): array
    {
        return $this->getClient()->post('/site?action=AddDomain', [
            'id'      => $id,
            'webname' => $webname,
            'domain'  => $domain,
        ]);
    }

    /**
     * Delete domain from a site.
     * API: /site?action=DelDomain
     */
    public function deleteDomain(int $id, string $webname, string $domain, int $port = 80): array
    {
        return $this->getClient()->post('/site?action=DelDomain', [
            'id'      => $id,
            'webname' => $webname,
            'domain'  => $domain,
            'port'    => $port,
        ]);
    }

    // ─── Redirect (301/302) ────────────────────────────────────

    /**
     * Get redirect rules for a site.
     * API: /site?action=GetRedirectList
     */
    public function getRedirects(string $siteName): array
    {
        return $this->getClient()->post('/site?action=GetRedirectList', [
            'siteName' => $siteName,
        ]);
    }

    /**
     * Create a redirect rule.
     * API: /site?action=CreateRedirect
     *
     * @param string $siteName      Site name
     * @param string $type          'domain' or 'path'
     * @param string $redirectDomain Domain/path to redirect from
     * @param string $toUrl         Target URL
     * @param int    $redirectCode  301 or 302
     * @param int    $holdPath      Keep path (1) or not (0)
     */
    public function createRedirect(string $siteName, string $type, string $redirectDomain, string $toUrl, int $redirectCode = 301, int $holdPath = 1): array
    {
        return $this->getClient()->post('/site?action=CreateRedirect', [
            'siteName'  => $siteName,
            'type'      => $type,
            'domainorpath' => $redirectDomain,
            'toUrl'     => $toUrl,
            'redirecttype' => $redirectCode,
            'holdpath'  => $holdPath,
        ]);
    }

    /**
     * Delete a redirect rule.
     * API: /site?action=DeleteRedirect
     */
    public function deleteRedirect(string $siteName, string $redirectName): array
    {
        return $this->getClient()->post('/site?action=DeleteRedirect', [
            'siteName' => $siteName,
            'name'     => $redirectName,
        ]);
    }

    // ─── Reverse Proxy (site-level) ───────────────────────────

    /**
     * Get reverse proxy list for a site.
     * API: /site?action=GetProxyList
     */
    public function getProxyList(string $siteName): array
    {
        return $this->getClient()->post('/site?action=GetProxyList', [
            'siteName' => $siteName,
        ]);
    }

    /**
     * Create a reverse proxy.
     * API: /site?action=CreateProxy
     */
    public function createProxy(string $siteName, string $proxyName, string $proxyDir, string $proxyUrl, string $proxyHost = '$host', int $cache = 0, int $subfilter = 0): array
    {
        return $this->getClient()->post('/site?action=CreateProxy', [
            'siteName'   => $siteName,
            'proxyname'  => $proxyName,
            'proxydir'   => $proxyDir,
            'proxysite'  => $proxyUrl,
            'todomain'   => $proxyHost,
            'cache'      => $cache,
            'subfilter'  => $subfilter,
            'type'       => 1,
            'advanced'   => 0,
        ]);
    }

    /**
     * Delete a reverse proxy.
     * API: /site?action=DeleteProxy
     */
    public function deleteProxy(string $siteName, string $proxyName): array
    {
        return $this->getClient()->post('/site?action=DeleteProxy', [
            'siteName'  => $siteName,
            'proxyname' => $proxyName,
        ]);
    }

    // ─── Hotlink Protection ────────────────────────────────────

    /**
     * Get hotlink protection config.
     * API: /site?action=GetSecurity
     */
    public function getHotlinkProtection(int $id, string $siteName): array
    {
        return $this->getClient()->post('/site?action=GetSecurity', [
            'id'   => $id,
            'name' => $siteName,
        ]);
    }

    /**
     * Set hotlink protection.
     * API: /site?action=SetSecurity
     *
     * @param string $domains  Allowed domains (comma-separated)
     * @param string $suffix   Protected file extensions (e.g. 'jpg,png,gif,js,css')
     * @param bool   $status   Enable/disable
     */
    public function setHotlinkProtection(int $id, string $siteName, string $domains, string $suffix, bool $status = true): array
    {
        return $this->getClient()->post('/site?action=SetSecurity', [
            'id'      => $id,
            'name'    => $siteName,
            'fix'     => $suffix,
            'domains' => $domains,
            'status'  => $status ? '1' : '0',
        ]);
    }

    // ─── Password Access ───────────────────────────────────────

    /**
     * Set password access protection.
     * API: /site?action=SetHasPwd
     */
    public function setPasswordAccess(int $id, string $username, string $password): array
    {
        return $this->getClient()->post('/site?action=SetHasPwd', [
            'id'       => $id,
            'username' => $username,
            'password' => $password,
        ]);
    }

    /**
     * Close password access protection.
     * API: /site?action=CloseHasPwd
     */
    public function closePasswordAccess(int $id): array
    {
        return $this->getClient()->post('/site?action=CloseHasPwd', [
            'id' => $id,
        ]);
    }

    // ─── Traffic Limit (Nginx) ─────────────────────────────────

    /**
     * Get traffic limit config.
     * API: /site?action=GetLimitNet
     */
    public function getTrafficLimit(int $id): array
    {
        return $this->getClient()->post('/site?action=GetLimitNet', [
            'id' => $id,
        ]);
    }

    /**
     * Set traffic limit.
     * API: /site?action=SetLimitNet
     */
    public function setTrafficLimit(int $id, int $perserver, int $perip, int $limitRate): array
    {
        return $this->getClient()->post('/site?action=SetLimitNet', [
            'id'         => $id,
            'perserver'  => $perserver,
            'perip'      => $perip,
            'limit_rate' => $limitRate,
        ]);
    }

    /**
     * Turn off traffic limits.
     * API: /site?action=CloseLimitNet
     */
    public function closeTrafficLimit(int $id): array
    {
        return $this->getClient()->post('/site?action=CloseLimitNet', [
            'id' => $id,
        ]);
    }

    // ─── Logs ──────────────────────────────────────────────────

    /**
     * Toggle access logs.
     * API: /site?action=logsOpen
     */
    public function toggleAccessLogs(int $id): array
    {
        return $this->getClient()->post('/site?action=logsOpen', [
            'id' => $id,
        ]);
    }

    /**
     * Get error logs for a site.
     * API: /site?action=GetErrLog
     */
    public function getErrorLog(string $siteName): array
    {
        return $this->getClient()->post('/site?action=GetErrLog', [
            'siteName' => $siteName,
        ]);
    }

    /**
     * Get security scan logs.
     * API: /site?action=GetSecurityLog
     */
    public function getSecurityLog(string $siteName): array
    {
        return $this->getClient()->post('/site?action=GetSecurityLog', [
            'siteName' => $siteName,
        ]);
    }

    // ─── Site Config File ──────────────────────────────────────

    /**
     * Get site web server config file content.
     * API: /files?action=GetFileBody
     */
    public function getSiteConfigFile(string $siteName, string $webserver = 'nginx'): array
    {
        $path = "/www/server/panel/vhost/{$webserver}/{$siteName}.conf";
        return $this->getClient()->post('/files?action=GetFileBody', [
            'path' => $path,
        ]);
    }

    /**
     * Save site web server config file.
     * API: /files?action=SaveFileBody
     */
    public function saveSiteConfigFile(string $siteName, string $data, string $webserver = 'nginx'): array
    {
        $path = "/www/server/panel/vhost/{$webserver}/{$siteName}.conf";
        return $this->getClient()->post('/files?action=SaveFileBody', [
            'path'     => $path,
            'data'     => $data,
            'encoding' => 'utf-8',
        ]);
    }

    // ─── Rewrite Rules ─────────────────────────────────────────

    /**
     * Get rewrite rule template list.
     * API: /site?action=GetRewriteList
     */
    public function getRewriteList(string $siteName): array
    {
        return $this->getClient()->post('/site?action=GetRewriteList', [
            'siteName' => $siteName,
        ]);
    }

    // ─── Backup ────────────────────────────────────────────────

    /**
     * Get site backup list.
     * API: /data?action=getData&table=backup
     */
    public function getBackups(int $siteId, int $limit = 5, int $page = 1): array
    {
        return $this->getClient()->post('/data?action=getData&table=backup', [
            'search' => $siteId,
            'limit'  => $limit,
            'p'      => $page,
            'type'   => 0,
        ]);
    }

    /**
     * Create site backup.
     * API: /site?action=ToBackup
     */
    public function createBackup(int $id): array
    {
        return $this->getClient()->post('/site?action=ToBackup', [
            'id' => $id,
        ]);
    }

    /**
     * Delete site backup.
     * API: /site?action=DelBackup
     */
    public function deleteBackup(int $id): array
    {
        return $this->getClient()->post('/site?action=DelBackup', [
            'id' => $id,
        ]);
    }

    // ─── Remarks ───────────────────────────────────────────────

    /**
     * Modify site remarks/notes.
     * API: /data?action=setPs&table=sites
     */
    public function setRemarks(int $id, string $remarks): array
    {
        return $this->getClient()->post('/data?action=setPs&table=sites', [
            'id' => $id,
            'ps' => $remarks,
        ]);
    }

    // ─── Directory Settings ────────────────────────────────────

    /**
     * Get dir config (anti-cross-hop, run directory, logs status, password access).
     * API: /site?action=GetDirUserINI
     */
    public function getDirUserINI(int $id, string $path): array
    {
        return $this->getClient()->post('/site?action=GetDirUserINI', [
            'id'   => $id,
            'path' => $path,
        ]);
    }

    /**
     * Toggle anti-cross-hop (open_basedir).
     * API: /site?action=SetDirUserINI
     */
    public function setDirUserINI(string $path): array
    {
        return $this->getClient()->post('/site?action=SetDirUserINI', [
            'path' => $path,
        ]);
    }

    // ─── Default Document ──────────────────────────────────────

    /**
     * Get default document (index files).
     * API: /site?action=GetIndex
     */
    public function getIndex(int $id): array
    {
        return $this->getClient()->post('/site?action=GetIndex', [
            'id' => $id,
        ]);
    }

    /**
     * Set default document.
     * API: /site?action=SetIndex
     */
    public function setIndex(int $id, string $index): array
    {
        return $this->getClient()->post('/site?action=SetIndex', [
            'id'    => $id,
            'Index' => $index,
        ]);
    }

    // ─── Root Path ─────────────────────────────────────────────

    /**
     * Get site root path.
     * API: /data?action=getKey&table=sites&key=path
     */
    public function getSitePath(int $id): array
    {
        return $this->getClient()->post('/data?action=getKey&table=sites&key=path', [
            'id' => $id,
        ]);
    }

    /**
     * Set site root directory.
     * API: /site?action=SetPath
     */
    public function setPath(int $id, string $path): array
    {
        return $this->getClient()->post('/site?action=SetPath', [
            'id'   => $id,
            'path' => $path,
        ]);
    }

    /**
     * Set site run path (e.g. /public for Laravel).
     * API: /site?action=SetSiteRunPath
     */
    public function setRunPath(int $id, string $runPath): array
    {
        return $this->getClient()->post('/site?action=SetSiteRunPath', [
            'id'      => $id,
            'runPath' => $runPath,
        ]);
    }

    // ─── Expiration ────────────────────────────────────────────

    /**
     * Set site expiration date.
     * API: /site?action=SetEdate
     *
     * @param string $edate 'YYYY-MM-DD' or '0000-00-00' for permanent
     */
    public function setExpiration(int $id, string $edate = '0000-00-00'): array
    {
        return $this->getClient()->post('/site?action=SetEdate', [
            'id'    => $id,
            'edate' => $edate,
        ]);
    }

    // ─── Disk Quota (XFS only) ─────────────────────────────────

    /**
     * Set disk quota for a site.
     * API: /site?action=SetDiskQuota
     */
    public function setDiskQuota(int $id, int $quotaMB): array
    {
        return $this->getClient()->post('/site?action=SetDiskQuota', [
            'id'    => $id,
            'quota' => $quotaMB,
        ]);
    }
}
