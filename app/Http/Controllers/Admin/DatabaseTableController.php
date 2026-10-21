<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DatabaseTableController extends Controller
{
    private const HIDDEN_COLUMNS = [
        'abilities',
        'exception',
        'failed_job_ids',
        'ip_address',
        'options',
        'owner',
        'password',
        'payload',
        'remember_token',
        'token',
        'user_agent',
        'value',
    ];

    public function index(): View
    {
        $tables = collect(Schema::getTableListing(schemaQualified: false))->sort()->values();

        return view('admin.tables.index', compact('tables'));
    }

    public function show(string $table): View
    {
        abort_unless(in_array($table, Schema::getTableListing(schemaQualified: false), true), 404);

        $columns = array_values(array_diff(
            Schema::getColumnListing($table),
            self::HIDDEN_COLUMNS,
            $table === 'sessions' ? ['id'] : [],
        ));

        $records = DB::table($table)->select($columns)->simplePaginate(25);

        return view('admin.tables.show', compact('table', 'columns', 'records'));
    }
}
