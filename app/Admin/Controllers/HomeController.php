<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        if (Admin::user()->inRoles(['florist-manager', 'florist'])){
            return redirect('admin/florist');
        } elseif (Admin::user()->inRoles(['coordinator'])) {
            return redirect('admin/invoice-details');
        } elseif (Admin::user()->inRoles(['shipper'])) {
            return redirect('admin/shipper');
        }

        return $content
            ->title('Dashboard')
            ->description('Description...')
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });

    }
}
