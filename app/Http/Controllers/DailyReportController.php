<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Expanse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DailyReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function getTotalIncomeExpanse()
    {
        try {
            $totals = [
                'total_income' => Income::where('user_id', Auth::user()->id)->sum('nominal'),
                'total_expanse' => Expanse::where('user_id', Auth::user()->id)->sum('nominal'),
            ];

            return response()->json($totals, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan dalam mengambil total pendapatan dan pengeluaran. Silakan coba lagi nanti.'], 500);
        }
    }

    public function getTransactionsByDate(Request $request)
    {
        try {
            // Mendapatkan tanggal dari permintaan atau menggunakan hari ini sebagai default
            $date = $request->input('date_time', Carbon::today()->toDateString());

            // Menggunakan Carbon untuk memastikan format tanggal yang benar
            $date = Carbon::createFromFormat('Y-m-d', $date)->toDateString();

            // Mengambil pemasukan berdasarkan tanggal
            $incomes = Income::where('user_id', Auth::user()->id)
                ->whereDate('date_time', $date)
                ->get();

            // Mengambil pengeluaran berdasarkan tanggal
            $expenses = Expanse::where('user_id', Auth::user()->id)
                ->whereDate('date_time', $date)
                ->get();

            return response()->json([
                'date' => $date,
                'incomes' => $incomes,
                'expenses' => $expenses,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan dalam mengambil transaksi. Silakan coba lagi nanti.'], 500);
        }
    }
}
