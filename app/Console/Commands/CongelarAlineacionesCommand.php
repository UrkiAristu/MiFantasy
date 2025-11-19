<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jornada;
use App\Services\CongelarAlineacionesService;
use Carbon\Carbon;

class CongelarAlineacionesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fantasy:congelar-alineaciones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Congela las alineaciones de las jornadas cuyo cierre ha pasado';

    public function __construct(protected CongelarAlineacionesService $service)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
         // Jornadas cuya fecha de cierre ya ha pasado y aún no están marcadas como congeladas
        $jornadas = Jornada::whereNotNull('fecha_cierre_alineaciones')
            ->where('fecha_cierre_alineaciones', '<=', Carbon::now())
            ->where('alineaciones_congeladas', false)
            ->get();

        if ($jornadas->isEmpty()) {
            $this->info("No hay jornadas para congelar.");
            return Command::SUCCESS;
        }

        foreach ($jornadas as $jornada) {
            $this->info("Congelando jornada {$jornada->id} ({$jornada->nombre})...");
            $this->service->congelarJornada($jornada);
        }

        $this->info("Alineaciones congeladas correctamente.");
        return Command::SUCCESS;
    }
}
