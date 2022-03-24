<?php

namespace app\utils;

use Yii;

class FKUploadUtils {

    public static function generateFilename($filename, $path = NULL, $hideName = false, $overwrite = false) {
        
        if (!self::endsWith($path, '/'))
            $path .= '/';

        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if ($hideName)
            $retFilename = md5($filename) . "." . $ext;
        else
            $retFilename = $filename;


        if ($overwrite) {
            return $retFilename;
        } else {
            $i = 1;
            while (file_exists($path . $retFilename)) {
                if ($hideName) {
                    $retFilename = md5($i . "_" . $filename) . "." . $ext;
                    $i++;
                } else
                    $retFilename = rand(111111, 999999) . "_" . $filename;
            }

            return $retFilename;
        }
    }

    public static function generateAndSaveFile($file, $path = NULL, $hideName = false, $overwrite = false) {
        $filename = self::generateFilename($file->getBaseName() . "." . $file->getExtension(), $path, $hideName, $overwrite);

        if ($str == trim($str) && strpos($str, ' ') !== false) {
            $filename = str_replace(" ", "_", $filename);
        }

        $file->saveAs($path . "/" .$filename);
        return $filename;
    }

    public static function generateAndSaveKit($file, $path = NULL, $hideName = false) {
        $filename = self::generateFilename($file->getBaseName() . "." . $file->getExtension(), $path, $hideName);

        $file->saveAs($path . "/" . $filename);

        return $filename;
    }

    public static function exifRotation($filename) {

        $exif = @exif_read_data($filename, 0, true);

        if ($exif === FALSE)
            return 0;

        $orientation = -1;
        $angle_degree = 0;

        if (@array_key_exists('Orientation', $exif['IFD0'])) {
            $orientation = $exif['IFD0']['Orientation'];

            switch ($orientation) {
                case 1: { // do nothing
                        $angle_degree = 0;
                        break;
                    }
                case 8: { // rotate 90 clockwise
                        $angle_degree = 90;
                        break;
                    }
                case 3: { // rotate 180 clockwise
                        $angle_degree = 180;
                        break;
                    }
                case 6: { // rotate 270 clockwise
                        $angle_degree = -90;
                        break;
                    }
            }
        }

        return $angle_degree;
    }

    public static function endsWith($haystack, $needle) {
        if ($haystack === "")
            return false;

        return substr($haystack, -1) === $needle;
    }

}
