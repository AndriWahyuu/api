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
                'total_income' => Income::where('user_id', Auth::user()->id)->sum('amount'),
                'total_expanse' => Expanse::where('user_id', Auth::user()->id)->sum('amount'),
            ];

            return response()->json([
                'success' => true,
                'user_id' => Auth::user()->id,
                'data' => $totals,
                'message' => 'Total Berhasil Ditampilkan!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan dalam mengambil total pendapatan dan pengeluaran. Silakan coba lagi nanti.'], 500);
        }
    }

    public function getTransactionsByDate(Request $request)
    {
        try {
            // Mendapatkan tanggal dari permintaan atau menggunakan hari ini sebagai default
            $dateString = $request->input('date_time', Carbon::today()->toDateString());

            // Mengonversi format tanggal dari input Flutter (1/7/2024) ke format Y-m-d
            $date = Carbon::createFromFormat('j/n/Y', $dateString)->toDateString();

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
            return response()->json(['error' => 'Terjadi kesalahan dalam menampilkan transaksi. Silakan coba lagi nanti.'], 500);
        }
    }
}
