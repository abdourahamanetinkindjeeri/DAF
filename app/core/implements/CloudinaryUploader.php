<?php

namespace App\Core\implements;

use App\Core\Uploader;
use Cloudinary\Cloudinary;

class CloudinaryUploader implements Uploader
{
  private Cloudinary $cloudinary;

  public function __construct()
  {
    $this->cloudinary = new Cloudinary([
      'cloud' => [
        'cloud_name' => CLOUD_NAME,
        'api_key'    => PUBLIC_KEY,
        'api_secret' => PRIVATE_KEY
      ]
    ]);
  }

  public function upload(string $localPath, string $folder): ?string
  {
    if (!file_exists($localPath)) {
      return null;
    }

    $upload = $this->cloudinary->uploadApi()->upload($localPath, [
      'folder' => $folder
    ]);

    return $upload['secure_url'] ?? null;
  }
}
