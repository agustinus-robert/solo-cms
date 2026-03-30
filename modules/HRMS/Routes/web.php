<?php

use Illuminate\Support\Facades\Route;
use Modules\HRMS\Http\Middleware\AccessMiddleware;

Route::middleware(['auth', AccessMiddleware::class])->group(function () {

    // Redirect to dashboard
    Route::get('/', fn() => redirect()->route('hrms::dashboard'))->name('index');

    // Dashboard
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    // Employment
    Route::prefix('employment')->namespace('Employment')->name('employment.')->group(function () {
        // Employees
        Route::get('/employees/template', 'TemplateController@download')->name('employees.template');
        Route::post('/employees/upload', 'TemplateController@upload')->name('employees.upload');
        Route::put('/employees/{employee}/restore', 'EmployeeController@restore')->name('employees.restore');
        Route::resource('employees', 'EmployeeController')->except('edit');
        // Contracts
        Route::put('/contracts/{contract}/restore', 'ContractController@restore')->name('contracts.restore');
        Route::put('/contracts/{contract}/workdays', 'ContractController@workdays')->name('contracts.workdays');
        Route::get('contracts/{contract}/positions/create', 'PositionController@create')->name('contracts.positions.create');
        Route::post('contracts/{contract}/positions', 'PositionController@store')->name('contracts.positions.store');
        Route::resource('contracts', 'ContractController');
        // Contract position
        Route::get('contract-positions/{position}/edit', 'PositionController@edit')->name('contract-positions.edit');
        Route::put('contract-positions/{position}', 'PositionController@update')->name('contract-positions.update');
        Route::delete('contract-positions/{position}', 'PositionController@destroy')->name('contract-positions.delete');
        // Addendums
        Route::get('/contracts/{contract}/addendum/create', 'ContractAddendumController@create')->name('contracts.addendum.create');
        Route::post('/contracts/{contract}/addendum/store', 'ContractAddendumController@store')->name('contracts.addendum.store');
        Route::get('/contracts/{contract}/addendum/{addendum}/show', 'ContractAddendumController@show')->name('contracts.addendum.show');
        Route::put('/contracts/{contract}/addendum/{addendum}/update', 'ContractAddendumController@update')->name('contracts.addendum.update');
        Route::delete('/contracts/{contract}/addendum/{addendum}/destroy', 'ContractAddendumController@destroy')->name('contracts.addendum.destroy');

        Route::put('/users/{employee}/tax-action', 'TaxController@update')->name('tax-action');
        Route::resource('/user-document', 'DocumentController')->only('store', 'destroy', 'show');
    });

    // Service
    Route::prefix('service')->namespace('Service')->name('service.')->group(function () {
        // Attendance
        Route::prefix('attendance')->namespace('Attendance')->name('attendance.')->group(function () {
            // Schedules
            Route::get('schedules/collective', 'CollectiveController@create')->name('schedules.collective.create');
            Route::post('schedules/collective', 'CollectiveController@store')->name('schedules.collective.store');
            Route::post('schedules/do-teacher-presence', 'ScheduleController@teacher_presence')->name('schedules.do-teacher-presence');
            Route::post('schedules/do-presence', 'ScheduleController@presence')->name('schedules.do-presence');
            Route::post('schedules/generate', 'ScheduleController@generate')->name('schedules.generate');
            Route::resource('schedules', 'ScheduleController')->except('edit');
            // Manage
            Route::resource('manage', 'ManageController')->only('index');
            // Scanlogs
            Route::resource('scanlogs', 'ScanlogController')->only('index', 'store');
        });
        // Vacation
        Route::prefix('vacation')->namespace('Vacation')->name('vacation.')->group(function () {
            // Quota
            Route::post('quotas/batch-create', 'QuotaController@batch_create')->name('quotas.batch-create');
            Route::resource('quotas', 'QuotaController')->except('show', 'edit', 'update');
            // Print
            Route::get('manage/{vacation}/print', 'PrintController@index')->name('manage.print');
            // Approvable
            Route::put('manage/{vacation}/approvable/toggle', 'ApprovableController@update')->name('manage.approvable.toggle');
            Route::put('manage/{vacation}/approvable/{approvable}', 'ApprovableController@update')->name('manage.approvable.update');
            // Manage
            Route::put('manage/{vacation}/change', 'ManageController@change')->name('manage.change');
            Route::put('manage/{vacation}/restore', 'ManageController@restore')->name('manage.restore');
            Route::resource('manage', 'ManageController')->parameters(['manage' => 'vacation'])->only('index', 'show', 'update', 'destroy');
        });
        // Leave
        Route::prefix('leave')->namespace('Leave')->name('leave.')->group(function () {
            // Print
            Route::get('manage/{leave}/print', 'PrintController@index')->name('manage.print');
            // Approvable
            Route::put('manage/{leave}/approvable/{approvable}', 'ApprovableController@update')->name('manage.approvable.update');
            // Manage
            Route::put('manage/{leave}/change', 'ManageController@change')->name('manage.change');
            Route::put('manage/{leave}/restore', 'ManageController@restore')->name('manage.restore');
            Route::resource('manage', 'ManageController')->parameters(['manage' => 'leave'])->only('index', 'show', 'update', 'destroy');
        });
        // Overtime
        Route::prefix('overtime')->namespace('Overtime')->name('overtime.')->group(function () {
            // Approvable
            Route::put('manage/{overtime}/approvable/{approvable}', 'ApprovableController@update')->name('manage.approvable.update');
            // Manage
            Route::put('manage/{overtime}/restore', 'ManageController@restore')->name('manage.restore');
            Route::resource('manage', 'ManageController')->parameters(['manage' => 'overtime'])->only('index', 'show', 'update', 'destroy');
        });
    });

    // // Benefit
    // Route::prefix('benefit')->namespace('Benefit')->name('benefit.')->group(function () {
    //     // Insurance
    //     Route::prefix('insurances')->namespace('Insurance')->name('insurances.')->group(function () {
    //         // Registration
    //         Route::post('registrations/max-salary', 'RegistrationController@savemaxsalary')->name('registrations.max-salary');
    //         Route::delete('registrations/{employee}/reset', 'RegistrationController@reset')->name('registrations.reset');
    //         Route::delete('registrations/{insurance}/destroy', 'RegistrationController@destroy')->name('registrations.destroy');
    //         Route::resource('registrations', 'RegistrationController')->parameters(['registrations' => 'employee'])->only('index', 'create', 'store');
            
    //     });
    // });

    // Benefit
    Route::prefix('benefit')->namespace('Benefit')->name('benefit.')->group(function () {
        // Insurance
        Route::prefix('insurances')->namespace('Insurance')->name('insurances.')->group(function () {
            // Registration
            Route::delete('registrations/{employee}/reset-health-insurance', 'ResetRegistrationController@resetHealthInsurance')->name('registrations.reset-health-insurance');
            Route::delete('registrations/{employee}/reset-employee-insurance', 'ResetRegistrationController@resetEmployeeInsurance')->name('registrations.reset-employee-insurance');
            Route::delete('registrations/batch-reset-health-insurance', 'ResetRegistrationController@batchResetHealthInsurance')->name('registrations.batch-reset-health-insurance');
            Route::delete('registrations/batch-reset-employee-insurance', 'ResetRegistrationController@batchResetEmployeeInsurance')->name('registrations.batch-reset-employee-insurance');
            Route::delete('registrations/{employee}/reset', 'RegistrationController@reset')->name('registrations.reset');
            Route::delete('registrations/{insurance}/destroy', 'RegistrationController@destroy')->name('registrations.destroy');
            Route::resource('registrations', 'RegistrationController')->parameters(['registrations' => 'employee'])->only('index', 'create', 'store');
 
            Route::post('templates/default-salary', 'DefaultSalaryController@store')->name('templates.default-salary');
            Route::resource('templates', 'TemplateController')->parameters(['templates' => 'template']);
        });
    });

    Route::prefix('summary')->namespace('Summary')->name('summary.')->group(function () {
        // Attendance
        Route::post('attendances/{employee}', 'AttendanceController@store')->name('attendances.store');
        Route::resource('attendances', 'AttendanceController')->only('index', 'create', 'show', 'update');
        // Feastdays
        Route::get('feastdays', 'FeastDayController@index')->name('feastdays.index');
        Route::get('feastdays/{employee}', 'FeastDayController@create')->name('feastdays.create');
        Route::post('feastdays/{employee}', 'FeastDayController@store')->name('feastdays.store');
        // Feastdays
        Route::get('postyears', 'PostYearController@index')->name('postyears.index');
        Route::get('postyears/{employee}', 'PostYearController@create')->name('postyears.create');
        Route::post('postyears/{employee}', 'PostYearController@store')->name('postyears.store');

        Route::get('ticket', 'TicketController@index')->name('ticket.index');
        Route::get('ticket/show', 'TicketController@show')->name('ticket.show');
    });

    Route::prefix('payroll')->namespace('Payroll')->name('payroll.')->group(function () {
        // Components
        // Route::get('templates/export', 'ExportTemplateController@index')->name('templates.export');
        // Route::resource('templates', 'TemplateController');
        // Route::resource('calculations', 'CalculationController')->parameters(['calculations' => 'salary']);
        Route::resource('approvals', 'ApprovalController')->only('index', 'show', 'edit', 'update')->parameters(['approvals' => 'salary']);
    });

    // Report
    Route::prefix('report')->namespace('Report')->name('report.')->group(function () {
        // Employee
        Route::get('employees', 'EmployeeController@index')->name('employees.index');
        Route::get('employees/summary', 'EmployeeController@summary')->name('employees.summary');
        Route::get('employees/worktime', 'EmployeeController@worktime')->name('employees.worktime');
        // Attendance
        Route::get('attendances', 'AttendanceController@index')->name('attendances.index');
        Route::get('attendances/summary', 'AttendanceController@summary')->name('attendances.summary');
        // Vacation
        Route::get('vacations', 'VacationController@index')->name('vacations.index');
        Route::get('vacations/summary', 'VacationController@summary')->name('vacations.summary');
        // Leave
        Route::get('leaves', 'LeaveController@index')->name('leaves.index');
        Route::get('leaves/summary', 'LeaveController@summary')->name('leaves.summary');
        // Salary
        Route::get('salaries', 'SalaryController@index')->name('salaries.index');
        Route::get('salaries/summary', 'SalaryController@summary')->name('salaries.summary');
    });

    Route::prefix('system')->namespace('System')->name('system.')->group(function () {
        // Users
        Route::put('/users/{user}/restore', 'UserController@restore')->name('users.restore');
        Route::put('/users/{user}/repass', 'UserController@repass')->name('users.repass');
        Route::put('/users/{user}/profile', 'User\ProfileController@update')->name('users.update.profile');
        Route::put('/users/{user}/username', 'User\UsernameController@update')->name('users.update.username');
        Route::put('/users/{user}/email', 'User\EmailController@update')->name('users.update.email');
        Route::put('/users/{user}/role', 'User\RoleController@update')->name('users.update.role');
        Route::put('/users/{user}/phone', 'User\PhoneController@update')->name('users.update.phone');
        Route::post('/users/{user}/login', 'UserController@login')->name('users.login');
        Route::put('/users/{user}/tax-action', 'User\TaxController@update')->name('users.tax-action');

        Route::resource('/user-document', 'User\DocumentController')->only('store', 'destroy', 'show');
        Route::resource('users', 'UserController')->except(['create', 'edit', 'update']);
        // User logs
        Route::resource('user-logs', 'UserLogController')->parameters(['user-logs' => 'log'])->only('index', 'destroy');
        // Roles
        // Route::get('settings', 'SettingController@index')->name('settings.index');
        // Route::put('settings/{setting}', 'SettingController@update')->name('settings.update');
    });
});
