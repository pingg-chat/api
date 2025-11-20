<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use App\Brain\Chat\Processes\CreateUserProcess;
use App\Models\User;
use Illuminate\Console\Command;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\note;
use function Laravel\Prompts\search;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class DefaultCommand extends Command
{
    protected $signature = 'app:default';

    protected $description = 'Default command description';

    private ?User $user = null;

    public function handle()
    {
        $sshkey = $_ENV['WHISP_USER_PUBLIC_KEY'];

        clear();

        note('   Pingg Chat ');

        if (! $this->checkIfUserExists($sshkey)) {
            $this->signUp($sshkey);
        }

        $this->openApplication();
    }

    private function openApplication(): void
    {
        $width  = $_ENV['WHISP_TERM_WIDTH'] ?? getenv('COLUMNS') ?? 80;
        $height = $_ENV['WHISP_TERM_HEIGHT'] ?? getenv('LINES') ?? 24;

        putenv("TERM_WIDTH={$width}");
        putenv("TERM_HEIGHT={$height}");

        passthru('pingg ' . $this->user->id);
    }

    private function checkIfUserExists(string $sshkey): bool
    {
        $this->user = User::query()
            ->where('ssh_key', $sshkey)
            ->first();

        return $this->user !== null;
    }

    private function signUp(string $sshkey): void
    {
        note('Welcome to Pingg! Let\'s set up your user account.');

        $whispUsername = $_ENV['WHISP_USERNAME'];

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

        $icon = search(
            label: 'Select an icon for your profile',
            options: fn (string $value) => $this->icons($value),
            required: true,
        );

        $this->user = CreateUserProcess::dispatchSync([
            'icon'     => $icon,
            'name'     => $name,
            'username' => $username,
            'email'    => $email,
            'ssh_key'  => $sshkey,
        ])->user;

        spin(
            callback: fn () => sleep(5),
            message: 'You account is being set up...'
        );

        note('Setup complete! You can now start using Pingg Chat.');
    }

    private function icons(?string $filter = null): array
    {
        $icons = [
            '󰻀' => 'penguin: 󰻀',
            '' => 'robot: ',
            '' => 'alien: ',
        ];

        if ($filter) {
            return array_filter(
                $icons,
                fn ($label) => str_contains($label, $filter)
            );
        }

        return $icons;
    }
}
