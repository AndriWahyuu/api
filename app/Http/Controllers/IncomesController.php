<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Income;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class IncomesController extends Controller
{
    /**
     * Apply middleware to authenticate API requests.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $income = DB::table('incomes')
        //     // ->select('incomes.id', 'incomes.user_id', 'incomes.name', 'incomes.amount', 'incomes.date_time', 'incomes.description')
        //     // ->where('incomes.user_id', Auth::user()->id)
        //     // ->orderBy('incomes.created_at', 'DESC')
        //     // ->get();

        $income = Income::where('user_id', Auth::id())->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'success' => true,
            'data' => $income
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'amount' => 'required|gt:0',
                    'date_time' => 'required|date_format:Y-m-d',
                    'description' => 'required',
                ],
                [
                    'name.required' => 'Masukkan Nama Penghasilan!',
                    'amount.required' => 'Masukkan Jumlah Penghasilan!',
                    'amount.numeric' => 'Penulisan angka anda salah!',
                    'amount.gt:0' => "Jumlah tidak boleh 0",
                    'date_time.required' => 'Silahkan Pilih Tanggal!',
                    'date_time.date_format' => 'Format Tanggal tidak valid! Format yang benar adalah Y-m-d',
                    'description.required' => 'Masukkan Deskripsi!',
                ]
            );

            // Jika validasi gagal
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'data' => $validator->errors()
                ], 422); // 422 Unprocessable Entity
            }

            // Membuat entri baru
            $income = Income::create([
                'user_id' => Auth::user()->id,
                'name' => $request->input('name'),
                'amount' => $request->input('amount'),
                'date_time' => $request->input('date_time'),
                'description' => $request->input('description'),
            ]);

            // Memeriksa keberhasilan penciptaan
            if (!$income) {
                throw new Exception('Gagal menyimpan data penghasilan.');
            }

            // Memberikan respons sukses
            return response()->json([
                'success' => true,
                'data' => $income,
                'message' => 'Data Berhasil Disimpan!'
            ], 201); // 201 Created

        } catch (Exception $e) {
            // Tangani kesalahan
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data penghasilan.',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            // Pengecekan ID valid
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception('ID tidak valid.');
            }

            // Mencari data penghasilan berdasarkan ID
            $income = Income::find($id);

            // Jika data tidak ditemukan
            if (!$income) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ], 404);
            }

            // Memberikan respons sukses dengan data penghasilan
            return response()->json([
                'success' => true,
                'data' => $income
            ], 200);

        } catch (Exception $e) {
            // Tangani kesalahan
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve income data.',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Income $income)
    {
        try {
            // Pastikan $income adalah instance dari Income
            if (!$income instanceof Income) {
                throw new Exception('Data tidak ditemukan.');
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'amount' => 'required|numeric|gt:0',
                'date_time' => 'required|date_format:Y-m-d',
                'description' => 'required',
            ], [
                'name.required' => 'Masukkan Nama Penghasilan!',
                'amount.required' => 'Masukkan Jumlah Penghasilan!',
                'amount.numeric' => 'Penulisan angka anda salah!',
                'amount.gt:0' => "Jumlah tidak boleh 0",
                'date_time.required' => 'Silahkan Pilih Tanggal!',
                'date_time.date_format' => 'Format Tanggal tidak valid! Format yang benar adalah YYYY-MM-DD',
                'description.required' => 'Masukkan Deskripsi!',
            ]);

            // Jika validasi gagal
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()
                ], 400);
            }

            // Lakukan pembaruan data
            $income->update([
                'name' => $request->input('name'),
                'amount' => $request->input('amount'),
                'date_time' => $request->input('date_time'),
                'description' => $request->input('description'),
            ]);

            // Berikan respons sukses
            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Diupdate!',
                'data' => $income
            ], 200);

        } catch (Exception $e) {
            // Tangani kesalahan server
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data penghasilan.',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // memastikan ID valid
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception('ID tidak valid.');
            }

            // Cari data penghasilan berdasarkan ID
            $income = Income::find($id);

            // Jika data ditemukan, hapus
            if ($income) {
                $income->delete();
                return response()->json([
                    'status' => 'Data Berhasil Dihapus!'
                ]);
            } else {
                // Jika data tidak ditemukan, berikan respons dengan kode 404
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data not found'
                ], 404);
            }

        } catch (Exception $e) {
            // Tangani kesalahan
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete income data.',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }

}
