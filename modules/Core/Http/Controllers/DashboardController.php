<?php

namespace Modules\Core\Http\Controllers;

use Modules\Core\Models;
use Modules\Account\Models\User;
use Modules\Account\Models\UserLog;
use Modules\HRMS\Models\Employee;
use Illuminate\Http\Request;
use Modules\Poz\Models\AuditLog;

class DashboardController extends Controller
{
    /**
     * Show the dashboard page.
     */
    public function index(Request $request)
    {
        $statistics = [
            'departments_count' => Models\CompanyDepartment::count(),
            'positions_count' => Models\CompanyPosition::count(),
            'employees_count' => Employee::count(),
            'users_count' => User::count(),
        ];

        $recent_activities = AuditLog::latest()->limit(5)->get();

        return view('core::dashboard', compact(
            'statistics',
            'recent_activities'
        ));
    }
}
