<?php

namespace AzozzALFiras\AAPanelAPI\Modules;

use AzozzALFiras\AAPanelAPI\HttpClient;
use AzozzALFiras\AAPanelAPI\Modules\Websites\PhpSite;
use AzozzALFiras\AAPanelAPI\Modules\Websites\NodeSite;
use AzozzALFiras\AAPanelAPI\Modules\Websites\PythonSite;
use AzozzALFiras\AAPanelAPI\Modules\Websites\ProxySite;

/**
 * Website management facade.
 *
 * Provides access to all 4 website project types:
 *   - PHP sites   → $panel->website()->php()
 *   - Node.js     → $panel->website()->node()
 *   - Python      → $panel->website()->python()
 *   - Proxy       → $panel->website()->proxy()
 *
 * For backward compatibility, common PHP site methods are available directly
 * on this class (e.g. $panel->website()->getList() delegates to php()->getList()).
 */
class Website extends AbstractModule
{
    private $php;
    private $node;
    private $python;
    private $proxy;

    /** PHP Website management (full featured) */
    public function php(): PhpSite
    {
        if ($this->php === null) {
            $this->php = new PhpSite($this->client);
        }
        return $this->php;
    }

    /** Node.js project management (32+ endpoints) */
    public function node(): NodeSite
    {
        if ($this->node === null) {
            $this->node = new NodeSite($this->client);
        }
        return $this->node;
    }

    /** Python project management (uWSGI/Gunicorn) */
    public function python(): PythonSite
    {
        if ($this->python === null) {
            $this->python = new PythonSite($this->client);
        }
        return $this->python;
    }

    /** Reverse Proxy project management */
    public function proxy(): ProxySite
    {
        if ($this->proxy === null) {
            $this->proxy = new ProxySite($this->client);
        }
        return $this->proxy;
    }

    // ─── Backward-compatible shortcuts (delegate to PhpSite) ──

    /** @see PhpSite::getList() */
    public function getList(int $limit = 20, int $page = 1, ?string $search = null, int $type = -1, string $order = 'id desc'): array
    {
        return $this->php()->getList($limit, $page, $search, $type, $order);
    }

    /** @see PhpSite::getSiteTypes() */
    public function getSiteTypes(): array
    {
        return $this->php()->getSiteTypes();
    }

    /** @see PhpSite::getPHPVersions() */
    public function getPHPVersions(): array
    {
        return $this->php()->getPHPVersions();
    }

    /** @see PhpSite::create() */
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
        return $this->php()->create($domain, $path, $description, $domainList, $typeId, $phpVersion, $port, $createFtp, $ftpUser, $ftpPass, $createDb, $dbUser, $dbPass, $codeing);
    }

    /** @see PhpSite::delete() */
    public function delete(int $id, string $webname, bool $delFtp = false, bool $delDb = false, bool $delPath = false): array
    {
        return $this->php()->delete($id, $webname, $delFtp, $delDb, $delPath);
    }

    /** @see PhpSite::stop() */
    public function stop(int $id, string $name): array
    {
        return $this->php()->stop($id, $name);
    }

    /** @see PhpSite::start() */
    public function start(int $id, string $name): array
    {
        return $this->php()->start($id, $name);
    }

    /** @see PhpSite via SiteCommon::enableHttps() */
    public function enableHttpsRedirection(string $siteName): array
    {
        return $this->php()->enableHttps($siteName);
    }

    /** @see PhpSite via SiteCommon::disableHttps() */
    public function disableHttpsRedirection(string $siteName): array
    {
        return $this->php()->disableHttps($siteName);
    }

    /** @see PhpSite via SiteCommon::getDomains() */
    public function getDomains(int $siteId): array
    {
        return $this->php()->getDomains($siteId);
    }

    /** @see PhpSite via SiteCommon::addDomain() */
    public function addDomain(int $id, string $webname, string $domain): array
    {
        return $this->php()->addDomain($id, $webname, $domain);
    }

    /** @see PhpSite via SiteCommon::deleteDomain() */
    public function deleteDomain(int $id, string $webname, string $domain, int $port = 80): array
    {
        return $this->php()->deleteDomain($id, $webname, $domain, $port);
    }

    /** @see PhpSite via SiteCommon::getBackups() */
    public function getBackups(int $siteId, int $limit = 5, int $page = 1): array
    {
        return $this->php()->getBackups($siteId, $limit, $page);
    }

    /** @see PhpSite via SiteCommon::createBackup() */
    public function createBackup(int $id): array
    {
        return $this->php()->createBackup($id);
    }

    /** @see PhpSite via SiteCommon::deleteBackup() */
    public function deleteBackup(int $id): array
    {
        return $this->php()->deleteBackup($id);
    }

    /** @see PhpSite via SiteCommon::setExpiration() */
    public function setExpiration(int $id, string $edate = '0000-00-00'): array
    {
        return $this->php()->setExpiration($id, $edate);
    }

    /** @see PhpSite via SiteCommon::setRemarks() */
    public function setRemarks(int $id, string $remarks): array
    {
        return $this->php()->setRemarks($id, $remarks);
    }

    /** @see PhpSite via SiteCommon::setPath() */
    public function setPath(int $id, string $path): array
    {
        return $this->php()->setPath($id, $path);
    }

    /** @see PhpSite via SiteCommon::setRunPath() */
    public function setRunPath(int $id, string $runPath): array
    {
        return $this->php()->setRunPath($id, $runPath);
    }

    /** @see PhpSite via SiteCommon::getSitePath() */
    public function getSitePath(int $id): array
    {
        return $this->php()->getSitePath($id);
    }

    /** @see PhpSite via SiteCommon::getDirUserINI() */
    public function getDirUserINI(int $id, string $path): array
    {
        return $this->php()->getDirUserINI($id, $path);
    }

    /** @see PhpSite via SiteCommon::setDirUserINI() */
    public function setDirUserINI(string $path): array
    {
        return $this->php()->setDirUserINI($path);
    }

    /** @see PhpSite via SiteCommon::toggleAccessLogs() */
    public function toggleAccessLogs(int $id): array
    {
        return $this->php()->toggleAccessLogs($id);
    }

    /** @see PhpSite via SiteCommon::getIndex() */
    public function getIndex(int $id): array
    {
        return $this->php()->getIndex($id);
    }

    /** @see PhpSite via SiteCommon::setIndex() */
    public function setIndex(int $id, string $index): array
    {
        return $this->php()->setIndex($id, $index);
    }

    /** @see PhpSite via SiteCommon::setPasswordAccess() */
    public function setPasswordAccess(int $id, string $username, string $password): array
    {
        return $this->php()->setPasswordAccess($id, $username, $password);
    }

    /** @see PhpSite via SiteCommon::closePasswordAccess() */
    public function closePasswordAccess(int $id): array
    {
        return $this->php()->closePasswordAccess($id);
    }

    /** @see PhpSite via SiteCommon::getTrafficLimit() */
    public function getTrafficLimit(int $id): array
    {
        return $this->php()->getTrafficLimit($id);
    }

    /** @see PhpSite via SiteCommon::setTrafficLimit() */
    public function setTrafficLimit(int $id, int $perserver, int $perip, int $limitRate): array
    {
        return $this->php()->setTrafficLimit($id, $perserver, $perip, $limitRate);
    }

    /** @see PhpSite via SiteCommon::closeTrafficLimit() */
    public function closeTrafficLimit(int $id): array
    {
        return $this->php()->closeTrafficLimit($id);
    }

    /** @see PhpSite via SiteCommon::getRewriteList() */
    public function getRewriteList(string $siteName): array
    {
        return $this->php()->getRewriteList($siteName);
    }
}
