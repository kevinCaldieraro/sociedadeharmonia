<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunMaintenance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-maintenance {task}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa tarefas de manutenção com modo manutenção ativado.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $task = $this->argument('task');

        $this->info('Entrando em modo de manutenção...');
        $this->callSilent('down', [
            '--render' => 'Maintenance',
        ]);

        try {
            if ($task === 'start-month') {
                $this->info('Executando tarefas do início do mês...');
                $this->call('app:start-month-tasks');
            } elseif ($task === 'update-statuses') {
                $this->info('Atualizando status das assinaturas...');
                $this->call('app:update-subscription-statuses');
            } else {
                $this->error('Tarefa inválida. Use: start-month ou update-statuses');
            }

            $this->info('Concluído com sucesso!');
        } catch (\Throwable $e) {
            $this->error('Erro durante execução: ' . $e->getMessage());
        } finally {
            $this->info('Saindo do modo de manutenção...');
            $this->callSilent('up');
        }

        $this->info('Manutenção finalizada.');
    }
}
