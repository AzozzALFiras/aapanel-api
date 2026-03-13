<?php

namespace AzozzALFiras\AAPanelAPI\Modules\Websites;

use AzozzALFiras\AAPanelAPI\HttpClient;
use AzozzALFiras\AAPanelAPI\Modules\AbstractModule;

/**
 * Python project management.
 *
 * Covers: /project/python/* endpoints.
 * Route pattern: /project/python/{method_name}/1
 *
 * Note: Python project management is a plugin-based feature.
 * Requires "Python Project Manager" plugin installed on the panel.
 * Endpoints follow the same pattern as Node.js (nodejsModel.py).
 */
class PythonSite extends AbstractModule
{
    use SiteCommon;

    private const BASE = '/project/python';

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
     * Get list of Python projects.
     * API: /project/python/get_project_list/1
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
     * Get detailed project info.
     * API: /project/python/get_project_info/1
     */
    public function getProjectInfo(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_info'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Get project config.
     * API: /project/python/get_project_find/1
     */
    public function getProjectConfig(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_find'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Check if project is running.
     * API: /project/python/get_project_run_state/1
     */
    public function getRunState(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_run_state'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Get load info (CPU, memory).
     * API: /project/python/get_project_load_info/1
     */
    public function getLoadInfo(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_load_info'), [
            'project_name' => $projectName,
        ]);
    }

    // ─── Create / Delete / Modify ──────────────────────────────

    /**
     * Create a new Python project.
     * API: /project/python/create_project/1
     *
     * @param string $projectName  Project name
     * @param string $path         Project root directory
     * @param string $startFile    Startup file (e.g. 'app.py', 'manage.py')
     * @param int    $port         Listening port
     * @param string $pythonVersion Python version
     * @param string $framework    Framework type ('flask', 'django', 'fastapi', 'general')
     * @param string $user         Execution user ('root' or 'www')
     * @param string $domain       Domain to bind
     * @param string $startCommand Start command (e.g. 'gunicorn', 'uwsgi')
     * @param string $remarks      Project remarks
     */
    public function create(
        string $projectName,
        string $path,
        string $startFile,
        int $port,
        string $pythonVersion,
        string $framework = 'general',
        string $user = 'www',
        string $domain = '',
        string $startCommand = '',
        string $remarks = ''
    ): array {
        return $this->client->post($this->endpoint('create_project'), [
            'project_name'   => $projectName,
            'path'           => $path,
            'start_file'     => $startFile,
            'port'           => $port,
            'python_version' => $pythonVersion,
            'framework'      => $framework,
            'user'           => $user,
            'domain'         => $domain,
            'start_command'  => $startCommand,
            'ps'             => $remarks,
        ]);
    }

    /**
     * Modify project settings.
     * API: /project/python/modify_project/1
     */
    public function modify(string $projectName, array $settings): array
    {
        return $this->client->post($this->endpoint('modify_project'), array_merge(
            ['project_name' => $projectName],
            $settings
        ));
    }

    /**
     * Delete a Python project.
     * API: /project/python/remove_project/1
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
     * API: /project/python/start_project/1
     */
    public function start(string $projectName): array
    {
        return $this->client->post($this->endpoint('start_project'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Stop project.
     * API: /project/python/stop_project/1
     */
    public function stop(string $projectName): array
    {
        return $this->client->post($this->endpoint('stop_project'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Restart project.
     * API: /project/python/restart_project/1
     */
    public function restart(string $projectName): array
    {
        return $this->client->post($this->endpoint('restart_project'), [
            'project_name' => $projectName,
        ]);
    }

    // ─── Python Version ────────────────────────────────────────

    /**
     * Get installed Python versions.
     * API: /project/python/get_python_version/1
     */
    public function getPythonVersions(): array
    {
        return $this->client->post($this->endpoint('get_python_version'));
    }

    /**
     * Get project's Python version.
     * API: /project/python/get_project_python_version/1
     */
    public function getProjectPythonVersion(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_python_version'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Set project's Python version.
     * API: /project/python/set_project_python_version/1
     */
    public function setProjectPythonVersion(string $projectName, string $version): array
    {
        return $this->client->post($this->endpoint('set_project_python_version'), [
            'project_name'   => $projectName,
            'python_version' => $version,
        ]);
    }

    // ─── Domain Management ─────────────────────────────────────

    /**
     * Get domains bound to a project.
     * API: /project/python/project_get_domain/1
     */
    public function getProjectDomains(string $projectName): array
    {
        return $this->client->post($this->endpoint('project_get_domain'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Add domain to a project.
     * API: /project/python/project_add_domain/1
     */
    public function addProjectDomain(string $projectName, string $domain): array
    {
        return $this->client->post($this->endpoint('project_add_domain'), [
            'project_name' => $projectName,
            'domain'       => $domain,
        ]);
    }

    /**
     * Remove domain from a project.
     * API: /project/python/project_remove_domain/1
     */
    public function removeProjectDomain(string $projectName, string $domain, int $port = 80): array
    {
        return $this->client->post($this->endpoint('project_remove_domain'), [
            'project_name' => $projectName,
            'domain'       => $domain,
            'port'         => $port,
        ]);
    }

    // ─── External Network Mapping ──────────────────────────────

    /**
     * Enable external network mapping (reverse proxy).
     * API: /project/python/bind_extranet/1
     */
    public function bindExtranet(string $projectName): array
    {
        return $this->client->post($this->endpoint('bind_extranet'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Disable external network mapping.
     * API: /project/python/unbind_extranet/1
     */
    public function unbindExtranet(string $projectName): array
    {
        return $this->client->post($this->endpoint('unbind_extranet'), [
            'project_name' => $projectName,
        ]);
    }

    // ─── Pip Module Management ─────────────────────────────────

    /**
     * Get installed pip modules.
     * API: /project/python/get_project_modules/1
     */
    public function getModules(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_modules'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Install pip requirements.
     * API: /project/python/install_packages/1
     */
    public function installPackages(string $projectName): array
    {
        return $this->client->post($this->endpoint('install_packages'), [
            'project_name' => $projectName,
        ]);
    }

    /**
     * Install a single pip module.
     * API: /project/python/install_module/1
     */
    public function installModule(string $projectName, string $moduleName): array
    {
        return $this->client->post($this->endpoint('install_module'), [
            'project_name' => $projectName,
            'module_name'  => $moduleName,
        ]);
    }

    /**
     * Uninstall a pip module.
     * API: /project/python/uninstall_module/1
     */
    public function uninstallModule(string $projectName, string $moduleName): array
    {
        return $this->client->post($this->endpoint('uninstall_module'), [
            'project_name' => $projectName,
            'module_name'  => $moduleName,
        ]);
    }

    // ─── Logs ──────────────────────────────────────────────────

    /**
     * Get project execution logs.
     * API: /project/python/get_project_log/1
     */
    public function getProjectLog(string $projectName): array
    {
        return $this->client->post($this->endpoint('get_project_log'), [
            'project_name' => $projectName,
        ]);
    }
}
