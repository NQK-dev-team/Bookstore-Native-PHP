
<?php
function rrmdir($dir)
{
      if (is_dir($dir)) {
            $objects = scandir($dir);
            if ($objects === false) {
                  throw new Exception("Failed to scan directory: $dir");
            }
            foreach ($objects as $object) {
                  if ($object != "." && $object != "..") {
                        $fullPath = $dir . DIRECTORY_SEPARATOR . $object;
                        if (is_dir($fullPath) && !is_link($fullPath)) {
                              rrmdir($fullPath);
                        } else {
                              if (!unlink($fullPath)) {
                                    throw new Exception("Failed to delete file: $fullPath");
                              }
                        }
                  }
            }
            if (!rmdir($dir)) {
                  throw new Exception("Failed to delete directory: $dir");
            }
      }
}
?>