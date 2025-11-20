<?php

namespace App\Helper;

use RuntimeException;

final class EnvWriter
{
    public function setMany(array $pairs): void
    {
        $path = base_path('.env');
        if (!is_file($path) || !is_writable($path)) {
            throw new RuntimeException('.env file is not writable.');
        }

        $content = file_get_contents($path);
        foreach ($pairs as $key => $value) {
            $escaped = $this->escapeValue((string)$value);
            if (preg_match("/^{$key}=.*$/m", $content)) {
                $content = preg_replace("/^{$key}=.*$/m", "{$key}={$escaped}", $content);
            } else {
                $content .= PHP_EOL . "{$key}={$escaped}";
            }
        }
        file_put_contents($path, $content);
    }

    private function escapeValue(string $value): string
    {
        if ($value === '' || str_contains($value, ' ') || str_contains($value, '#')) {
            return '"' . str_replace('"', '\"', $value) . '"';
        }
        return $value;
    }
}