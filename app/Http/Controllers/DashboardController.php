<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Shared;
use Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function info(Request $request)
    {
        $numberOfSales = Sale::count();
        $dateLastRun = date('Y-m', strtotime('-1 months'));
        $numberOfSalesLastMonth = Sale::where('created_at', 'like', '%' . $dateLastRun . '%')->count();
        $amountOfSalesLastMonth = Sale::where('created_at', 'like', '%' . $dateLastRun . '%')->sum('total_net_amount');
        $totalAmount = Sale::sum('total_net_amount');

        $dateLastRun = date('Y-m');
        $numberOfSalesMonth = Sale::where('created_at', 'like', '%' . $dateLastRun . '%')->count();
        $amountOfSalestMonth = Sale::where('created_at', 'like', '%' . $dateLastRun . '%')->sum('total_net_amount');
        $amountOfSalestYearly = Sale::where('created_at', 'like', '%' . date('Y') . '%')->sum('total_net_amount');
        $numberOfSalestYearly = Sale::where('created_at', 'like', '%' . date('Y') . '%')->count();

        $year = date('Y');

        $months = [

            'January' => $year . '-01',
            'February' => $year . '-02'
            , 'March' => $year . '-03'
            , 'April' => $year . '-04'
            , 'May' => $year . '-05'
            , 'Jun' => $year . '-06',
            'July' => ($year) . '-07'
            , 'August' => ($year) . '-08'
            , 'September' => ($year) . '-09'
            , 'October' => ($year) . '-10'
            , 'November' => ($year) . '-11'
            , 'December' => ($year) . '-12',

        ];
        $dataset = [];
        $datasetAmount = [];
        foreach ($months as $key => $value) {

            $dataset[] = Sale::where('created_at', 'like', '%' . $value . '%')->count();
            $datasetAmount[] = Sale::where('created_at', 'like', '%' . $value . '%')->sum('total_net_amount');
        }

        $chartData = [
            'labels' => [
                'January'
                , 'February'
                , 'March'
                , 'April'
                , 'May'
                , 'Jun',
                'July'
                , 'August'
                , 'September'
                , 'October'
                , 'November'
                , 'December',

            ],
            'datasets' => [
                [
                    'label' => 'January ' . ($year) . ' to December ' . $year,
                    'backgroundColor' => '#f87979',
                    'data' => $dataset,
                ],
            ],
        ];

        $chartDataAmount = [
            'labels' => [
                'January'
                , 'February'
                , 'March'
                , 'April'
                , 'May'
                , 'Jun',
                'July'
                , 'August'
                , 'September'
                , 'October'
                , 'November'
                , 'December',

            ],
            'datasets' => [
                [
                    'label' => 'January ' . ($year) . ' to December ' . $year,
                    'backgroundColor' => '#f87979',
                    'data' => $datasetAmount,
                ],
            ],
        ];

        $data = [
            'salesCount' => $numberOfSales,
            'lastMonthSales' => $numberOfSalesLastMonth,
            'currentMonthSales' => $numberOfSalesMonth,
            'salesAmount' => $totalAmount,
            'currentMonthSalesAmount' => $amountOfSalestMonth,
            'lastMonthSalesAmount' => $amountOfSalesLastMonth,
            'yearlySales' => $numberOfSalestYearly,
            'yearlySalesAmount' => $amountOfSalestYearly,
            'yearlyDataSet' => $chartData,
            'yearlyDataSetAmount' => $chartDataAmount,

        ];

        return $this->successResponse('Dashboard info', $data);
    }

    public function infoShare(Request $request)
    {
        $user = Auth::user();
        $numberOfShares = Shared::where('user_id', $user->id)->count();
        $dateLastRun = date('Y-m', strtotime('-1 months'));
        $numberOfSharesLastMonth = Shared::where('user_id', $user->id)->where('created_at', 'like', '%' . $dateLastRun . '%')->count();
        $amountOfSharesLastMonth = Shared::where('user_id', $user->id)->where('created_at', 'like', '%' . $dateLastRun . '%')->sum('amount');
        $totalAmount = Shared::where('user_id', $user->id)->sum('amount');

        $dateLastRun = date('Y-m');
        $numberOfSharesMonth = Shared::where('user_id', $user->id)->where('created_at', 'like', '%' . $dateLastRun . '%')->count();
        $amountOfSharesMonth = Shared::where('user_id', $user->id)->where('created_at', 'like', '%' . $dateLastRun . '%')->sum('amount');
        $amountOfSharesYearly = Shared::where('user_id', $user->id)->where('created_at', 'like', '%' . date('Y') . '%')->sum('amount');
        $numberOfSharesYearly = Shared::where('user_id', $user->id)->where('created_at', 'like', '%' . date('Y') . '%')->count();

        $year = date('Y');

        $months = [

            'January' => $year . '-01',
            'February' => $year . '-02'
            , 'March' => $year . '-03'
            , 'April' => $year . '-04'
            , 'May' => $year . '-05'
            , 'Jun' => $year . '-06',
            'July' => ($year) . '-07'
            , 'August' => ($year) . '-08'
            , 'September' => ($year) . '-09'
            , 'October' => ($year) . '-10'
            , 'November' => ($year) . '-11'
            , 'December' => ($year) . '-12',

        ];
        $dataset = [];
        $datasetAmount = [];
        foreach ($months as $key => $value) {

            $dataset[] = Shared::where('user_id', $user->id)->where('created_at', 'like', '%' . $value . '%')->count();
            $datasetAmount[] = Shared::where('user_id', $user->id)->where('created_at', 'like', '%' . $value . '%')->sum('amount');
        }

        $chartData = [
            'labels' => [
                'January'
                , 'February'
                , 'March'
                , 'April'
                , 'May'
                , 'Jun',
                'July'
                , 'August'
                , 'September'
                , 'October'
                , 'November'
                , 'December',

            ],
            'datasets' => [
                [
                    'label' => 'January ' . ($year) . ' to December ' . $year,
                    'backgroundColor' => '#f87979',
                    'data' => $dataset,
                ],
            ],
        ];

        $chartDataAmount = [
            'labels' => [
                'January'
                , 'February'
                , 'March'
                , 'April'
                , 'May'
                , 'Jun',
                'July'
                , 'August'
                , 'September'
                , 'October'
                , 'November'
                , 'December',

            ],
            'datasets' => [
                [
                    'label' => 'January ' . ($year) . ' to December ' . $year,
                    'backgroundColor' => '#f87979',
                    'data' => $datasetAmount,
                ],
            ],
        ];

        $data = [
            'salesCount' => $numberOfShares,
            'lastMonthSales' => $numberOfSharesLastMonth,
            'currentMonthSales' => $numberOfSharesMonth,
            'salesAmount' => $totalAmount,
            'currentMonthSalesAmount' => $amountOfSharesMonth,
            'lastMonthSalesAmount' => $amountOfSharesMonth,
            'yearlySales' => $numberOfSharesYearly,
            'yearlySalesAmount' => $amountOfSharesYearly,
            'yearlyDataSet' => $chartData,
            'yearlyDataSetAmount' => $chartDataAmount,
            'user' => $user,
            'investorDetail' => $user->investorDetail,
            'shares' => Shared::where('user_id', $user->id)->with('user', 'share')->get(),
            'unpaid' => Shared::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
        ];

        return $this->successResponse('Dashboard info', $data);
    }
}
