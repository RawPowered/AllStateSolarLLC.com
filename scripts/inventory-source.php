<?php
declare(strict_types=1);

/**
 * AllState Thasit code fingerprint (READ-ONLY, CLI ONLY).
 * Run in an authenticated hosting terminal against the private app root:
 *   php scripts/inventory-source.php /path/to/allstate-app /path/to/public_html > allstate-inventory.json
 *
 * Produces file paths, lengths and SHA-256 only. Does not output file contents,
 * DB data, OAuth secrets, environment values, PHP configuration or private storage.
 * Review output locally before sharing; do not place it in public_html.
 */
if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only\n");
    exit(2);
}
if ($argc !== 3) {
    fwrite(STDERR, "Usage: php inventory-source.php /path/to/allstate-app /path/to/public_html\n");
    exit(2);
}
$roots = ['application' => realpath($argv[1]), 'webroot' => realpath($argv[2])];
foreach ($roots as $name => $root) {
    if (!is_string($root) || !is_dir($root)) {
        fwrite(STDERR, "Missing root: " . $name . "\n");
        exit(2);
    }
}
$entries = [];
$protected = [
    'storage', 'private', 'uploads', 'upload', 'backups', 'backup', 'logs', 'log',
    'sessions', 'cache', '.git', 'vendor', 'node_modules', '.well-known',
    'secrets', 'credentials', 'keys', 'tmp', 'temp',
];
foreach ($roots as $name => $root) {
    $walker = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
            static function (SplFileInfo $item) use ($protected): bool {
                if ($item->isLink()) return false;
                $leaf = strtolower($item->getFilename());
                if ($item->isDir()) return !in_array($leaf, $protected, true);
                return $item->isFile();
            }
        )
    );
    foreach ($walker as $item) {
        if (!$item->isFile() || $item->isLink()) continue;
        $path = substr($item->getPathname(), strlen($root) + 1);
        $parts = preg_split('~[/\\\\]+~', strtolower($path));
        if (array_intersect($parts, $protected)) continue;
        $leaf = strtolower($item->getFilename());
        if (preg_match('/(^\\.|config|secret|credential|token|oauth|\.env|\.pem|\.key|password|backup|dump|session)/i', $leaf)) continue;
        $ext = strtolower(pathinfo($leaf, PATHINFO_EXTENSION));
        if (!in_array($ext, ['php', 'css', 'js'], true) && $leaf !== '.htaccess') continue;
        if (!is_readable($item->getPathname())) {
            fwrite(STDERR, "Unreadable code file; inventory incomplete.\n");
            exit(3);
        }
        $entries[] = [
            'root' => $name,
            'path' => str_replace('\\\\', '/', $path),
            'bytes' => $item->getSize(),
            'sha256' => hash_file('sha256', $item->getPathname()),
        ];
    }
}
usort($entries, static fn(array $a, array $b): int => [$a['root'], $a['path']] <=> [$b['root'], $b['path']]);
echo json_encode([
    'type' => 'allstate-thasit-code-fingerprint-v1',
    'generated_utc' => gmdate('c'),
    'content_included' => false,
    'database_access' => false,
    'files' => $entries,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
