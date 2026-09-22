<?php

declare(strict_types=1);

$environmentFile = dirname(__DIR__).'/.env';

if (! is_file($environmentFile)) {
    fwrite(STDOUT, "Hostinger post-deploy skipped: .env is not configured.\n");

    exit(0);
}

$environment = null;

foreach (file($environmentFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
    if (str_starts_with(trim($line), 'APP_ENV=')) {
        $environment = trim(substr($line, strlen('APP_ENV=')), " \t\n\r\0\x0B\"'");
        break;
    }
}

if ($environment !== 'production') {
    fwrite(STDOUT, "Hostinger post-deploy skipped outside production.\n");

    exit(0);
}

$commands = [
    [PHP_BINARY, 'artisan', 'migrate', '--force', '--no-interaction'],
    [PHP_BINARY, 'artisan', 'optimize', '--no-interaction'],
];

foreach ($commands as $command) {
    $escapedCommand = implode(' ', array_map('escapeshellarg', $command));
    passthru($escapedCommand, $exitCode);

    if ($exitCode !== 0) {
        fwrite(STDERR, "Production post-deploy command failed.\n");
        exit($exitCode);
    }
}
