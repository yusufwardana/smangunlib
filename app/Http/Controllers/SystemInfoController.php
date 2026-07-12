<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SystemInfoService;

class SystemInfoController extends Controller
{
    protected $infoService;

    public function __construct(SystemInfoService $infoService)
    {
        $this->infoService = $infoService;
    }

    public function index()
    {
        $app = $this->infoService->getAppInfo();
        $server = $this->infoService->getServerInfo();
        $extensions = $this->infoService->getPhpExtensions();
        $permissions = $this->infoService->getFolderPermissions();
        $dbInfo = $this->infoService->getDatabaseInfo();
        $stats = $this->infoService->getStatistics();
        $health = $this->infoService->getHealthCheck();

        return view('system.info.index', compact('app', 'server', 'extensions', 'permissions', 'dbInfo', 'stats', 'health'));
    }
}
