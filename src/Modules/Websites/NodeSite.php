<?php

namespace AzozzALFiras\AAPanelAPI\Modules\Websites;

use AzozzALFiras\AAPanelAPI\HttpClient;
use AzozzALFiras\AAPanelAPI\Modules\AbstractModule;

/**
 * Node.js project management.
 *
 * Covers: /project/nodejs/* endpoints (32+ methods).
 * Source: github.com/aaPanel/aaPanel/blob/master/class/projectModel/nodejsModel.py
 *
 * All endpoints use POST method.
 * Route pattern: /project/nodejs/{method_name}/1
 */
class NodeSite extends AbstractModule
{
    use SiteCommon;

    private const BASE = '/project/nodejs';

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
     * Get list of Node.js projects.
     * API: /project/nodejs/get_project_list/1
     * Also works: /v2/project/nodejs/get_project_list
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
     * Get detailed project info including runtime state.
     * API: /project/nodejs/get_project_info/1
     */
    public function getProjectInfo(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_info'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Get complete project config from database.
     * API: /project/nodejs/get_project_find/1
     */
    public function getProjectConfig(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_find'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Check if project is running.
     * API: /project/nodejs/get_project_run_state/1
     */
    public function getRunState(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_run_state'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Get CPU/memory/IO load info for project processes.
     * API: /project/nodejs/get_project_load_info/1
     */
    public function getLoadInfo(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_load_info'), [
            'project_name' => $projectName,
        ]);
    }

    // ─── Create / Delete / Modify ──────────────────────────────

    /**
     * Create a new Node.js project.
     * API: /project/nodejs/create_project/1
     *
     * @param string $projectName  Project display name
     * @param string $path         Project root directory
     * @param string $runScript    Startup script from package.json (e.g. 'start')
     * @param int    $port         Listening port
     * @param string $nodeVersion  Node.js version to use
     * @param string $user         Execution user ('root' or 'www')
     * @param string $domain       Domain name to bind
     * @param string $remarks      Project remarks
     */
    public function create(
        string $projectName,
        string $path,
        string $runScript,
        int $port,
        string $nodeVersion,
        string $user = 'www',
        string $domain = '',
        string $remarks = ''
    ): array {
        return $this->client->post($this->endpoint('create_project'), [
            'project_name' => $projectName,
            'path'         => $path,
            'run_script'   => $runScript,
            'port'         => $port,
            'node_version' => $nodeVersion,
            'user'         => $user,
            'domain'       => $domain,
            'ps'           => $remarks,
        ]);
    }

    /**
     * Modify project settings.
     * API: /project/nodejs/modify_project/1
     */
    public function modify(string $projectName, array $settings): array
    {
        return $this->client->post($this->endpoint('modify_project'), array_merge(
            ['project_name' => $projectName],
            $settings
        ));
    }

    /**
     * Delete a Node.js project.
     * API: /project/nodejs/remove_project/1
     */
    public function delete(string $projectName): array
    {
        return $this->client->post($this->endpoint('remove_project'), [
            'project_name' => $projectName,
        ]);
    }

    // ─── Start / Stop / Restart ────────────────────────────────

    /**
     * Start project.
     * API: /project/nodejs/start_project/1
     */
    public function start(string $projectName): array
    {
        return $this->client->post($this->endpoint('start_project'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Stop project.
     * API: /project/nodejs/stop_project/1
     */
    public function stop(string $projectName): array
    {
        return $this->client->post($this->endpoint('stop_project'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Restart project.
     * API: /project/nodejs/restart_project/1
     */
    public function restart(string $projectName): array
    {
        return $this->client->post($this->endpoint('restart_project'), [
            'project_name' => $projectName,
        ]);
    }

    // ─── Node.js Version Management ───────────────────────────

    /**
     * Check if Node.js version manager is installed.
     * API: /project/nodejs/is_install_nodejs/1
     */
    public function isNodeInstalled(): array
    {
        return $this->client->post($this->endpoint('is_install_nodejs'));
    }

    /**
     * Get all installed Node.js versions.
     * API: /project/nodejs/get_nodejs_version/1
     */
    public function getNodeVersions(): array
    {
        return $this->client->post($this->endpoint('get_nodejs_version'));
    }

    /**
     * Get project's current Node.js version.
     * API: /project/nodejs/get_project_nodejs_version/1
     */
    public function getProjectNodeVersion(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_nodejs_version'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Change Node.js version for a project.
     * API: /project/nodejs/set_project_nodejs_version/1
     */
    public function setProjectNodeVersion(string $projectName, string $version): array
    {
        return $this->client->post($this->endpoint('set_project_nodejs_version'), [
            'project_name' => $projectName,
            'node_version' => $version,
        ]);
    }

    // ─── Port / Listen ─────────────────────────────────────────

    /**
     * Set project listening port.
     * API: /project/nodejs/set_project_listen/1
     */
    public function setListenPort(string $projectName, int $port): array
    {
        return $this->client->post($this->endpoint('set_project_listen'), [
            'project_name' => $projectName,
            'port'         => $port,
        ]);
    }

    /**
     * Check if port is available.
     * API: /project/nodejs/check_port_is_used/1
     */
    public function checkPort(int $port, ?string $sock = null): array
    {
        $data = ['port' => $port];
        if ($sock !== null) {
            $data['sock'] = $sock;
        }
        return $this->client->post($this->endpoint('check_port_is_used'), $data);
    }

    // ─── Domain Management ─────────────────────────────────────

    /**
     * Get domains bound to a project.
     * API: /project/nodejs/project_get_domain/1
     */
    public function getProjectDomains(string $projectName): array
    {
        return $this->client->post($this->endpoint('project_get_domain'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Add domain(s) to a project.
     * API: /project/nodejs/project_add_domain/1
     */
    public function addProjectDomain(string $projectName, string $domain): array
    {
        return $this->client->post($this->endpoint('project_add_domain'), [
            'project_name' => $projectName,
            'domain'       => $domain,
        ]);
    }

    /**
     * Remove a domain from a project.
     * API: /project/nodejs/project_remove_domain/1
     */
    public function removeProjectDomain(string $projectName, string $domain, int $port = 80): array
    {
        return $this->client->post($this->endpoint('project_remove_domain'), [
            'project_name' => $projectName,
            'domain'       => $domain,
            'port'         => $port,
        ]);
    }

    // ─── External Network Mapping (Nginx/Apache) ──────────────

    /**
     * Enable external network mapping (reverse proxy via Nginx/Apache).
     * API: /project/nodejs/bind_extranet/1
     */
    public function bindExtranet(string $projectName): array
    {
        return $this->client->post($this->endpoint('bind_extranet'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Disable external network mapping.
     * API: /project/nodejs/unbind_extranet/1
     */
    public function unbindExtranet(string $projectName): array
    {
        return $this->client->post($this->endpoint('unbind_extranet'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Apply web server config for the project.
     * API: /project/nodejs/set_config/1
     */
    public function setWebConfig(string $projectName): array
    {
        return $this->client->post($this->endpoint('set_config'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Remove web server config for the project.
     * API: /project/nodejs/clear_config/1
     */
    public function clearWebConfig(string $projectName): array
    {
        return $this->client->post($this->endpoint('clear_config'), [
            'project_name' => $projectName,
        ]);
    }

    // ─── NPM Module Management ─────────────────────────────────

    /**
     * Get available startup scripts from package.json.
     * API: /project/nodejs/get_run_list/1
     */
    public function getRunScripts(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_run_list'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Install project dependencies (npm install).
     * API: /project/nodejs/install_packages/1
     */
    public function installPackages(string $projectName): array
    {
        return $this->client->post($this->endpoint('install_packages'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Update all installed packages.
     * API: /project/nodejs/update_packages/1
     */
    public function updatePackages(string $projectName): array
    {
        return $this->client->post($this->endpoint('update_packages'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Reinstall all dependencies (remove + install).
     * API: /project/nodejs/reinstall_packages/1
     */
    public function reinstallPackages(string $projectName): array
    {
        return $this->client->post($this->endpoint('reinstall_packages'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Get list of installed npm modules.
     * API: /project/nodejs/get_project_modules/1
     */
    public function getModules(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_modules'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Install a single npm module.
     * API: /project/nodejs/install_module/1
     */
    public function installModule(string $projectName, string $moduleName): array
    {
        return $this->client->post($this->endpoint('install_module'), [
            'project_name' => $projectName,
            'module_name'  => $moduleName,
        ]);
    }

    /**
     * Uninstall an npm module.
     * API: /project/nodejs/uninstall_module/1
     */
    public function uninstallModule(string $projectName, string $moduleName): array
    {
        return $this->client->post($this->endpoint('uninstall_module'), [
            'project_name' => $projectName,
            'module_name'  => $moduleName,
        ]);
    }

    /**
     * Upgrade a specific npm module.
     * API: /project/nodejs/upgrade_module/1
     */
    public function upgradeModule(string $projectName, string $moduleName): array
    {
        return $this->client->post($this->endpoint('upgrade_module'), [
            'project_name' => $projectName,
            'module_name'  => $moduleName,
        ]);
    }

    /**
     * Rebuild native modules (npm rebuild).
     * API: /project/nodejs/rebuild_project/1
     */
    public function rebuildProject(string $projectName): array
    {
        return $this->client->post($this->endpoint('rebuild_project'), [
            'project_name' => $projectName,
        ]);
    }

    // ─── Logs ──────────────────────────────────────────────────

    /**
     * Get project execution logs (last 200 lines).
     * API: /project/nodejs/get_project_log/1
     */
    public function getProjectLog(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_log'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Get NPM execution logs (last 20 lines).
     * API: /project/nodejs/get_exec_logs/1
     */
    public function getExecLogs(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_exec_logs'), [
            'project_name' => $projectName,
        ]);
    }

    // ─── SSL ───────────────────────────────────────────────────

    /**
     * Get SSL certificate expiration info for project.
     * API: /project/nodejs/get_ssl_end_date/1
     */
    public function getSSLEndDate(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_ssl_end_date'), [
            'project_name' => $projectName,
        ]);
    }
}
