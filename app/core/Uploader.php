<?php

namespace App\Core;

interface Uploader
{

    /**
     * Upload un fichier local et retourne l'URL distante.
     */
    public function upload(string $localPath, string $folder): ?string;
}