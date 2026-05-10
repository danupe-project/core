<?php

namespace Danupe\Core\Classes;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;

class ClonePlugin
{
    private string $source;
    private string $destination;
    private string $search;
    private string $replace;

    public function __construct(string $source, string $destination, string $search, string $replace) 
    {
        // Sicherstellen, dass das Quellverzeichnis existiert
        if (!is_dir($source)) {
            throw new Exception("Source directory does not exist: $source");
        }

        // Pfade normalisieren (abschließende Slashes entfernen)
        $this->source = rtrim($source, DIRECTORY_SEPARATOR);
        $this->destination = rtrim($destination, DIRECTORY_SEPARATOR);
        $this->search = $search;
        $this->replace = $replace;
    }

    public function process() 
    {
        // 1. Temporären Ordner im gleichen Elternverzeichnis wie das Ziel erstellen.
        // Das verhindert "Invalid cross-device link" Fehler in Docker/auf verschiedenen Partitionen.
        $parentDir = dirname($this->destination);
        if (!is_dir($parentDir)) {
            mkdir($parentDir, 0755, true);
        }
        
        $tempFolder = $parentDir . DIRECTORY_SEPARATOR . uniqid(".tmp_clone_");

        try {
            // 2. Dateien und Ordnerstruktur kopieren
            $this->recurseCopy($this->source, $tempFolder);
            
            // 3. Inhalte der Dateien und Dateinamen anpassen
            $this->renameFilesAndContent($tempFolder);

            // 4. Sicherstellen, dass das Ziel noch nicht existiert
            if (is_dir($this->destination)) {
                throw new Exception("Destination directory already exists: {$this->destination}");
            }

            // 5. Ordner final an den Zielort verschieben
            if (!rename($tempFolder, $this->destination)) {
                throw new Exception("Could not move processed folder to {$this->destination}");
            }

            echo "Successfully created plugin at: {$this->destination}\n";

        } catch (Exception $e) {
            // Cleanup: Bei einem Fehler wird der temporäre Ordner wieder gelöscht
            if (is_dir($tempFolder)) {
                $this->deleteDirectory($tempFolder);
            }
            throw $e;
        }
    }

    private function recurseCopy($source, $destination) 
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $dir = opendir($source);
        while (false !== ($file = readdir($dir))) {
            if ($file === '.' || $file === '..' || $file === '.git') {
                continue;
            }
            $srcFile = $source . DIRECTORY_SEPARATOR . $file;
            $dstFile = $destination . DIRECTORY_SEPARATOR . $file;

            if (is_dir($srcFile)) {
                $this->recurseCopy($srcFile, $dstFile);
            } else {
                copy($srcFile, $dstFile);
            }
        }
        closedir($dir);
    }

    private function renameFilesAndContent($folder) 
    {
        // 1. Zuerst die Inhalte aller Dateien anpassen
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS), 
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($items as $item) {
            if ($item->isFile()) {
                $content = file_get_contents($item->getRealPath());
                $newContent = $this->applyNamingLogic($content);
                
                // Nur speichern, wenn sich wirklich etwas geändert hat (bessere Performance)
                if ($content !== $newContent) {
                    file_put_contents($item->getRealPath(), $newContent);
                }
            }
        }

        // 2. Danach die Dateinamen und Ordner anpassen
        $this->renameItems($folder);
    }

    private function renameItems($folder) 
    {
        // WICHTIG: CHILD_FIRST stellt sicher, dass wir Dateien vor ihren übergeordneten Ordnern umbenennen
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS), 
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $oldPath = $item->getRealPath();
            $newName = $this->applyNamingLogic($item->getFilename());
            $newPath = dirname($oldPath) . DIRECTORY_SEPARATOR . $newName;

            if ($oldPath !== $newPath) {
                rename($oldPath, $newPath);
            }
        }
    }

    private function deleteDirectory($dir) 
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
        }
        rmdir($dir);
    }
    
    private function applyNamingLogic(string $input): string 
    {
        // Ersetzt exakte Übereinstimmungen, Capitalized Versionen und lowercase Versionen
        $result = str_replace($this->search, $this->replace, $input);
        $result = str_replace(ucfirst($this->search), ucfirst($this->replace), $result);
        return str_replace(strtolower($this->search), strtolower($this->replace), $result);
    }
}