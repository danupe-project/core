<?php

namespace Danupe\Core\Classes;

class Input
{
    /**
     * NEU: Diese Hilfsfunktion bündelt klassische Formular-Daten ($_REQUEST)
     * und moderne JSON-Payloads (php://input) zu einem einzigen Array.
     */
    private function getRequestData()
    {
        // Statisch, damit wir php://input pro Request nur einmal auslesen müssen
        static $mergedInput = null;

        if ($mergedInput === null) {
            $mergedInput = $_REQUEST;

            // Prüfen, ob es sich um einen JSON-Request handelt (z.B. von unserem Alpine.js fetch)
            $contentType = strtolower($_SERVER['CONTENT_TYPE'] ?? '');
            
            if (strpos($contentType, 'application/json') !== false) {
                $rawBody = file_get_contents('php://input');
                if (!empty($rawBody)) {
                    $jsonBody = json_decode($rawBody, true);
                    if (is_array($jsonBody)) {
                        // JSON Daten mit den eventuellen URL-Parametern zusammenführen
                        $mergedInput = array_merge($mergedInput, $jsonBody);
                    }
                }
            }
        }

        return $mergedInput;
    }

    public function get($key = null, $default = null)
    {
        // Greift auf die gebündelten Daten (inkl. JSON) zu
        $input = $this->getRequestData();

        array_walk_recursive($input, function (&$value) {
            // WICHTIG: Nur Strings escapen, damit JSON-Booleans (true/false) nicht kaputt gehen
            if (is_string($value)) {
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        });

        return danupe()->data()->get($input, $key, $default);
    }

    public function all()
    {
        // Greift auf die gebündelten Daten (inkl. JSON) zu
        $input = $this->getRequestData();

        return array_map(function ($value) {
            if (is_array($value)) {
                return array_map(function($subValue) {
                    return is_string($subValue) ? htmlspecialchars($subValue, ENT_QUOTES, 'UTF-8') : $subValue;
                }, $value);
            }
            return is_string($value) ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $value;
        }, $input);
    }

    public function has($key)
    {
        $input = $this->getRequestData();
        return isset($input[$key]);
    }

    public function only(array $keys)
    {
        $input = $this->all();
        return array_intersect_key($input, array_flip($keys));
    }

    public function except(array $keys)
    {
        $input = $this->all();
        return array_diff_key($input, array_flip($keys));
    }
}