<?php
namespace Danupe\Core\Classes;

class Cache
{
    private string $cacheDir;
    private int $defaultTtl = 3600; // one hour

    public function __construct()
    {
        $this->cacheDir = sys_get_temp_dir() . '/opcache_storage/';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    private function getFilePath(string $key): string
    {
        return $this->cacheDir . 'cache_' . md5($key) . '.php';
    }

    public function set(string $key, mixed $value, ?int $ttl = null): void
    {
        $file = $this->getFilePath($key);
        $ttl = $ttl ?? $this->defaultTtl;

        $data = [
            'expires' => time() + $ttl,
            'content' => $value
        ];

        $content = "<?php\nreturn " . var_export($data, true) . ";";
        file_put_contents($file, $content);

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($file, true);
        }
    }

    public function get(string $key): mixed
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return null;
        }

        $data = include $file;

        if (time() > $data['expires']) {
            $this->delete($key); // Datei löschen, wenn abgelaufen
            return null;
        }

        return $data['content'];
    }

    public function has(string $key): bool
    {
        return ($this->get($key) !== null);
    }

    public function delete(string $key): void
    {
        $file = $this->getFilePath($key);
        if (file_exists($file)) {
            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($file, true);
            }
            unlink($file);
        }
    }
}