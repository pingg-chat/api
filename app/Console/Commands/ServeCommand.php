<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Whisp\Server;

class ServeCommand extends Command
{
    protected $signature = 'app:serve';

    protected $description = 'Start ssh server';

    public function handle()
    {
        $server = new Server(port: 2222);

        $server->run(base_path('servers/default.php'));
    }
}
