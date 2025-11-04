<?php

declare(strict_types = 1);

namespace App\Brain\Chat\Tasks;

use App\Models\Workspace;
use Brain\Task;

/**
 * Task CreateWorkspaceTask
 *
 * @property-read int $owner_id
 * @property-read string $icon
 * @property-read string $name
 * @property-read string $description
 *
 * @property Workspace $workspace
 */
class CreateWorkspaceTask extends Task
{
    public function rules(): array
    {
        return [
            'owner_id'    => ['required', 'integer', 'exists:users,id'],
            'icon'        => ['nullable', 'string', 'max:1'], // 
            'name'        => ['required', 'min:3', 'max:15', 'unique:workspaces,name', 'alpha_dash'],
            'description' => ['nullable', 'max:255'],
        ];
    }

    public function handle(): self
    {
        $this->workspace = Workspace::query()
            ->create([
                'owner_id'    => $this->owner_id,
                'name'        => $this->name,
                'description' => $this->description,
                'icon'        => $this->icon,
            ]);

        return $this;
    }
}
