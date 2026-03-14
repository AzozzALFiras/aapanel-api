<?php

namespace AzozzALFiras\AAPanelAPI;

/**
 * Legacy backward-compatible wrapper.
 *
 * @deprecated Use AaPanel class instead for the new modular API.
 *
 * All old method calls are delegated to the new modular architecture.
 */
class aaPanelApiClient
{
    private $panel;

    public function __construct($apiKey, $baseUrl)
    {
        $this->panel = new AaPanel($apiKey, $baseUrl);
    }

    /** @return AaPanel Access the new modular client */
    public function getPanel(): AaPanel
    {
        return $this->panel;
    }

    // ─── Legacy method mappings ────────────────────────────────

    public function fetchLogs()
    {
        return $this->panel->system()->getLogs();
    }

    public function addSite($domain, $path, $description, $typeId = 0, $type = 'php', $phpVersion = '73', $port = '80', $ftp = null, $ftpUsername = null, $ftpPassword = null, $sql = null, $databaseUsername = null, $databasePassword = null, $setSsl = 1, $forceSsl = 1)
    {
        return $this->panel->website()->create(
            $domain,
            "/www/wwwroot/" . $path,
            $description,
            [],
            $typeId,
            $phpVersion,
            $port,
            $ftp !== null,
            $ftpUsername,
            $ftpPassword,
            $sql !== null,
            $databaseUsername,
            $databasePassword
        );
    }

    public function addSubdomain($subdomain, $mainDomain, $ipTarget)
    {
        return $this->panel->dns()->addRecord($subdomain, $mainDomain, $ipTarget);
    }

    public function deleteSubdomain($subdomain, $mainDomain, $ipTarget)
    {
        return $this->panel->dns()->deleteRecord($subdomain, $mainDomain, $ipTarget);
    }

    public function fetchFtpAccounts($limit, $page, $search = null)
    {
        return $this->panel->ftp()->getList($limit, $page, $search);
    }

    public function addFtpAccount($username, $password, $path, $ps = null)
    {
        return $this->panel->ftp()->create($username, $password, $path, $ps);
    }

    public function deleteFtpAccount($username, $id)
    {
        return $this->panel->ftp()->delete($id, $username);
    }

    public function importSqlFile($file, $databaseName)
    {
        return $this->panel->database()->importSql($file, $databaseName);
    }

    public function saveFile($fileContent, $path)
    {
        return $this->panel->files()->saveFileBody($path, $fileContent);
    }

    public function unzipFile($sourceFile, $destination, $password = null)
    {
        return $this->panel->files()->extract($sourceFile, $destination, 'zip', $password);
    }

    public function applySslCertificate($domain, $domainId, $autoWildcard = 0)
    {
        return $this->panel->ssl()->applyAndDeploy($domain, $domainId, $autoWildcard);
    }

    public function renewCert($domain)
    {
        return $this->panel->ssl()->renewByDomain($domain);
    }

    public function getIndexValue($domain)
    {
        $ssl = $this->panel->ssl()->getSSL($domain);
        return $ssl['index'] ?? null;
    }

    public function enableHttpsRedirection($siteName)
    {
        return $this->panel->website()->enableHttpsRedirection($siteName);
    }

    public function disableSite($siteId, $siteName)
    {
        return $this->panel->website()->stop($siteId, $siteName);
    }

    public function enableSite($siteId, $siteName)
    {
        return $this->panel->website()->start($siteId, $siteName);
    }

    public function getFtpAccountDetails($username)
    {
        return $this->panel->ftp()->getDetails($username);
    }

    public function setServerConfig($configData)
    {
        return $this->panel->system()->setConfig($configData);
    }

    public function getServerConfig()
    {
        return $this->panel->system()->getConfig();
    }

    public function deleteSite($domain, $id)
    {
        return $this->panel->website()->delete($id, $domain, true, true, true);
    }

    public function fetchSites($limit, $page, $search = null)
    {
        return $this->panel->website()->getList($limit, $page, $search);
    }

    public function fetchDirectory($path, $page, $showRow = 100)
    {
        return $this->panel->files()->getDirectory($path, $page, $showRow);
    }

    public function downloadFile($remoteUrl, $path, $filename)
    {
        return $this->panel->files()->downloadRemoteFile($remoteUrl, $path, $filename);
    }

    public function getFileBody($path)
    {
        return $this->panel->files()->getFileBody($path);
    }

    public function uploadFile($localPath, $path, $filename)
    {
        return $this->panel->files()->upload($localPath, $path, $filename);
    }
}
