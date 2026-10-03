<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SauvegarderBase extends Command
{
    protected $signature   = 'comptoflow:sauvegarder
                                {--garder=30 : Nombre de jours de sauvegardes à conserver}';
    protected $description = 'Exporte la base de données MySQL en fichier SQL daté, et purge les vieilles sauvegardes.';

    public function handle(): int
    {
        $db   = config('database.connections.mysql');
        $date = now()->format('Y-m-d_His');
        $nom  = "backup_{$date}.sql";
        $dir  = storage_path('app/backups');

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $fichier = "{$dir}/{$nom}";

        // Construction de la commande mysqldump
        $host   = escapeshellarg($db['host']);
        $port   = escapeshellarg($db['port'] ?? '3306');
        $user   = escapeshellarg($db['username']);
        $pass   = $db['password'] ? '-p' . escapeshellarg($db['password']) : '';
        $dbname = escapeshellarg($db['database']);
        $out    = escapeshellarg($fichier);

        $cmd = "mysqldump -h {$host} -P {$port} -u {$user} {$pass} {$dbname} > {$out} 2>&1";
        exec($cmd, $output, $code);

        if ($code !== 0 || !file_exists($fichier) || filesize($fichier) < 100) {
            $this->error("Échec de la sauvegarde. Code: {$code}");
            $this->error(implode("\n", $output));
            return self::FAILURE;
        }

        $taille = round(filesize($fichier) / 1024, 1);
        $this->info("✅ Sauvegarde créée : {$nom} ({$taille} Ko)");

        // Purger les sauvegardes plus vieilles que --garder jours
        $garder = (int) $this->option('garder');
        $purges = 0;
        foreach (glob("{$dir}/backup_*.sql") as $ancien) {
            if (filemtime($ancien) < now()->subDays($garder)->timestamp) {
                unlink($ancien);
                $purges++;
            }
        }

        if ($purges > 0) {
            $this->line("🗑  {$purges} ancienne(s) sauvegarde(s) supprimée(s) (> {$garder} jours).");
        }

        return self::SUCCESS;
    }
}
