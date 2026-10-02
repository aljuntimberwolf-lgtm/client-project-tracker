<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_projects(): void
    {
        Project::create([
            'client_name' => 'Test Client',
            'project_name' => 'Test Project',
            'description' => 'Test description',
            'status' => 'Planning',
            'priority' => 'Medium',
            'start_date' => '2026-10-01',
            'due_date' => '2026-10-30',
        ]);

        $response = $this->getJson('/api/projects');

        $response
            ->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_can_create_a_project(): void
    {
        $data = [
            'client_name' => 'Test Client',
            'project_name' => 'New Project',
            'description' => 'A test project',
            'status' => 'Planning',
            'priority' => 'High',
            'start_date' => '2026-10-01',
            'due_date' => '2026-10-30',
        ];

        $response = $this->postJson('/api/projects', $data);

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'client_name' => 'Test Client',
                'project_name' => 'New Project',
            ]);

        $this->assertDatabaseHas('projects', [
            'client_name' => 'Test Client',
            'project_name' => 'New Project',
        ]);
    }

    public function test_project_requires_required_fields(): void
    {
        $response = $this->postJson('/api/projects', []);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'client_name',
                'project_name',
                'status',
                'priority',
                'start_date',
                'due_date',
            ]);
    }

    public function test_due_date_cannot_be_before_start_date(): void
    {
        $data = [
            'client_name' => 'Test Client',
            'project_name' => 'Invalid Dates',
            'description' => 'Testing date validation',
            'status' => 'Planning',
            'priority' => 'Medium',
            'start_date' => '2026-10-30',
            'due_date' => '2026-10-20',
        ];

        $response = $this->postJson('/api/projects', $data);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'due_date',
            ]);
    }

    public function test_can_update_a_project(): void
    {
        $project = Project::create([
            'client_name' => 'Old Client',
            'project_name' => 'Old Project',
            'description' => 'Old description',
            'status' => 'Planning',
            'priority' => 'Low',
            'start_date' => '2026-10-01',
            'due_date' => '2026-10-30',
        ]);

        $data = [
            'client_name' => 'Updated Client',
            'project_name' => 'Updated Project',
            'description' => 'Updated description',
            'status' => 'In Progress',
            'priority' => 'High',
            'start_date' => '2026-10-01',
            'due_date' => '2026-11-01',
        ];

        $response = $this->putJson(
            "/api/projects/{$project->id}",
            $data
        );

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'client_name' => 'Updated Client',
                'project_name' => 'Updated Project',
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'client_name' => 'Updated Client',
            'project_name' => 'Updated Project',
        ]);
    }

    public function test_can_delete_a_project(): void
    {
        $project = Project::create([
            'client_name' => 'Delete Client',
            'project_name' => 'Delete Project',
            'description' => 'Project to delete',
            'status' => 'Planning',
            'priority' => 'Low',
            'start_date' => '2026-10-01',
            'due_date' => '2026-10-30',
        ]);

        $response = $this->deleteJson(
            "/api/projects/{$project->id}"
        );

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Project deleted successfully.',
            ]);

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }
}