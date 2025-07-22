<?php

namespace App\Core;

class Upload
{


    /**
     * Récupère une information depuis le tableau $_FILES
     */
    static function getInfoToFile(string $name, string $key): string
    {
        return $_FILES[$name][$key] ?? '';
    }

    /**
     * Déplace un fichier uploadé vers le dossier de destination
     */
    static function moveUploadedFile(string $tmp_name, string $destination): bool
    {
        return is_uploaded_file($tmp_name) && move_uploaded_file($tmp_name, $destination);
    }



    /**
     * Vérifie si un fichier a été uploadé avec succès
     */
    static function isFileUploaded(string $fieldName): bool
    {
        return isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK;
    }

    /**
     * Récupère l'extension d’un fichier de manière sécurisée
     */
    static function getFileExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Génère un nom de fichier unique en conservant l’extension
     */
    static function generateUniqueFilename(string $originalName): string
    {
        $extension = static::getFileExtension($originalName);
        return uniqid('file_', true) . '.' . $extension;
    }


    // Classe App\Config\Upload

    static function handleFileUpload(string $fieldName, string $destinationFolder='./images/upload/'): ?string
    {
        if (!self::isFileUploaded($fieldName)) {
            return null;
        }

        $originalName = self::getInfoToFile($fieldName, 'name');
        $tmpName = self::getInfoToFile($fieldName, 'tmp_name');

        if (!is_dir($destinationFolder)) {
            mkdir($destinationFolder, 0755, true);
        }

        $uniqueName = self::generateUniqueFilename($originalName);
        $destination = rtrim($destinationFolder, '/') . '/' . $uniqueName;

        if (self::moveUploadedFile($tmpName, $destination)) {
            return $uniqueName;
        }

        return null;
    }

}