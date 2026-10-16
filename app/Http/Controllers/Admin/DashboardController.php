<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // [label, value, icon, tone]. The numbers come from the database in
        // the dashboard stats step; until then the cards show a dash.
        return view('admin.dashboard', [
            'stats' => [
                ['Total products', null, 'box', 'brand'],
                ['Active products', null, 'grid', 'green'],
                ['Out of stock', null, 'alert', 'red'],
                ['Categories', null, 'tag', 'amber'],
            ],
        ]);
    }
}
