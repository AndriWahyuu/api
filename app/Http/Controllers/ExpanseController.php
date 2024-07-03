<?php

namespace App\Http\Controllers;
use App\Models\Expanse;


use Illuminate\Http\Request;;
use Illuminate\Support\Facades\DB;
use App\Models\Income;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;


class ExpanseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        $expanse = Expanse::where('user_id', Auth::id())->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'success' => true,
            'data' => $expanse
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
                'amount' => 'required|numeric|gt:0',
                'date_time' => 'required|date_format:Y-m-d',
                'description' => 'required',
            ],
            [
                'name.required' => 'Masukkan Nama Pengeluaran!',
                'amount.required' => 'Masukkan Jumlah Pengeluaran!',
                'amount.numeric' => 'Penulisan angka Anda salah!',
                'amount.gt:0' => "Jumlah tidak boleh 0",
                'date_time.required' => 'Silahkan Pilih Tanggal!',
                'date_time.date_format' => 'Format Tanggal tidak valid! Format yang benar adalah YYYY-MM-DD',
                'description.required' => 'Masukkan Deskripsi!',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors()
            ], 401);
        }

        $expanse = Expanse::create([
            'user_id' => Auth::user()->id,
            'name' => $request->input('name'),
            'amount' => $request->input('amount'),
            'date_time' => $request->input('date_time'),
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'success' => true,
            'data' => $expanse,
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
        $expanse = Expanse::find($id);

        if (!$expanse) {
            return response()->json([
                'success' => false,
                'message' => 'Data not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $expanse
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expanse $expanse)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'amount' => 'required|numeric|gt:0',
            'date_time' => 'required|date_format:Y-m-d',
            'description' => 'required',
        ], [
            'name.required' => 'Masukkan Nama Pengeluaran!',
            'amount.required' => 'Masukkan Jumlah Pengeluaran!',
            'amount.numeric' => 'Penulisan angka Anda salah!',
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

        $expanse->update([
            'name' => $request->input('name'),
            'amount' => $request->input('amount'),
            'date_time' => $request->input('date_time'),
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate!',
            'data' => $expanse
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
        $expanse = Expanse::find($id);

        if ($expanse) {
            $expanse->delete();
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
