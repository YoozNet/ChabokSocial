<?php
namespace App\Http\Controllers\Installer;

use App\Http\Requests\Installer\StepAdminRequest;
use App\Http\Requests\Installer\StepEnvRequest;
use App\Http\Requests\Installer\StepDbRequest;
use App\Services\Installer\DTOs\EnvConfigDTO;
use App\Services\Installer\DTOs\DbConfigDTO;
use App\Services\Installer\InstallerService;
use App\Services\Installer\DTOs\AdminDTO;
use App\Http\Controllers\Controller;

final class InstallController extends Controller
{
    public function __construct(private InstallerService $svc) {}

    public function intro()
    { 
        return view('installer.intro');
    }

    public function showPreflight()
    { 
        $report = $this->svc->runPreflightChecks();
        return view('installer.preflight', compact('report'));
    }
    public function runPreflight()
    {
        return redirect()->route('install.db');
    }

    public function showDatabase()
    {
        return view('installer.database');
    }
    public function applyDatabase(StepDbRequest $request)
    {
        $dto = new DbConfigDTO(
            driver: $request->string('db_connection')->toString(),
            host: $request->string('db_host')->toString(),
            port: $request->integer('db_port'),
            database: $request->string('db_database')->toString(),
            username: $request->string('db_username')->toString(),
            password: (string) $request->input('db_password', '')
        );

        $this->svc->testDatabaseConnection($dto);
        $this->svc->persistDatabaseConfig($dto);
        $this->svc->reloadDatabaseConfigRuntime($dto);
        $this->svc->migrate();
        $this->svc->generateAppKey();
        $this->svc->createStorageLink();

        return redirect()->route('install.env')
            ->with('ok', 'اتصال دیتابیس با موفقیت برقرار شد. حالا کلید و Storage را بسازید.');
    }

    public function showEnv()
    {
        return view('installer.env');
    }
    public function applyEnv(StepEnvRequest $request)
    {
        $dto = new EnvConfigDTO(
            appName: $request->string('app_name')->toString(),
            appUrl: $request->string('app_url')->toString(),
            appEnv: 'production',
            appDebug: false,
            cacheDriver: $request->string('cache_driver')->toString(),
            sessionDriver: $request->string('session_driver')->toString(),
            queueConnection: $request->string('queue_connection')->toString(),
        );

        $this->svc->applyEnvironment($dto);

        return redirect()->route('install.admin')
            ->with('ok', 'تنظیمات اولیه ذخیره شد.');
    }
    
    public function showAdmin()
    {
        return view('installer.admin');
    }
    public function createAdmin(StepAdminRequest $request)
    {
        $dto = new AdminDTO(
            name: $request->string('name')->toString(),
            email: $request->string('email')->toString(),
            password: $request->string('password')->toString(),
            username: $request->string('username')->toString(),
        );

        $this->svc->createAdmin($dto);

        return redirect()->route('install.done')
            ->with('ok', 'ادمین اولیه ایجاد شد.');
    }

    public function done()
    {
        $this->svc->finalizeInstallation();
        return view('installer.done');
    }
}