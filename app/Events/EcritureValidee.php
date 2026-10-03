<?php
namespace App\Events;
use App\Http\Resources\EcritureResource;
use App\Models\Ecriture;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class EcritureValidee implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public Ecriture $ecriture) {}
    public function broadcastOn(): array { return [new PrivateChannel('societe.'.$this->ecriture->societe_id)]; }
    public function broadcastAs(): string { return 'ecriture.validee'; }
    public function broadcastWith(): array {
        return ['ecriture'=>(new EcritureResource($this->ecriture->load('lignes.compte')))->resolve(),'message'=>"L'écriture {$this->ecriture->numero_piece} a été validée par {$this->ecriture->user->nom_complet}."];
    }
}
