<?php

namespace Danupe\Core\Classes;

class ClonePlugin
{
    private string $source;
    private string $destination;
    private string $search;
    private string $replace;

    public function __construct(string $source, string $destination, string $search, string $replace) {
        if (!is_dir($source)) {
            throw new Exception("Source directory does not exist.");
        }

        $this->source = $source;
        $this->destination = $destination;
        $this->search = $search;
        $this->replace = $replace;
    }

    public function process() {
        $tempFolder = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid("tmp_folder_");
        $this->recurseCopy($this->source, $tempFolder);
        $this->renameFilesAndContent($tempFolder);
        rename($tempFolder, $this->destination);
        echo "Folder processed and moved to: {$this->destination}\n";
    }

    private function recurseCopy($source, $destination) {
        mkdir($destination, 0755, true);
        $dir = opendir($source);
        while (false !== ($file = readdir($dir))) {
            if ($file != '.' && $file != '..') {
                $srcFile = "$source/$file";
                $dstFile = "$destination/$file";
                if (is_dir($srcFile)) {
                    $this->recurseCopy($srcFile, $dstFile);
                } else {
                    copy($srcFile, $dstFile);
                }
            }
        }
        closedir($dir);
    }

    private function renameFilesAndContent($folder) {
        $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($items as $item) {
            if ($item->isFile()) {
                $content = file_get_contents($item->getRealPath());
                $newContent = str_replace($this->search, $this->replace, $content);
                $newContent = str_replace(ucfirst($this->search), ucfirst($this->replace), $newContent);
                $newContent = str_replace(strtolower($this->search), strtolower($this->replace), $newContent);
                file_put_contents($item->getRealPath(), $newContent);
            }
        }
        $this->renameItems($folder);
    }

    private function renameItems($folder) {
        $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($items as $item) {
            $newName = str_replace($this->search, $this->replace, $item->getFilename());
            $newName = str_replace(ucfirst($this->search), ucfirst($this->replace), $newName);
            $newName = str_replace(strtolower($this->search), strtolower($this->replace), $newName);
            $newPath = dirname($item->getPathname()) . DIRECTORY_SEPARATOR . $newName;
            if ($newName !== $item->getFilename()) {
                rename($item->getPathname(), $newPath);
            }
        }
    }  
}
