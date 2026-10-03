<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service) {}

    public function index(Request $request)
    {
        $societe = auth()->user()->societe;
        $periode = $request->get('periode', 'mois');
        $stats   = $this->service->getStats($societe, $periode);

        return view('dashboard.index', compact('stats', 'periode'));
    }
}
