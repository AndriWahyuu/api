<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Income;
use App\Models\Expanse;
use Carbon\Carbon;

class DetailReportController extends Controller
{
    public function getMonthlyReportDetail(Request $request)
    {
        try {
            // Ambil nilai dari parameter query
            $year = $request->get('year');
            $month = $request->get('month');

            // Pastikan parameter query tidak kosong
            if (!$year || !$month) {
                return response()->json(['error' => 'Year and month parameters are required.'], 400);
            }

            // Ambil ID pengguna yang sedang login
            $userId = Auth::id();

            // Ambil total pendapatan untuk bulan tertentu berdasarkan pengguna yang sedang login
            $totalIncome = Income::where('user_id', $userId)
                ->whereYear('date_time', $year)
                ->whereMonth('date_time', $month)
                ->sum('amount');

            // Ambil total pengeluaran untuk bulan tertentu berdasarkan pengguna yang sedang login
            $totalExpenses = Expanse::where('user_id', $userId)
                ->whereYear('date_time', $year)
                ->whereMonth('date_time', $month)
                ->sum('amount');

            // Hitung sisa saldo
            $remainingBalance = $totalIncome - $totalExpenses;

            // Ambil daftar transaksi untuk bulan tertentu berdasarkan pengguna yang sedang login
            $incomeTransactions = Income::where('user_id', $userId)
                ->whereYear('date_time', $year)
                ->whereMonth('date_time', $month)
                ->get();

            $expenseTransactions = Expanse::where('user_id', $userId)
                ->whereYear('date_time', $year)
                ->whereMonth('date_time', $month)
                ->get();

            // Gabungkan pendapatan dan pengeluaran menjadi satu daftar transaksi
            $transactions = $incomeTransactions->merge($expenseTransactions)->sortBy('date_time');

            // Format nama bulan
            $monthName = Carbon::createFromDate($year, $month, 1)->format('F');

            return response()->json([
                'year' => $year,
                'month' => $monthName,
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'remaining_balance' => $remainingBalance,
                'transactions' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch monthly report detail.', 'message' => $e->getMessage()], 500);
        }
    }
}
