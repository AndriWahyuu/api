<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

;
use Illuminate\Support\Facades\DB;
use App\Models\Income;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'amount' => 'required|numeric|gt:0',
            'date_time' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $inputDate = $request->input('date_time');
        $formattedDate = date('Y-m-d', strtotime(str_replace('/', '-', $inputDate)));

        $income = Income::create([
            'user_id' => Auth::user()->id,
            'name' => $request->input('name'),
            'amount' => $request->input('amount'),
            'date_time' => $formattedDate, // Simpan tanggal dengan format "yyyy-MM-dd"
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
            'date_time' => 'required|date_format:d/m/Y',
            'description' => 'required',
        ], [
            'name.required' => 'Masukkan Nama Pengeluaran!',
            'amount.required' => 'Masukkan Jumlah Pengeluaran!',
            'amount.numeric' => 'Penulisan angka Anda salah!',
            'amount.gt:0' => "Jumlah tidak boleh 0",
            'date_time.required' => 'Silahkan Pilih Tanggal!',
            'date_time.date_format' => 'Format Tanggal tidak valid! Format yang benar adalah DD/MM/YYYY',
            'description.required' => 'Masukkan Deskripsi!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        // Ubah format tanggal dari dd/MM/yyyy ke yyyy-MM-dd
        $inputDate = $request->input('date_time');
        $formattedDate = date('Y-m-d', strtotime(str_replace('/', '-', $inputDate)));

        $income->update([
            'name' => $request->input('name'),
            'amount' => $request->input('amount'),
            'date_time' => $formattedDate, // Simpan tanggal dengan format yyyy-MM-dd
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
