<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Income;
use App\Models\Expanse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


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
            return response()->json(['error' => 'Terjadi kesalahan dalam mengambil total pendapatan dan pengeluaran. Silakan coba lagi nanti.'], 500); // Status Code 500 Internal Server Error jika terjadi kesalahan
        }
    }
    public function getTransactionsByDate(Request $request)
    {
        try {
            // Mendapatkan tanggal dari permintaan atau menggunakan hari ini sebagai default
            $date = $request->input('date', date('Y-m-d'));

            // Mengambil pemasukan berdasarkan tanggal
            $incomes = Income::where('user_id', Auth::user()->id)
                ->whereDate('created_at', $date)
                ->get();

            // Mengambil pengeluaran berdasarkan tanggal
            $expanses = Expanse::where('user_id', Auth::user()->id)
                ->whereDate('created_at', $date)
                ->get();

            return response()->json([
                'date' => $date,
                'incomes' => $incomes,
                'expenses' => $expanses,
            ], 200); // Status Code 200 OK jika sukses
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan dalam mengambil transaksi. Silakan coba lagi nanti.'], 500); // Status Code 500 Internal Server Error jika terjadi kesalahan
        }
    }

}
