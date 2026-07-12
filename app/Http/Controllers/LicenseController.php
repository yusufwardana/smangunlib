<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LicenseService;
use App\Models\License;

class LicenseController extends Controller
{
    protected $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    public function index()
    {
        $currentLicense = $this->licenseService->checkCurrentLicense();
        $history = License::orderBy('created_at', 'desc')->get();
        return view('system.license.index', compact('currentLicense', 'history'));
    }

    public function activate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'nama_sekolah' => 'required|string',
            'email' => 'required|email',
        ]);

        try {
            $this->licenseService->activate(
                $request->license_key,
                $request->nama_sekolah,
                $request->email
            );
            return back()->with('success', 'Lisensi berhasil diaktivasi!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
