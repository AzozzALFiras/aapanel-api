<?php

namespace AzozzALFiras\AAPanelAPI\Modules;

use AzozzALFiras\AAPanelAPI\Exceptions\AaPanelException;

/**
 * SSL certificate management.
 *
 * Covers: /acme, /site SSL-related endpoints.
 */
class Ssl extends AbstractModule
{
    /**
     * Get SSL details for a domain.
     * API: /site?action=GetSSL
     */
    public function getSSL(string $siteName): array
    {
        return $this->client->post('/site?action=GetSSL', [
            'siteName' => $siteName,
        ]);
    }

    /**
     * Apply (issue) a Let's Encrypt SSL certificate.
     * API: /acme?action=apply_cert_api
     *
     * @param string $domain       Domain name
     * @param int    $siteId       Website/domain ID
     * @param string $authType     Verification type ('http', 'dns', 'tls')
     * @param int    $autoWildcard Auto wildcard (0 or 1)
     */
    public function applyCertificate(string $domain, int $siteId, string $authType = 'http', int $autoWildcard = 0): array
    {
        return $this->client->post('/acme?action=apply_cert_api', [
            'domains'       => json_encode([$domain]),
            'id'            => $siteId,
            'auth_to'       => $siteId,
            'auth_type'     => $authType,
            'auto_wildcard' => $autoWildcard,
        ]);
    }

    /**
     * Deploy/set SSL certificate to a site.
     * API: /site?action=SetSSL
     *
     * @param string $siteName    Site domain
     * @param string $key         Private key content
     * @param string $certificate Full certificate chain (cert + root)
     * @param int    $type        Certificate type (1 = custom, 2 = Let's Encrypt)
     */
    public function setSSL(string $siteName, string $key, string $certificate, int $type = 1): array
    {
        return $this->client->post('/site?action=SetSSL', [
            'type'     => $type,
            'siteName' => $siteName,
            'key'      => $key,
            'csr'      => $certificate,
        ]);
    }

    /**
     * Apply and deploy SSL certificate in one step.
     *
     * @throws AaPanelException If certificate application fails
     */
    public function applyAndDeploy(string $domain, int $siteId, int $autoWildcard = 0): array
    {
        $result = $this->applyCertificate($domain, $siteId, 'http', $autoWildcard);

        if (!isset($result['private_key'], $result['cert'])) {
            throw new AaPanelException('Failed to apply certificate: ' . json_encode($result));
        }

        $certificate = $result['cert'];
        if (isset($result['root'])) {
            $certificate .= ' ' . $result['root'];
        }

        return $this->setSSL($domain, $result['private_key'], $certificate);
    }

    /**
     * Renew SSL certificate.
     * API: /acme?action=renew_cert
     */
    public function renewCertificate(string $index): array
    {
        return $this->client->post('/acme?action=renew_cert', [
            'index' => $index,
        ]);
    }

    /**
     * Renew SSL by domain name (auto-fetches index).
     *
     * @throws AaPanelException If domain SSL info not found
     */
    public function renewByDomain(string $domain): array
    {
        $ssl = $this->getSSL($domain);

        if (!isset($ssl['index'])) {
            throw new AaPanelException("SSL index not found for domain: {$domain}");
        }

        return $this->renewCertificate($ssl['index']);
    }

    /**
     * Close/remove SSL for a site.
     * API: /site?action=CloseSSLConf
     */
    public function closeSSL(int $siteId, string $siteName): array
    {
        return $this->client->post('/site?action=CloseSSLConf', [
            'updateOf' => $siteId,
            'siteName' => $siteName,
        ]);
    }
}
