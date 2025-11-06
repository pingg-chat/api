<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use App\Brain\Chat\Processes\CreateUserProcess;
use Illuminate\Console\Command;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class DefaultCommand extends Command
{
    protected $signature = 'app:default';

    protected $description = 'Default command description';

    public function handle()
    {
        $this->info('Default command executed successfully.');

        $whispUsername = $_ENV['WHISP_USERNAME'];
        $sshkey        = $_ENV['WHISP_USER_PUBLIC_KEY'];

        // --- Registration Workflow ---
        $name = text(
            label: 'Enter your name',
            placeholder: '',
            required: true,
            validate: ['min:2', 'max:100']
        );

        $username = text(
            default: $whispUsername,
            label: 'Choose a username',
            required: true,
            validate: ['min:3', 'max:100', 'alpha_dash']
        );

        $email = text(
            label: 'Enter your email address',
            placeholder: '',
            required: true,
            validate: ['email', 'max:100']
        );

        $icon = select(
            label: 'Select an icon for your profile',
            options: $this->icons(),
            default: 'account',
            required: true,
        );

        dump(
            compact(
                'icon',
                'name',
                'username',
                'email',
                'sshkey',
            )
        );

        // CreateUserProcess::dispatch([
        //     'icon'     => $icon,
        //     'name'     => $name,
        //     'username' => $username,
        //     'email'    => $email,
        //     'sshkey'   => $sshkey,
        // ]);
    }

    private function icons(): array
    {
        // icons based on nerd fonts
        return [
            '󰻀' => 'penguim: 󰻀',
            '' => 'robot: ',
            '' => 'alien: ',
        ];
    }
}
