<?php
declare(strict_types=1);

namespace ViteHelper\Utilities;

use Nette\Utils\Strings;
use stdClass;

class ManifestRecord
{
    private string $key;

    private stdClass $chunk;

    /**
     * Default constructor
     *
     * @param string $key The unique key for this record
     * @param \stdClass $chunk The chunks
     * @see https://vitejs.dev/guide/backend-integration.html
     */
    public function __construct(string $key, stdClass $chunk)
    {
        $this->key = $key;
        $this->chunk = $chunk;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * The current Record is an entry
     *
     * @return bool
     */
    public function isEntry(): bool
    {
        if (!empty($this->chunk->isEntry)) {
            return (bool)$this->chunk->isEntry;
        }

        return false;
    }

    /**
     * The current Record is legacy
     *
     * @return bool
     */
    public function isLegacy(): bool
    {
        return Strings::contains($this->chunk->file, 'legacy');
    }

    /**
     * The current Record is polyfill
     *
     * @return bool
     */
    public function isPolyfill(): bool
    {
        return Strings::contains($this->chunk->file, 'polyfills');
    }

    /**
     * The current Record is a javascript file
     *
     * @return bool
     */
    public function isJavascript(): bool
    {
        return Strings::endsWith($this->chunk->file, '.js');
    }

    /**
     * The current Record is a stylesheet file
     *
     * @return bool
     */
    public function isStylesheet(): bool
    {
        return Strings::endsWith((string)$this->chunk->file, '.css');
    }

    /**
     * Returns the files (URL) relative path
     *
     * @return string
     */
    public function url(): string
    {
        return '/' . $this->chunk->file;
    }

    /**
     * Returns all dependent CSS file
     *
     * @return array
     */
    public function getCss(): array
    {
        $files = $this->chunk?->css ?? [];
        if (!count($files)) {
            return $files;
        }

        return array_map(function ($file) {
            $file = trim($file);

            return '/' . ltrim($file, '/');
        }, $files);
    }

    /**
     * Check whether this record is a javascript entry point
     * and should be appended to the script block.
     *
     * @return bool
     */
    public function isEntryScript(): bool
    {
        return $this->isEntry() && $this->isJavascript();
    }

    /**
     * Check whether this record is an entry script and a module file,
     *   i.e. not a polyfill or legacy build
     *
     * @return bool
     */
    public function isModuleEntryScript(): bool
    {
        return $this->isEntry() && $this->isJavascript() && !$this->isLegacy() && !$this->isPolyfill();
    }
}
