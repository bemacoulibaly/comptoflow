<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\PrevisionService;
use Illuminate\Http\Request;
class DashboardApiController extends Controller {
    public function __construct(private DashboardService $ds, private PrevisionService $ps) {}
    public function index(Request $request) {
        $s = $request->user()->societe;
        $periode = $request->get('periode','mois');
        $stats = $this->ds->getStats($s,$periode);
        $prev  = $this->ps->projections($s,$stats['tresorerie']??0);
        return response()->json([
            'periode'=>$periode,
            'kpis'=>['tresorerie'=>round($stats['tresorerie']??0,2),'recettes'=>round($stats['recettes']??0,2),'charges'=>round($stats['charges']??0,2),'resultat'=>round($stats['resultat']??0,2),'tva_nette'=>round($stats['tva']??0,2),'factures_retard'=>$stats['enRetard']??0,'ecritures_valider'=>$stats['aValider']??0],
            'evolution_mensuelle'=>$stats['evolutionMensuelle']??[],
            'previsions_6mois'=>$prev['projections'],
            'tendance_tresorerie'=>$prev['tendance'],
            'echeances'=>collect($stats['echeances']??[])->map(fn($e)=>['titre'=>$e->titre,'date_echeance'=>$e->date_echeance->format('Y-m-d'),'type'=>$e->type,'priorite'=>$e->priorite]),
        ]);
    }
}
