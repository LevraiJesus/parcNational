<?php
namespace Skand\Backend\helpers;


class FileUploaderHelper {
    public static function uploadFile($file, $targetDir = 'uploads/') {
        $targetFile = $targetDir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($file["tmp_name"]);
        if($check === false) {
            return false;
        }
        
        // Check file size (limit to 5MB)
        if ($file["size"] > 5000000) {
            return false;
        }
        
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            return false;
        }
        
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $targetFile;
        } else {
            return false;
        }
    }
}
