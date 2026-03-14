<?php

namespace AzozzALFiras\AAPanelAPI;

use AzozzALFiras\AAPanelAPI\Modules\System;
use AzozzALFiras\AAPanelAPI\Modules\Website;
use AzozzALFiras\AAPanelAPI\Modules\Database;
use AzozzALFiras\AAPanelAPI\Modules\Ftp;
use AzozzALFiras\AAPanelAPI\Modules\FileManager;
use AzozzALFiras\AAPanelAPI\Modules\Ssl;
use AzozzALFiras\AAPanelAPI\Modules\Cron;
use AzozzALFiras\AAPanelAPI\Modules\Firewall;
use AzozzALFiras\AAPanelAPI\Modules\Dns;
use AzozzALFiras\AAPanelAPI\Modules\Websites\PhpSite;
use AzozzALFiras\AAPanelAPI\Modules\Websites\NodeSite;
use AzozzALFiras\AAPanelAPI\Modules\Websites\PythonSite;
use AzozzALFiras\AAPanelAPI\Modules\Websites\ProxySite;
use AzozzALFiras\AAPanelAPI\Modules\Databases\Mysql;
use AzozzALFiras\AAPanelAPI\Modules\Databases\PostgreSql;
use AzozzALFiras\AAPanelAPI\Modules\Databases\MongoDb;
use AzozzALFiras\AAPanelAPI\Modules\Databases\Redis;
use AzozzALFiras\AAPanelAPI\Modules\Databases\SqlServer;

/**
 * aaPanel API Client - Main entry point.
 *
 * Usage:
 *   $panel = new AaPanel('your_api_key', 'https://your-panel:8888');
 *
 *   // System
 *   $panel->system()->getSystemTotal();
 *
 *   // Websites - Facade (backward compatible, defaults to PHP sites)
 *   $panel->website()->getList();
 *   $panel->website()->create('example.com', '/www/wwwroot/example.com', 'My site');
 *
 *   // Websites - Specific project types
 *   $panel->website()->php()->getList();
 *   $panel->website()->node()->create('myapp', '/www/node/myapp', 'start', 3000, 'v18');
 *   $panel->website()->python()->create('myapi', '/www/python/myapi', 'app.py', 8000, '3.11');
 *   $panel->website()->proxy()->create('myproxy', 'proxy.example.com', 'http://127.0.0.1:3000');
 *
 *   // Direct shortcuts (skip the facade)
 *   $panel->phpSite()->getList();
 *   $panel->nodeSite()->getList();
 *   $panel->pythonSite()->getList();
 *   $panel->proxySite()->getList();
 *
 *   // Other modules
 *   $panel->database()->create('mydb', 'myuser', 'mypass');        // MySQL (default)
 *   $panel->database()->mysql()->getRunStatus();
 *   $panel->database()->pgsql()->create('pgdb', 'pguser', 'pgpass');
 *   $panel->database()->mongodb()->create('mgdb', 'mgpass');
 *   $panel->database()->redis()->setKey(0, 'key', 'value');
 *   $panel->database()->sqlserver()->create('msdb', 'msuser', 'mspass');
 *   $panel->ftp()->create('ftpuser', 'ftppass', '/www/wwwroot/mysite');
 *   $panel->files()->getDirectory('/www/wwwroot');
 *   $panel->ssl()->applyAndDeploy('example.com', 1);
 *   $panel->cron()->create('Backup', 'day', '3', '0');
 *   $panel->firewall()->addPortRule('8080');
 *   $panel->dns()->addRecord('sub', 'example.com', '1.2.3.4');
 */
class AaPanel
{
    private $client;
    private $system;
    private $website;
    private $database;
    private $ftp;
    private $fileManager;
    private $ssl;
    private $cron;
    private $firewall;
    private $dns;

    // Direct site type instances
    private $phpSite;
    private $nodeSite;
    private $pythonSite;
    private $proxySite;

    /**
     * @param string $apiKey  API key from aaPanel Settings > API
     * @param string $baseUrl Panel URL with port (e.g. https://your-server:8888)
     * @param array  $options Optional: ['timeout' => 60, 'verify_ssl' => false, 'cookie_dir' => '/tmp']
     */
    public function __construct(string $apiKey, string $baseUrl, array $options = [])
    {
        if ($apiKey === '') {
            throw new \InvalidArgumentException('API key cannot be empty.');
        }
        if ($baseUrl === '') {
            throw new \InvalidArgumentException('Base URL cannot be empty.');
        }
        $this->client = new HttpClient($apiKey, $baseUrl, $options);
    }

    public function getHttpClient(): HttpClient
    {
        return $this->client;
    }

    // ─── Core Modules ──────────────────────────────────────────

    /** System status & panel management */
    public function system(): System
    {
        if ($this->system === null) {
            $this->system = new System($this->client);
        }
        return $this->system;
    }

    /** Website management facade (all 4 types + backward-compatible PHP shortcuts) */
    public function website(): Website
    {
        if ($this->website === null) {
            $this->website = new Website($this->client);
        }
        return $this->website;
    }

    /** Database (MySQL) management */
    public function database(): Database
    {
        if ($this->database === null) {
            $this->database = new Database($this->client);
        }
        return $this->database;
    }

    /** FTP account management */
    public function ftp(): Ftp
    {
        if ($this->ftp === null) {
            $this->ftp = new Ftp($this->client);
        }
        return $this->ftp;
    }

    /** File management */
    public function files(): FileManager
    {
        if ($this->fileManager === null) {
            $this->fileManager = new FileManager($this->client);
        }
        return $this->fileManager;
    }

    /** SSL certificate management */
    public function ssl(): Ssl
    {
        if ($this->ssl === null) {
            $this->ssl = new Ssl($this->client);
        }
        return $this->ssl;
    }

    /** Scheduled tasks (Cron) */
    public function cron(): Cron
    {
        if ($this->cron === null) {
            $this->cron = new Cron($this->client);
        }
        return $this->cron;
    }

    /** Firewall & security */
    public function firewall(): Firewall
    {
        if ($this->firewall === null) {
            $this->firewall = new Firewall($this->client);
        }
        return $this->firewall;
    }

    /** DNS management (plugin) */
    public function dns(): Dns
    {
        if ($this->dns === null) {
            $this->dns = new Dns($this->client);
        }
        return $this->dns;
    }

    // ─── Direct Website Type Shortcuts ─────────────────────────

    /** PHP site management (direct shortcut) */
    public function phpSite(): PhpSite
    {
        if ($this->phpSite === null) {
            $this->phpSite = new PhpSite($this->client);
        }
        return $this->phpSite;
    }

    /** Node.js project management (direct shortcut) */
    public function nodeSite(): NodeSite
    {
        if ($this->nodeSite === null) {
            $this->nodeSite = new NodeSite($this->client);
        }
        return $this->nodeSite;
    }

    /** Python project management (direct shortcut) */
    public function pythonSite(): PythonSite
    {
        if ($this->pythonSite === null) {
            $this->pythonSite = new PythonSite($this->client);
        }
        return $this->pythonSite;
    }

    /** Reverse Proxy project management (direct shortcut) */
    public function proxySite(): ProxySite
    {
        if ($this->proxySite === null) {
            $this->proxySite = new ProxySite($this->client);
        }
        return $this->proxySite;
    }
}
