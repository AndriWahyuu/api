<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Income;
use App\Models\Expanse; 
use Carbon\Carbon;

class MonthlyReportController extends Controller
{
    public function getMonthlyReports()
    {
        try {
            $incomeReports = Income::select(
                DB::raw('YEAR(date_time) as year'),
                DB::raw('MONTH(date_time) as month'),
                DB::raw('SUM(amount) as total_income')
            )
            ->groupBy(DB::raw('YEAR(date_time)'), DB::raw('MONTH(date_time)'))
            ->get();

            $expanseReports = Expanse::select(
                DB::raw('YEAR(date_time) as year'),
                DB::raw('MONTH(date_time) as month'),
                DB::raw('SUM(amount) as total_expenses')
            )
            ->groupBy(DB::raw('YEAR(date_time)'), DB::raw('MONTH(date_time)'))
            ->get();

            // Gabungkan data pendapatan dan pengeluaran ke dalam satu array untuk laporan bulanan
            $formattedReports = [];
            foreach ($incomeReports as $incomeReport) {
                $month = $incomeReport->month;
                $year = $incomeReport->year;

                // Cari data pengeluaran untuk bulan yang sama
                $expenseReport = $expanseReports->first(function ($item) use ($year, $month) {
                    return $item->year == $year && $item->month == $month;
                });

                $total_income = $incomeReport->total_income;
                $total_expenses = $expenseReport ? $expenseReport->total_expenses : 0;
                $remaining_balance = $total_income - $total_expenses;

                $formattedReports[] = [
                    'success' => true,
                    'user_id' => Auth::user()->id,
                    'year' => $year,
                    'month' => Carbon::create()->month($month)->format('F'), // Ubah angka bulan menjadi nama bulan
                    'total_income' => $total_income,
                    'total_expenses' => $total_expenses,
                    'remaining_balance' => $remaining_balance,
                ];
            }

            return response()->json(['data' => $formattedReports]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch monthly reports.', 'message' => $e->getMessage()], 500);
        }
    }

    
    
}
