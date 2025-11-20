<?php

namespace App\Services\Installer;

use App\Services\Installer\Repositories\AdminRepositoryInterface;
use App\Services\Installer\DTOs\EnvConfigDTO;
use App\Services\Installer\DTOs\DbConfigDTO;
use App\Services\Installer\DTOs\AdminDTO;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Helper\CommandRunner;
use App\Helper\EnvWriter;
use RuntimeException;

final class InstallerService
{
    public function __construct(
        private EnvWriter $envWriter,
        private CommandRunner $cmd,
        private AdminRepositoryInterface $admins,
    ) {}

    public function runPreflightChecks(): array
    {
        return [
            'php' => PHP_VERSION,
            'extensions' => [
                'mbstring' => extension_loaded('mbstring'),
                'openssl'  => extension_loaded('openssl'),
                'pdo'      => extension_loaded('pdo'),
                'curl'     => extension_loaded('curl'),
                'fileinfo' => extension_loaded('fileinfo'),
            ],
            'permissions' => [
                'storage_writable'   => is_writable(storage_path()),
                'bootstrap_writable' => is_writable(base_path('bootstrap/cache')),
                'env_writable'       => is_writable(base_path('.env')),
            ],
        ];
    }

    public function applyEnvironment(EnvConfigDTO $dto): void
    {
        $this->envWriter->setMany([
            'APP_NAME'        => $dto->appName,
            'APP_URL'         => $dto->appUrl,
            'APP_ENV'         => $dto->appEnv,
            'APP_DEBUG'       => $dto->appDebug ? 'true' : 'false',
            'CACHE_DRIVER'    => $dto->cacheDriver,
            'SESSION_DRIVER'  => $dto->sessionDriver,
            'QUEUE_CONNECTION' => $dto->queueConnection,
        ]);
    }

    public function testDatabaseConnection(DbConfigDTO $dto): void
    {
        $dsn = match ($dto->driver) {
            'mysql'  => "mysql:host={$dto->host};port={$dto->port};dbname={$dto->database}",
            default  => throw new RuntimeException('Unsupported DB driver'),
        };

        try {
            new \PDO($dsn, $dto->username, $dto->password);
        } catch (\Throwable $e) {
            throw new RuntimeException('DB connection failed: ' . $e->getMessage());
        }
    }
    public function reloadDatabaseConfigRuntime(DbConfigDTO $dto): void
    {
        config([
            'database.default' => $dto->driver,
            "database.connections.{$dto->driver}.host" => $dto->host,
            "database.connections.{$dto->driver}.port" => $dto->port,
            "database.connections.{$dto->driver}.database" => $dto->database,
            "database.connections.{$dto->driver}.username" => $dto->username,
            "database.connections.{$dto->driver}.password" => $dto->password,
        ]);

        DB::purge();
        DB::reconnect();
    }
    public function persistDatabaseConfig(DbConfigDTO $dto): void
    {
        $this->envWriter->setMany([
            'DB_CONNECTION' => $dto->driver,
            'DB_HOST'       => $dto->host,
            'DB_PORT'       => (string)$dto->port,
            'DB_DATABASE'   => $dto->database,
            'DB_USERNAME'   => $dto->username,
            'DB_PASSWORD'   => $dto->password,
        ]);
        config([
            'database.default' => $dto->driver,
            'database.connections.' . $dto->driver . '.host' => $dto->host,
        ]);
        DB::purge();
    }

    public function generateAppKey(): void
    {
        $this->cmd->call('key:generate', ['--force' => true]);
    }

    public function createStorageLink(): void
    {
        $this->cmd->call('storage:link');
    }

    public function migrate(): void
    {
        $this->cmd->call('migrate', ['--force' => true]);
    }

    public function createAdmin(AdminDTO $dto): void
    {
        if ($this->admins->existsByEmail($dto->email)) {
            return; 
        }
        if ($this->admins->existsByUsername($dto->username)) {
            return; 
        }
        $hashed = Hash::make($dto->password);
        $this->admins->createSuperAdmin($dto->name, $dto->email, $hashed,$dto->username);
    }

    public function finalizeInstallation(): void
    {
        if (Schema::hasTable('sessions')) {
            $this->envWriter->setMany(['SESSION_DRIVER' => 'database']);
        }
        if (Schema::hasTable('configs')) {
            $this->envWriter->setMany(['CACHE_DRIVER' => 'database']);
            $this->envWriter->setMany(['CACHE_STORE' => 'database']);
        }
        $this->envWriter->setMany(['APP_INSTALLED' => 'true']);

        $this->cmd->call('optimize:clear');
    }
}