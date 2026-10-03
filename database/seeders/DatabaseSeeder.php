<?php

namespace Database\Seeders;

use App\Models\Compte;
use App\Models\Societe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SocieteSeeder::class,
            OhadaSeeder::class,
        ]);
    }
}

// ────────────────────────────────────────────────────────────
class SocieteSeeder extends Seeder
{
    public function run(): void
    {
        $societe = Societe::create([
            'raison_sociale'      => 'Cabinet Coulibaly & Associés',
            'numero_contribuable' => 'CI-ABJ-2019-00842-X',
            'regime_fiscal'       => 'reel_simplifie',
            'devise'              => 'XOF',
            'taux_tva'            => 18.00,
            'adresse'             => 'Avenue Botreau-Roussel, Plateau',
            'ville'               => 'Abidjan',
            'telephone'           => '+225 07 00 00 00',
            'email'               => 'contact@coulibaly-comptable.ci',
        ]);

        // Admin
        User::create([
            'nom'        => 'Coulibaly',
            'prenom'     => 'Kofi',
            'email'      => 'admin@comptoflow.ci',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'societe_id' => $societe->id,
        ]);

        // Éditeur
        User::create([
            'nom'        => 'Brou',
            'prenom'     => 'Ama',
            'email'      => 'editeur@comptoflow.ci',
            'password'   => Hash::make('password'),
            'role'       => 'editeur',
            'societe_id' => $societe->id,
        ]);
    }
}

// ────────────────────────────────────────────────────────────
class OhadaSeeder extends Seeder
{
    public function run(): void
    {
        $societes = Societe::all();

        foreach ($societes as $societe) {
            $this->seedComptes($societe->id);
        }
    }

    private function seedComptes(int $societeId): void
    {
        $comptes = [
            // ── CLASSE 1 — Ressources durables ─────────────
            ['numero' => '101',  'libelle' => 'Capital social',                        'classe' => '1', 'type' => 'passif'],
            ['numero' => '102',  'libelle' => 'Capital non appelé',                    'classe' => '1', 'type' => 'passif'],
            ['numero' => '104',  'libelle' => 'Primes liées au capital social',        'classe' => '1', 'type' => 'passif'],
            ['numero' => '106',  'libelle' => 'Écarts de réévaluation',                'classe' => '1', 'type' => 'passif'],
            ['numero' => '111',  'libelle' => 'Réserve légale',                        'classe' => '1', 'type' => 'passif'],
            ['numero' => '112',  'libelle' => 'Réserves statutaires ou contractuelles','classe' => '1', 'type' => 'passif'],
            ['numero' => '118',  'libelle' => 'Autres réserves',                       'classe' => '1', 'type' => 'passif'],
            ['numero' => '121',  'libelle' => 'Report à nouveau (solde créditeur)',    'classe' => '1', 'type' => 'passif'],
            ['numero' => '129',  'libelle' => 'Report à nouveau (solde débiteur)',     'classe' => '1', 'type' => 'actif'],
            ['numero' => '131',  'libelle' => 'Résultat net de l\'exercice — bénéfice','classe'=>'1','type'=>'passif'],
            ['numero' => '139',  'libelle' => 'Résultat net de l\'exercice — perte',  'classe' => '1', 'type' => 'actif'],
            ['numero' => '141',  'libelle' => 'Subventions d\'équipement',            'classe' => '1', 'type' => 'passif'],
            ['numero' => '151',  'libelle' => 'Provisions pour risques',              'classe' => '1', 'type' => 'passif'],
            ['numero' => '161',  'libelle' => 'Emprunts obligataires',                'classe' => '1', 'type' => 'passif'],
            ['numero' => '162',  'libelle' => 'Emprunts et dettes auprès des établissements de crédit','classe'=>'1','type'=>'passif'],
            ['numero' => '163',  'libelle' => 'Emprunts et dettes financières divers','classe' => '1', 'type' => 'passif'],
            ['numero' => '164',  'libelle' => 'Dettes de location acquisition',       'classe' => '1', 'type' => 'passif'],
            ['numero' => '165',  'libelle' => 'Dépôts et cautionnements reçus',       'classe' => '1', 'type' => 'passif'],

            // ── CLASSE 2 — Actif immobilisé ─────────────────
            ['numero' => '211',  'libelle' => 'Frais de recherche et de développement','classe'=>'2','type'=>'actif'],
            ['numero' => '212',  'libelle' => 'Brevets, licences, concessions',       'classe' => '2', 'type' => 'actif'],
            ['numero' => '213',  'libelle' => 'Logiciels',                            'classe' => '2', 'type' => 'actif'],
            ['numero' => '221',  'libelle' => 'Terrains',                             'classe' => '2', 'type' => 'actif'],
            ['numero' => '222',  'libelle' => 'Aménagements de terrains',             'classe' => '2', 'type' => 'actif'],
            ['numero' => '231',  'libelle' => 'Bâtiments sur sol propre',             'classe' => '2', 'type' => 'actif'],
            ['numero' => '232',  'libelle' => 'Bâtiments sur sol d\'autrui',          'classe' => '2', 'type' => 'actif'],
            ['numero' => '241',  'libelle' => 'Matériel et outillage industriel',     'classe' => '2', 'type' => 'actif'],
            ['numero' => '244',  'libelle' => 'Matériel de bureau et informatique',   'classe' => '2', 'type' => 'actif'],
            ['numero' => '245',  'libelle' => 'Mobilier de bureau et matériel de bureau','classe'=>'2','type'=>'actif'],
            ['numero' => '248',  'libelle' => 'Autres matériels et outillages',       'classe' => '2', 'type' => 'actif'],
            ['numero' => '251',  'libelle' => 'Titres de participation',              'classe' => '2', 'type' => 'actif'],
            ['numero' => '261',  'libelle' => 'Prêts et créances rattachés à des participations','classe'=>'2','type'=>'actif'],
            ['numero' => '281',  'libelle' => 'Amortissements — Immobilisations incorporelles','classe'=>'2','type'=>'passif'],
            ['numero' => '284',  'libelle' => 'Amortissements — Matériels',           'classe' => '2', 'type' => 'passif'],

            // ── CLASSE 3 — Stocks ────────────────────────────
            ['numero' => '31',   'libelle' => 'Marchandises',                         'classe' => '3', 'type' => 'actif'],
            ['numero' => '32',   'libelle' => 'Matières premières et fournitures liées','classe'=>'3','type'=>'actif'],
            ['numero' => '33',   'libelle' => 'Autres approvisionnements',            'classe' => '3', 'type' => 'actif'],
            ['numero' => '35',   'libelle' => 'Stocks de produits finis',             'classe' => '3', 'type' => 'actif'],
            ['numero' => '37',   'libelle' => 'Stocks de produits en cours',          'classe' => '3', 'type' => 'actif'],

            // ── CLASSE 4 — Tiers ──────────────────────────────
            ['numero' => '401',  'libelle' => 'Fournisseurs, dettes en compte',       'classe' => '4', 'type' => 'passif'],
            ['numero' => '4011', 'libelle' => 'Fournisseurs — Achats de biens et services','classe'=>'4','type'=>'passif'],
            ['numero' => '402',  'libelle' => 'Fournisseurs — Effets à payer',        'classe' => '4', 'type' => 'passif'],
            ['numero' => '408',  'libelle' => 'Fournisseurs — Factures non parvenues','classe' => '4', 'type' => 'passif'],
            ['numero' => '409',  'libelle' => 'Fournisseurs débiteurs (avances, RRR à obtenir)','classe'=>'4','type'=>'actif'],
            ['numero' => '411',  'libelle' => 'Clients',                              'classe' => '4', 'type' => 'actif'],
            ['numero' => '4111', 'libelle' => 'Clients — Ventes de biens et services','classe'=>'4','type'=>'actif'],
            ['numero' => '412',  'libelle' => 'Clients — Effets à recevoir',          'classe' => '4', 'type' => 'actif'],
            ['numero' => '418',  'libelle' => 'Clients — Produits non encore facturés','classe'=>'4','type'=>'actif'],
            ['numero' => '419',  'libelle' => 'Clients créditeurs (avances, RRR à accorder)','classe'=>'4','type'=>'passif'],
            ['numero' => '421',  'libelle' => 'Personnel — Rémunérations dues',       'classe' => '4', 'type' => 'passif'],
            ['numero' => '422',  'libelle' => 'Personnel — Avances et acomptes consentis','classe'=>'4','type'=>'actif'],
            ['numero' => '424',  'libelle' => 'Personnel — Œuvres sociales',          'classe' => '4', 'type' => 'passif'],
            ['numero' => '425',  'libelle' => 'Personnel — Dépôts',                   'classe' => '4', 'type' => 'passif'],
            ['numero' => '431',  'libelle' => 'Sécurité sociale (CNPS)',              'classe' => '4', 'type' => 'passif'],
            ['numero' => '432',  'libelle' => 'Caisses de retraite',                  'classe' => '4', 'type' => 'passif'],
            ['numero' => '441',  'libelle' => 'État — TVA collectée',                 'classe' => '4', 'type' => 'passif'],
            ['numero' => '4441', 'libelle' => 'État — TVA collectée sur ventes',      'classe' => '4', 'type' => 'passif'],
            ['numero' => '445',  'libelle' => 'État — TVA déductible',                'classe' => '4', 'type' => 'actif'],
            ['numero' => '4451', 'libelle' => 'État — TVA déductible sur achats',     'classe' => '4', 'type' => 'actif'],
            ['numero' => '4452', 'libelle' => 'État — TVA déductible sur immobilisations','classe'=>'4','type'=>'actif'],
            ['numero' => '447',  'libelle' => 'État — Autres impôts et taxes',        'classe' => '4', 'type' => 'passif'],
            ['numero' => '448',  'libelle' => 'État — Charges à payer et produits à recevoir','classe'=>'4','type'=>'passif'],
            ['numero' => '451',  'libelle' => 'Groupe',                               'classe' => '4', 'type' => 'actif'],
            ['numero' => '461',  'libelle' => 'Débiteurs divers',                     'classe' => '4', 'type' => 'actif'],
            ['numero' => '462',  'libelle' => 'Créditeurs divers',                    'classe' => '4', 'type' => 'passif'],
            ['numero' => '471',  'libelle' => 'Débiteurs — Charges constatées d\'avance','classe'=>'4','type'=>'actif'],
            ['numero' => '476',  'libelle' => 'Créditeurs — Produits constatés d\'avance','classe'=>'4','type'=>'passif'],
            ['numero' => '491',  'libelle' => 'Dépréciation des comptes clients',     'classe' => '4', 'type' => 'passif'],

            // ── CLASSE 5 — Trésorerie ─────────────────────────
            ['numero' => '511',  'libelle' => 'Valeurs à l\'encaissement',            'classe' => '5', 'type' => 'actif'],
            ['numero' => '512',  'libelle' => 'Banque',                               'classe' => '5', 'type' => 'actif'],
            ['numero' => '5121', 'libelle' => 'SGBCI — Compte courant',               'classe' => '5', 'type' => 'actif'],
            ['numero' => '5122', 'libelle' => 'BIAO — Compte courant',                'classe' => '5', 'type' => 'actif'],
            ['numero' => '514',  'libelle' => 'Chèques postaux',                      'classe' => '5', 'type' => 'actif'],
            ['numero' => '521',  'libelle' => 'Banque — Crédits de trésorerie',       'classe' => '5', 'type' => 'passif'],
            ['numero' => '531',  'libelle' => 'CCP et comptes assimilés',             'classe' => '5', 'type' => 'actif'],
            ['numero' => '570',  'libelle' => 'Caisse',                               'classe' => '5', 'type' => 'actif'],
            ['numero' => '571',  'libelle' => 'Caisse siège social',                  'classe' => '5', 'type' => 'actif'],
            ['numero' => '572',  'libelle' => 'Caisse agence',                        'classe' => '5', 'type' => 'actif'],
            ['numero' => '581',  'libelle' => 'Virements de fonds internes',          'classe' => '5', 'type' => 'actif'],
            ['numero' => '590',  'libelle' => 'Dépréciation des valeurs mobilières',  'classe' => '5', 'type' => 'passif'],

            // ── CLASSE 6 — Charges ───────────────────────────
            ['numero' => '601',  'libelle' => 'Achats de marchandises',               'classe' => '6', 'type' => 'charge'],
            ['numero' => '602',  'libelle' => 'Achats de matières premières',         'classe' => '6', 'type' => 'charge'],
            ['numero' => '604',  'libelle' => 'Achats d\'études et de prestations de services','classe'=>'6','type'=>'charge'],
            ['numero' => '605',  'libelle' => 'Achats de matériels, équipements et travaux','classe'=>'6','type'=>'charge'],
            ['numero' => '608',  'libelle' => 'Autres achats',                        'classe' => '6', 'type' => 'charge'],
            ['numero' => '61',   'libelle' => 'Transports',                           'classe' => '6', 'type' => 'charge'],
            ['numero' => '611',  'libelle' => 'Transports sur achats',                'classe' => '6', 'type' => 'charge'],
            ['numero' => '612',  'libelle' => 'Transports sur ventes',                'classe' => '6', 'type' => 'charge'],
            ['numero' => '613',  'libelle' => 'Transports pour le compte de tiers',   'classe' => '6', 'type' => 'charge'],
            ['numero' => '614',  'libelle' => 'Transports du personnel',              'classe' => '6', 'type' => 'charge'],
            ['numero' => '618',  'libelle' => 'Autres frais de transports',           'classe' => '6', 'type' => 'charge'],
            ['numero' => '621',  'libelle' => 'Locations et charges locatives',       'classe' => '6', 'type' => 'charge'],
            ['numero' => '622',  'libelle' => 'Redevances de crédit-bail',            'classe' => '6', 'type' => 'charge'],
            ['numero' => '623',  'libelle' => 'Redevances pour concessions, brevets, licences','classe'=>'6','type'=>'charge'],
            ['numero' => '624',  'libelle' => 'Entretien, réparations et maintenance','classe' => '6', 'type' => 'charge'],
            ['numero' => '625',  'libelle' => 'Primes d\'assurances',                 'classe' => '6', 'type' => 'charge'],
            ['numero' => '626',  'libelle' => 'Études, recherches et documentation',  'classe' => '6', 'type' => 'charge'],
            ['numero' => '627',  'libelle' => 'Publicité, publications, relations publiques','classe'=>'6','type'=>'charge'],
            ['numero' => '628',  'libelle' => 'Télécommunications',                   'classe' => '6', 'type' => 'charge'],
            ['numero' => '629',  'libelle' => 'Autres services extérieurs',           'classe' => '6', 'type' => 'charge'],
            ['numero' => '631',  'libelle' => 'Frais bancaires',                      'classe' => '6', 'type' => 'charge'],
            ['numero' => '632',  'libelle' => 'Rémunérations d\'intermédiaires',      'classe' => '6', 'type' => 'charge'],
            ['numero' => '633',  'libelle' => 'Frais de formation du personnel',      'classe' => '6', 'type' => 'charge'],
            ['numero' => '634',  'libelle' => 'Redevances pour brevets, licences',    'classe' => '6', 'type' => 'charge'],
            ['numero' => '635',  'libelle' => 'Cotisations',                          'classe' => '6', 'type' => 'charge'],
            ['numero' => '637',  'libelle' => 'Charges de personnel extérieur',       'classe' => '6', 'type' => 'charge'],
            ['numero' => '641',  'libelle' => 'Rémunérations du personnel',           'classe' => '6', 'type' => 'charge'],
            ['numero' => '642',  'libelle' => 'Avantages en nature',                  'classe' => '6', 'type' => 'charge'],
            ['numero' => '643',  'libelle' => 'Indemnités et avantages divers',       'classe' => '6', 'type' => 'charge'],
            ['numero' => '644',  'libelle' => 'Charges sociales',                     'classe' => '6', 'type' => 'charge'],
            ['numero' => '645',  'libelle' => 'Charges de retraite',                  'classe' => '6', 'type' => 'charge'],
            ['numero' => '648',  'libelle' => 'Autres charges de personnel',          'classe' => '6', 'type' => 'charge'],
            ['numero' => '651',  'libelle' => 'Redevances pour concessions (charges financières)','classe'=>'6','type'=>'charge'],
            ['numero' => '661',  'libelle' => 'Intérêts des emprunts',                'classe' => '6', 'type' => 'charge'],
            ['numero' => '671',  'libelle' => 'Pertes sur créances irrécouvrables',   'classe' => '6', 'type' => 'charge'],
            ['numero' => '681',  'libelle' => 'Dotations aux amortissements',         'classe' => '6', 'type' => 'charge'],
            ['numero' => '691',  'libelle' => 'Participation des travailleurs',       'classe' => '6', 'type' => 'charge'],
            ['numero' => '695',  'libelle' => 'Impôts sur les bénéfices (BIC)',       'classe' => '6', 'type' => 'charge'],
            ['numero' => '697',  'libelle' => 'Contributions et taxes assises sur les salaires','classe'=>'6','type'=>'charge'],

            // ── CLASSE 7 — Produits ───────────────────────────
            ['numero' => '701',  'libelle' => 'Ventes de marchandises',               'classe' => '7', 'type' => 'produit'],
            ['numero' => '702',  'libelle' => 'Ventes de produits finis',             'classe' => '7', 'type' => 'produit'],
            ['numero' => '703',  'libelle' => 'Ventes de produits intermédiaires',    'classe' => '7', 'type' => 'produit'],
            ['numero' => '704',  'libelle' => 'Ventes de produits résiduels',         'classe' => '7', 'type' => 'produit'],
            ['numero' => '705',  'libelle' => 'Travaux facturés',                     'classe' => '7', 'type' => 'produit'],
            ['numero' => '706',  'libelle' => 'Services vendus (prestations)',        'classe' => '7', 'type' => 'produit'],
            ['numero' => '707',  'libelle' => 'Produits accessoires',                 'classe' => '7', 'type' => 'produit'],
            ['numero' => '711',  'libelle' => 'Variation de stocks de produits finis','classe'=>'7','type'=>'produit'],
            ['numero' => '721',  'libelle' => 'Production immobilisée',               'classe' => '7', 'type' => 'produit'],
            ['numero' => '731',  'libelle' => 'Variations de stocks de marchandises', 'classe' => '7', 'type' => 'produit'],
            ['numero' => '741',  'libelle' => 'Subventions d\'exploitation',          'classe' => '7', 'type' => 'produit'],
            ['numero' => '751',  'libelle' => 'Revenus des créances et valeurs assimilées','classe'=>'7','type'=>'produit'],
            ['numero' => '752',  'libelle' => 'Revenus de valeurs mobilières de placement','classe'=>'7','type'=>'produit'],
            ['numero' => '761',  'libelle' => 'Gains de change',                      'classe' => '7', 'type' => 'produit'],
            ['numero' => '771',  'libelle' => 'Gains sur cessions d\'immobilisations','classe'=>'7','type'=>'produit'],
            ['numero' => '781',  'libelle' => 'Reprises d\'amortissements',           'classe' => '7', 'type' => 'produit'],
            ['numero' => '791',  'libelle' => 'Reprises de provisions',               'classe' => '7', 'type' => 'produit'],
        ];

        foreach ($comptes as $compte) {
            Compte::create(array_merge($compte, ['societe_id' => $societeId]));
        }
    }
}
