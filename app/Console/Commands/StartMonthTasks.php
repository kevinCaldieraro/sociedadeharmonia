<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartMonthTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:start-month-tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando atualização dos membros isentos...');
        $this->call('app:update-exempt-members');

        $this->info('Gerando mensalidades...');
        $this->call('app:generate-monthly-subscriptions');

        $this->info('Concluído.');
    }
}
