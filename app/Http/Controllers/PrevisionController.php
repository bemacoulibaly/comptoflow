<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\PrevisionService;
use Illuminate\Http\Request;

class PrevisionController extends Controller
{
    public function __construct(
        private PrevisionService  $previsionService,
        private DashboardService  $dashboardService,
    ) {}

    public function index()
    {
        $societe      = auth()->user()->societe;
        $stats        = $this->dashboardService->getStats($societe, 'mois');
        $soldeCourant = $stats['tresorerie'] ?? 0;

        $donnees = $this->previsionService->projections($societe, $soldeCourant);

        return view('previsions.index', array_merge($donnees, [
            'soldeCourant' => $soldeCourant,
        ]));
    }
}
