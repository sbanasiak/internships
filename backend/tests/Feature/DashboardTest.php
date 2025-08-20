<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Internships\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_route_requires_authentication(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('Dashboard/Index'));
    }

    public function test_dashboard_stats_api_returns_correct_format(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->getJson('/api/dashboard/stats');
        
        $response->assertOk();
        $response->assertJsonStructure([
            'totalCompanies',
            'totalUsers',
            'totalSubmissions',
            'pendingCompanies',
            'verifiedCompanies',
            'companiesThisMonth',
            'recentCompanies'
        ]);
    }

    public function test_dashboard_chart_data_api_returns_correct_format(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->getJson('/api/dashboard/chart-data');
        
        $response->assertOk();
        $response->assertJsonStructure([
            'monthlyGrowth' => [
                '*' => ['month', 'companies']
            ],
            'statusDistribution' => [
                '*' => ['status', 'count']
            ]
        ]);
    }

    public function test_dashboard_recent_activity_api_returns_array(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->getJson('/api/dashboard/recent-activity');
        
        $response->assertOk();
        $response->assertJson([]);
    }
}