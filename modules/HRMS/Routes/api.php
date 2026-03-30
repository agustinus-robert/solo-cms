<?php

use Illuminate\Support\Facades\Route;

// Search active employees
Route::get('/employees/search', 'EmployeeController@search')->name('employees.search');
Route::get('/employees/salary', 'EmployeeController@salary')->name('employees.salary');
Route::get('/employees/all', 'EmployeeController@all')->name('employees.all');
Route::get('/employees/get', 'EmployeeController@get')->name('employees.get');
Route::get('/positions/all', 'PositionController@all')->name('positions.all');
Route::get('/attendance-report/', 'AttendaceReportController@index')->name('attendance-report');

Route::get('employees', fn (\Illuminate\Http\Request $request) => response()->json(\Modules\HRMS\Models\Employee::with('user')->search($request->get('search'))->whenTrashed($request->get('trash'))->paginate($request->get('limit', 10))));
