<?php

namespace App\Services;

use Config\Services;
use CodeIgniter\HTTP\Files\UploadedFile;

class RemoteUploadService
{
    protected string $uploadUrl;
    protected string $deleteUrl;
    protected string $token;
    protected string $baseUrl;

    public function __construct()
    {
        $this->uploadUrl = env('upload.server.url') ?: 'https://skj.nsnpao.go.th/upload.php';
        $this->deleteUrl = env('upload.server.delete.url') ?: 'https://skj.nsnpao.go.th/delete.php';
        $this->token     = (string) (env('upload.server.token') ?: 'Dekpiano2025!!');
        $this->baseUrl   = env('upload.server.baseurl') ?: 'https://skj.nsnpao.go.th/uploads/';
    }

    /**
     * Upload an instance of CodeIgniter UploadedFile directly to the remote server.
     *
     * @param UploadedFile $file
     * @param string $remoteSubPath e.g. 'saraban/inbound', 'saraban/orders', 'saraban/documents'
     * @param string|null $desiredFilename Custom filename if specified
     * @return array ['status' => 'success'|'error', 'filename' => ..., 'filePath' => ..., 'fullUrl' => ..., 'message' => ...]
     */
    public function uploadUploadedFile(UploadedFile $file, string $remoteSubPath, ?string $desiredFilename = null): array
    {
        if (!$file->isValid()) {
            return [
                'status'  => 'error',
                'message' => 'ไฟล์ที่อัปโหลดไม่ถูกต้อง: ' . $file->getErrorString() . ' (' . $file->getError() . ')'
            ];
        }

        $filename = $desiredFilename ?: $file->getClientName();
        $tempPath = $file->getTempName();
        $mimeType = $file->getMimeType() ?: 'application/octet-stream';

        return $this->sendMultipartUpload($tempPath, $remoteSubPath, $filename, $mimeType);
    }

    /**
     * Upload a local file located on the server disk directly to the remote server.
     * (Useful for stamped PDFs or generated files)
     *
     * @param string $localFullPath
     * @param string $remoteSubPath e.g. 'saraban/inbound', 'saraban/orders'
     * @param string $desiredFilename
     * @param string|null $mimeType
     * @return array ['status' => 'success'|'error', 'filename' => ..., 'filePath' => ..., 'fullUrl' => ..., 'message' => ...]
     */
    public function uploadLocalFile(string $localFullPath, string $remoteSubPath, string $desiredFilename, ?string $mimeType = null): array
    {
        if (!file_exists($localFullPath)) {
            return [
                'status'  => 'error',
                'message' => 'ไม่พบไฟล์ในเครื่องที่ระบุ: ' . $localFullPath
            ];
        }

        $detectedMime = $mimeType ?: (mime_content_type($localFullPath) ?: 'application/octet-stream');
        return $this->sendMultipartUpload($localFullPath, $remoteSubPath, $desiredFilename, $detectedMime);
    }

    /**
     * Delete a file from the remote upload server.
     *
     * @param string $remoteFilePath Relative path (e.g. 'saraban/inbound/file.pdf') or full URL
     * @return bool
     */
    public function deleteFile(string $remoteFilePath): bool
    {
        if (empty($remoteFilePath)) {
            return true;
        }

        // If a full URL was passed, strip baseUrl prefix
        if (preg_match('#^https?://#i', $remoteFilePath)) {
            $base = rtrim($this->baseUrl, '/');
            if (str_starts_with($remoteFilePath, $base)) {
                $remoteFilePath = ltrim(substr($remoteFilePath, strlen($base)), '/');
            } else {
                $remoteFilePath = basename($remoteFilePath);
            }
        }

        $pathParts  = pathinfo($remoteFilePath);
        $remotePath = ($pathParts['dirname'] !== '.' && $pathParts['dirname'] !== '') ? $pathParts['dirname'] : '';
        $filename   = $pathParts['basename'] ?? '';

        if (empty($filename)) {
            return false;
        }

        try {
            $client   = Services::curlrequest();
            $jsonData = json_encode([
                'path'  => $remotePath,
                'files' => [$filename]
            ]);

            $response = $client->setBody($jsonData)->post($this->deleteUrl, [
                'headers'     => [
                    'Content-Type' => 'application/json',
                    'X-Auth-Token' => $this->token
                ],
                'http_errors' => false,
                'timeout'     => 30
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody(), true);

            if ($statusCode === 200 && isset($body['status']) && ($body['status'] === 'success' || $body['status'] === 'partial_success')) {
                return true;
            }

            log_message('warning', "Remote file delete notice ({$remoteFilePath}): " . ($body['message'] ?? "Status {$statusCode}"));
            return false;
        } catch (\Exception $e) {
            log_message('error', "Remote file delete exception ({$remoteFilePath}): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check connection to the remote upload server.
     *
     * @return array
     */
    public function pingServer(): array
    {
        try {
            $client = Services::curlrequest();
            $pingUrl = $this->uploadUrl . (str_contains($this->uploadUrl, '?') ? '&' : '?') . 'ping=1';
            $response = $client->get($pingUrl, [
                'headers'     => ['X-Auth-Token' => $this->token],
                'http_errors' => false,
                'timeout'     => 10
            ]);

            return [
                'status'     => $response->getStatusCode() === 200 ? 'success' : 'warning',
                'statusCode' => $response->getStatusCode(),
                'response'   => trim($response->getBody())
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get the public URL for a given relative remote file path.
     *
     * @param string $remoteFilePath e.g. 'saraban/inbound/sample.pdf'
     * @return string
     */
    public function getFullUrl(string $remoteFilePath): string
    {
        if (empty($remoteFilePath)) {
            return '';
        }
        if (preg_match('#^https?://#i', $remoteFilePath)) {
            return $remoteFilePath;
        }
        return rtrim($this->baseUrl, '/') . '/' . ltrim($remoteFilePath, '/');
    }

    /**
     * Internal helper to send a single direct multipart POST to upload.php.
     */
    protected function sendMultipartUpload(string $localFilePath, string $remoteSubPath, string $filename, string $mimeType): array
    {
        if (empty($this->uploadUrl)) {
            return ['status' => 'error', 'message' => 'Upload server URL is not configured.'];
        }

        try {
            $client = Services::curlrequest();

            // Prepare cURL File
            $curlFile = new \CURLFile($localFilePath, $mimeType, $filename);

            $postData = [
                'path'             => trim($remoteSubPath, '/'),
                'filename'         => $filename,
                'desired_filename' => $filename,
                'file'             => $curlFile
            ];

            $headers = [
                'X-Auth-Token' => $this->token
            ];

            $response = $client->post($this->uploadUrl, [
                'multipart'   => $postData,
                'headers'     => $headers,
                'http_errors' => false,
                'timeout'     => 120 // 2 minutes for large files
            ]);

            $statusCode = $response->getStatusCode();
            $rawBody    = $response->getBody();
            $body       = json_decode($rawBody, true);

            if ($statusCode === 200 && isset($body['status']) && $body['status'] === 'success') {
                $savedFilename = $body['filename'] ?? $filename;
                $relativePath  = trim($remoteSubPath, '/') . '/' . $savedFilename;

                return [
                    'status'   => 'success',
                    'filename' => $savedFilename,
                    'filePath' => $relativePath,
                    'fullUrl'  => $this->getFullUrl($relativePath)
                ];
            }

            $errorMessage = $body['message'] ?? ("Server Error (Status: {$statusCode}). Response: " . substr(strip_tags($rawBody), 0, 200));
            log_message('error', "RemoteUploadService failed for {$filename}: {$errorMessage}");

            return [
                'status'  => 'error',
                'message' => "อัปโหลดไฟล์ไปยังเซิร์ฟเวอร์ปลายทางไม่สำเร็จ: {$errorMessage}"
            ];
        } catch (\Exception $e) {
            log_message('error', "RemoteUploadService cURL exception: " . $e->getMessage());
            return [
                'status'  => 'error',
                'message' => 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์อัปโหลด: ' . $e->getMessage()
            ];
        }
    }
}
