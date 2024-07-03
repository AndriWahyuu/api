<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Income; // Gunakan model Income
use App\Models\Expanse; // Gunakan model Expense
use Carbon\Carbon;

class DetailReportController extends Controller
{
    public function getMonthlyReportDetail($year, $month)
    {
        try {
            // Ambil total pendapatan untuk bulan tertentu
            $totalIncome = Income::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('amount');

            // Ambil total pengeluaran untuk bulan tertentu
            $totalExpenses = Expanse::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('amount');

            // Hitung sisa saldo
            $remainingBalance = $totalIncome - $totalExpenses;

            // Ambil daftar transaksi untuk bulan tertentu
            $incomeTransactions = Income::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get(['date', 'amount', 'description']);

            $expenseTransactions = Expanse::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get(['date', 'amount', 'description']);

            // Gabungkan pendapatan dan pengeluaran menjadi satu daftar transaksi
            $transactions = $incomeTransactions->merge($expenseTransactions)->sortBy('date');

            // Format nama bulan
            $monthName = Carbon::create()->month($month)->format('F');

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
