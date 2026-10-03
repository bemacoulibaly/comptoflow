<?php
namespace App\Events;
use App\Http\Resources\FactureResource;
use App\Models\Facture;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class FactureCree implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public Facture $facture) {}
    public function broadcastOn(): array { return [new PrivateChannel('societe.'.$this->facture->societe_id)]; }
    public function broadcastAs(): string { return 'facture.creee'; }
    public function broadcastWith(): array {
        return ['facture'=>(new FactureResource($this->facture->load('tiers')))->resolve(),'message'=>"Facture {$this->facture->numero} créée par {$this->facture->user->nom_complet}."];
    }
}
