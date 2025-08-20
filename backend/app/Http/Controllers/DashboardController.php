<?php

declare(strict_types=1);

namespace Internships\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Inertia\Response;
use Internships\Models\Company;
use Internships\Models\User;
use Internships\Models\Submission;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return inertia("Dashboard/Index");
    }

    public function stats(): JsonResponse
    {
        $totalCompanies = Company::count();
        $totalUsers = User::count();
        $totalSubmissions = Submission::count();
        $pendingCompanies = Company::where('status', 'pending')->count();
        $verifiedCompanies = Company::where('status', 'verified')->count();
        
        // Companies created this month
        $companiesThisMonth = Company::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
            
        // Recent companies (last 7 days)
        $recentCompanies = Company::where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();
            
        return response()->json([
            'totalCompanies' => $totalCompanies,
            'totalUsers' => $totalUsers,
            'totalSubmissions' => $totalSubmissions,
            'pendingCompanies' => $pendingCompanies,
            'verifiedCompanies' => $verifiedCompanies,
            'companiesThisMonth' => $companiesThisMonth,
            'recentCompanies' => $recentCompanies,
        ]);
    }

    public function chartData(): JsonResponse
    {
        // Companies created over the last 6 months
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Company::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'companies' => $count,
            ];
        }

        // Company status distribution
        $statusData = [
            ['status' => 'Verified', 'count' => Company::where('status', 'verified')->count()],
            ['status' => 'Pending', 'count' => Company::where('status', 'pending')->count()],
            ['status' => 'Rejected', 'count' => Company::where('status', 'rejected')->count()],
        ];

        return response()->json([
            'monthlyGrowth' => $monthlyData,
            'statusDistribution' => $statusData,
        ]);
    }

    public function recentActivity(): JsonResponse
    {
        $recentCompanies = Company::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'user' => $company->user->first_name . ' ' . $company->user->last_name,
                    'status' => $company->status->value,
                    'created_at' => $company->created_at->diffForHumans(),
                ];
            });

        return response()->json($recentCompanies);
    }
}