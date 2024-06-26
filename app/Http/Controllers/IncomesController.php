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
                'date_time.date_format' => 'Format Tanggal tidak valid! Format yang benar adalah HH:MM',
                'description.required' => 'Masukkan Deskripsi!',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors()
            ], 401);
        }

        // $amount = str_replace(",", ".", "",$request->input('amount')); // Menghapus koma jika ada
        // $amount = (float) $amount; // Konversi ke float

        $income = Income::create([
            'user_id' => Auth::user()->id,  // Pastikan untuk menyertakan user_id
            'name' => $request->input('name'),
            'amount' => $request->input('amount'),
            'date_time' => $request->input('date_time'),
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'success' => true,
            'data' => $income,
            'message' => 'Data Berhasil Disimpan!'
        ], 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $income = Income::find($id);

        if (!$income) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $income
        ], 200);
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        $income->update([
            'name' => $request->input('name'),
            'amount' => $request->input('amount'),
            'date_time' => $request->input('date_time'),
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate!',
            'data' => $income
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $income = Income::find($id);

        if ($income) {
            $income->delete();
            return response()->json([
                'status' => 'Data Berhasil Dihapus!'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found'
            ], 404);
        }
    }
}
