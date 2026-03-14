<?php

namespace AzozzALFiras\AAPanelAPI\Modules;

/**
 * File management operations.
 *
 * Covers: /files endpoints.
 */
class FileManager extends AbstractModule
{
    /**
     * Get directory listing.
     * API: /files?action=GetDir
     */
    public function getDirectory(string $path, int $page = 1, int $showRow = 100): array
    {
        return $this->client->post('/files?action=GetDir', [
            'path'         => $path,
            'p'            => $page,
            'showRow'      => $showRow,
            'is_operating' => true,
        ]);
    }

    /**
     * Get file content.
     * API: /files?action=GetFileBody
     */
    public function getFileBody(string $path): array
    {
        return $this->client->post('/files?action=GetFileBody', [
            'path' => $path,
        ]);
    }

    /**
     * Save file content.
     * API: /files?action=SaveFileBody
     */
    public function saveFileBody(string $path, string $data, string $encoding = 'utf-8'): array
    {
        return $this->client->post('/files?action=SaveFileBody', [
            'path'     => $path,
            'data'     => $data,
            'encoding' => $encoding,
        ]);
    }

    /**
     * Create a file.
     * API: /files?action=CreateFile
     */
    public function createFile(string $path): array
    {
        return $this->client->post('/files?action=CreateFile', [
            'path' => $path,
        ]);
    }

    /**
     * Create a directory.
     * API: /files?action=CreateDir
     */
    public function createDirectory(string $path): array
    {
        return $this->client->post('/files?action=CreateDir', [
            'path' => $path,
        ]);
    }

    /**
     * Delete file or directory.
     * API: /files?action=DeleteFile / DeleteDir
     */
    public function deleteFile(string $path): array
    {
        return $this->client->post('/files?action=DeleteFile', [
            'path' => $path,
        ]);
    }

    public function deleteDirectory(string $path): array
    {
        return $this->client->post('/files?action=DeleteDir', [
            'path' => $path,
        ]);
    }

    /**
     * Copy file/directory.
     * API: /files?action=CopyFile
     */
    public function copy(string $source, string $destination): array
    {
        return $this->client->post('/files?action=CopyFile', [
            'sfile' => $source,
            'dfile' => $destination,
        ]);
    }

    /**
     * Move/rename file or directory.
     * API: /files?action=MvFile
     */
    public function move(string $source, string $destination): array
    {
        return $this->client->post('/files?action=MvFile', [
            'sfile' => $source,
            'dfile' => $destination,
        ]);
    }

    /**
     * Set file/directory permissions.
     * API: /files?action=SetFileAccess
     */
    public function setPermissions(string $path, string $mode, bool $recursive = false): array
    {
        return $this->client->post('/files?action=SetFileAccess', [
            'filename' => $path,
            'user'     => 'www',
            'access'   => $mode,
            'all'      => $recursive ? 'True' : 'False',
        ]);
    }

    /**
     * Compress files/directories.
     * API: /files?action=Zip
     */
    public function compress(string $sourcePath, string $destinationZip, string $type = 'zip'): array
    {
        return $this->client->post('/files?action=Zip', [
            'sfile' => $sourcePath,
            'dfile' => $destinationZip,
            'type'  => $type,
        ]);
    }

    /**
     * Extract archive.
     * API: /files?action=UnZip
     */
    public function extract(string $sourceFile, string $destination, string $type = 'zip', ?string $password = null): array
    {
        $data = [
            'sfile'  => $sourceFile,
            'dfile'  => $destination,
            'type'   => $type,
            'coding' => 'UTF-8',
        ];
        if ($password !== null) {
            $data['password'] = $password;
        }
        return $this->client->post('/files?action=UnZip', $data);
    }

    /**
     * Download remote file to server.
     * API: /files?action=DownloadFile
     */
    public function downloadRemoteFile(string $url, string $path, string $filename): array
    {
        return $this->client->post('/files?action=DownloadFile', [
            'url'      => $url,
            'path'     => $path,
            'filename' => $filename,
        ]);
    }

    /**
     * Upload local file to server.
     * API: /files?action=upload
     */
    public function upload(string $localPath, string $remotePath, string $filename): array
    {
        if (!file_exists($localPath)) {
            throw new \InvalidArgumentException("File not found: {$localPath}");
        }
        return $this->client->postWithFile(
            '/files?action=upload',
            [
                'f_path'  => $remotePath,
                'f_name'  => $filename,
                'f_size'  => filesize($localPath),
                'f_start' => 0,
            ],
            'blob',
            $localPath,
            $filename
        );
    }

    /**
     * Get file/directory size.
     * API: /files?action=GetDirSize
     */
    public function getSize(string $path): array
    {
        return $this->client->post('/files?action=GetDirSize', [
            'path' => $path,
        ]);
    }
}
