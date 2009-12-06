<?php
final class Filesystem {
  public static final function delDir($dirPath) {
    if (false === strpos($dirPath, ROOT . 'projects')) {
      die('Invalid path to deltree!!');
    }
    if (!is_dir($dirPath)) {
      trigger_error('Provided path is not a directory.', E_USER_ERROR);
    }
    foreach (scandir($dirPath) as $itemInDir) {
      if (!in_array($itemInDir, array('.', '..'))) {
        if (is_dir($itemInDir)) {
          self::delDir("{$dirPath}/{$itemInDir}");
        } else {
          unlink("{$dirPath}/{$itemInDir}");
        }
      }
    }
    rmdir($dirPath);
  }
}
?>
