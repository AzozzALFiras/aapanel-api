<?php

namespace AzozzALFiras\AAPanelAPI\Modules\Websites;

use AzozzALFiras\AAPanelAPI\HttpClient;
use AzozzALFiras\AAPanelAPI\Modules\AbstractModule;

/**
 * PHP Website / Project management.
 *
 * Covers: /site (AddSite, DeleteSite, etc.), /data?table=sites,
 * PHP version, Composer, site categories, default pages,
 * plus all shared features via SiteCommon trait.
 */
class PhpSite extends AbstractModule
{
    use SiteCommon;

    protected function getClient(): HttpClient
    {
        return $this->client;
    }

    // ─── List / Query ──────────────────────────────────────────

    /**
     * Get list of PHP websites.
     * API: /data?action=getData&table=sites
     */
    public function getList(int $limit = 20, int $page = 1, ?string $search = null, int $type = -1, string $order = 'id desc'): array
    {
        $data = [
            'table'  => 'sites',
            'limit'  => $limit,
            'p'      => $page,
            'type'   => $type,
            'order'  => $order,
            'tojs'   => 'get_site_list',
        ];
        if ($search !== null) {
            $data['search'] = $search;
        }
        return $this->client->post('/data?action=getData', $data);
    }

    /**
     * Get PHP websites (v2 endpoint with more data).
     * API: /v2/data?action=getData&table=sites
     */
    public function getListV2(int $limit = 50, int $page = 1, ?string $search = null, int $type = -1): array
    {
        $query = http_build_query([
            'action' => 'getData',
            'p'      => $page,
            'limit'  => $limit,
            'table'  => 'sites',
            'search' => $search ?? '',
            'order'  => '',
            'type'   => $type,
        ]);
        return $this->client->post("/v2/data?{$query}");
    }

    /**
     * Get website types/categories.
     * API: /site?action=get_site_types
     */
    public function getSiteTypes(): array
    {
        return $this->client->post('/site?action=get_site_types');
    }

    /**
     * Get installed PHP versions.
     * API: /site?action=GetPHPVersion
     */
    public function getPHPVersions(): array
    {
        return $this->client->post('/site?action=GetPHPVersion');
    }

    // ─── Create / Delete ───────────────────────────────────────

    /**
     * Create a new PHP website.
     * API: /site?action=AddSite
     *
     * @param string      $domain      Main domain name
     * @param string      $path        Root directory path (e.g. /www/wwwroot/example.com)
     * @param string      $description Site remarks
     * @param array       $domainList  Additional domains
     * @param int         $typeId      Category/classification ID
     * @param string      $phpVersion  PHP version code ('73', '80', '81', etc.)
     * @param string      $port        Port number
     * @param bool        $createFtp   Whether to create FTP account
     * @param string|null $ftpUser     FTP username
     * @param string|null $ftpPass     FTP password
     * @param bool        $createDb    Whether to create database
     * @param string|null $dbUser      Database username
     * @param string|null $dbPass      Database password
     * @param string      $codeing     Database charset (utf8, utf8mb4, gbk, big5)
     */
    public function create(
        string $domain,
        string $path,
        string $description,
        array $domainList = [],
        int $typeId = 0,
        string $phpVersion = '73',
        string $port = '80',
        bool $createFtp = false,
        ?string $ftpUser = null,
        ?string $ftpPass = null,
        bool $createDb = false,
        ?string $dbUser = null,
        ?string $dbPass = null,
        string $codeing = 'utf8'
    ): array {
        $data = [
            'webname'  => json_encode([
                'domain'     => $domain,
                'domainlist' => $domainList,
                'count'      => 0,
            ]),
            'path'     => $path,
            'ps'       => $description,
            'type_id'  => $typeId,
            'type'     => 'PHP',
            'version'  => $phpVersion,
            'port'     => $port,
            'ftp'      => $createFtp ? 'true' : 'false',
            'sql'      => $createDb ? 'true' : 'false',
            'codeing'  => $codeing,
        ];

        if ($createFtp && $ftpUser) {
            $data['ftp_username'] = $ftpUser;
            $data['ftp_password'] = $ftpPass;
        }
        if ($createDb && $dbUser) {
            $data['datauser'] = $dbUser;
            $data['datapassword'] = $dbPass;
        }

        return $this->client->post('/site?action=AddSite', $data);
    }

    /**
     * Delete a PHP website.
     * API: /site?action=DeleteSite
     *
     * @param int    $id        Website ID
     * @param string $webname   Site name
     * @param bool   $delFtp    Delete associated FTP
     * @param bool   $delDb     Delete associated database
     * @param bool   $delPath   Delete root directory
     */
    public function delete(int $id, string $webname, bool $delFtp = false, bool $delDb = false, bool $delPath = false): array
    {
        $data = [
            'id'      => $id,
            'webname' => $webname,
        ];
        if ($delFtp) $data['ftp'] = '1';
        if ($delDb) $data['database'] = '1';
        if ($delPath) $data['path'] = '1';

        return $this->client->post('/site?action=DeleteSite', $data);
    }

    // ─── Start / Stop ──────────────────────────────────────────

    /**
     * Stop a website.
     * API: /site?action=SiteStop
     */
    public function stop(int $id, string $name): array
    {
        return $this->client->post('/site?action=SiteStop', [
            'id'   => $id,
            'name' => $name,
        ]);
    }

    /**
     * Start a website.
     * API: /site?action=SiteStart
     */
    public function start(int $id, string $name): array
    {
        return $this->client->post('/site?action=SiteStart', [
            'id'   => $id,
            'name' => $name,
        ]);
    }

    // ─── PHP Version ───────────────────────────────────────────

    /**
     * Set PHP version for a site.
     * API: /site?action=SetPHPVersion
     */
    public function setPHPVersion(string $siteName, string $version): array
    {
        return $this->client->post('/site?action=SetPHPVersion', [
            'siteName' => $siteName,
            'version'  => $version,
        ]);
    }

    /**
     * Get current PHP version for a site.
     * API: /site?action=GetSitePHPVersion
     */
    public function getSitePHPVersion(string $siteName): array
    {
        return $this->client->post('/site?action=GetSitePHPVersion', [
            'siteName' => $siteName,
        ]);
    }

    // ─── Default Pages ─────────────────────────────────────────

    /**
     * Get default site page (for unbound domains, 404, stopped sites).
     * API: /site?action=GetDefaultSite
     */
    public function getDefaultSite(): array
    {
        return $this->client->post('/site?action=GetDefaultSite');
    }

    /**
     * Set default site for unbound domains.
     * API: /site?action=SetDefaultSite
     */
    public function setDefaultSite(string $siteName): array
    {
        return $this->client->post('/site?action=SetDefaultSite', [
            'name' => $siteName,
        ]);
    }

    // ─── Deny Files ────────────────────────────────────────────

    /**
     * Get denied file extensions config.
     * API: /site?action=GetDenyAccess
     */
    public function getDenyAccess(int $id, string $siteName): array
    {
        return $this->client->post('/site?action=GetDenyAccess', [
            'id'   => $id,
            'name' => $siteName,
        ]);
    }

    /**
     * Set denied file extensions.
     * API: /site?action=SetDenyAccess
     */
    public function setDenyAccess(int $id, string $siteName, string $suffix): array
    {
        return $this->client->post('/site?action=SetDenyAccess', [
            'id'   => $id,
            'name' => $siteName,
            'fix'  => $suffix,
        ]);
    }

    // ─── Site Categories ───────────────────────────────────────

    /**
     * Add a site category/type.
     * API: /site?action=add_site_type
     */
    public function addSiteType(string $name): array
    {
        return $this->client->post('/site?action=add_site_type', [
            'name' => $name,
        ]);
    }

    /**
     * Delete a site category/type.
     * API: /site?action=remove_site_type
     */
    public function removeSiteType(int $id): array
    {
        return $this->client->post('/site?action=remove_site_type', [
            'id' => $id,
        ]);
    }

    /**
     * Set site type/category.
     * API: /site?action=set_site_type
     */
    public function setSiteType(int $siteId, int $typeId): array
    {
        return $this->client->post('/site?action=set_site_type', [
            'id'   => $siteId,
            'type' => $typeId,
        ]);
    }
}
