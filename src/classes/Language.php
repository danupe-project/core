<?php

namespace Danupe\Core\Classes;

class Language
{
    private string $locale;
    private array $translations = [];

    public function __construct(string $locale = 'en')
    {
        $this->locale = $locale;
        $this->loadTranslations();
    }

    private function loadTranslations(): void
    {
        $this->translations = danupe()->config()->getAllByKey("language") ?? [];
    }

    public function get(string $key, array $replacements = []): string|false|array
    {
        // Struktur der geladenen Übersetzungen: [ pluginKey => [ locale => [ key => value ] ] ]
        foreach ($this->translations as $pluginKey => $locales) {
            if (!is_array($locales)) continue;
            $entries = danupe()->data()->get($locales, $this->locale, []);
            if (!is_array($entries)) continue;
            if (array_key_exists($key, $entries)) {
                $translation = $entries[$key];
                foreach ($replacements as $placeholder => $value) {
                    $translation = str_replace(":$placeholder", $value, $translation);
                }
                return $translation;
            }
        }
        return false; // Nichts gefunden
    }

    public function getAll(): array
    {
        return $this->translations;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getAllAvailableBackendLanguageKeys(bool $groupAsKey = false): array
    {

        $keys = [];

        foreach ($this->translations as $key => $group) {

            foreach ($group as $subKey => $value) {
                if ($groupAsKey) {
                    $keys[$subKey] = $subKey;
                } else {
                    $keys[] = $subKey;
                }
            }
        }
        return $keys;
    }
    public function getAllAvailableFrontendLanguageKeys(bool $groupAsKey = false): array
    {

        $keysFromEnv = explode(',', danupe()->env()->get('DANUPE_LANGUAGE_FRONTEND'));
        if ($groupAsKey) {
            foreach ($keysFromEnv as $key) {
                $keys[$key] = $key;
            }
        }else{
            $keys = $keysFromEnv;
        }

        return $keys;
    }
}
