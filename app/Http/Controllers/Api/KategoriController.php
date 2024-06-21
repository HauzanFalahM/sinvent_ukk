<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data kategori dari database
        $kategori = Kategori::all();
        $data = array("data"=>$kategori);

        // Mengembalikan response JSON dengan data kategori
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data dari request jika diperlukan
        $request->validate([
            'deskripsi'   => 'required',
            'kategori'    => 'required',
        ]);
        
        // Menyimpan data kategori baru ke database
        $kategoribaru = Kategori::create([
            'deskripsi'  => $request->deskripsi,
            'kategori'   => $request->kategori,
        ]);

        $data = array("data"=>$kategoribaru);

        // Mengembalikan response JSON dengan data kategori yang baru dibuat
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        // Mengambil data kategori berdasarkan ID
        $kategori = Kategori::find($id);
        
        if(!$kategori){
            // Mengembalikan response JSON dengan pesan error jika kategori tidak ditemukan
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }else{
            $data=array("data"=>$kategori);
            // Mengembalikan response JSON dengan data kategori
            return response()->json($data);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Mengambil data kategori berdasarkan ID
        $kategori = Kategori::find($id);

        // Validasi data dari request jika diperlukan
        $request->validate([
            'deskripsi'   => 'required',
            'kategori'    => 'required',
        ]);
        
        if (!$kategori) {
            // Mengembalikan response JSON dengan pesan error jika kategori tidak ditemukan
            return response()->json(['status' => 'Kategori tidak ditemukan'], 404);
        }else{
            // Memperbarui data kategori dengan input baru
            $kategori->update([
                'deskripsi' => $request->deskripsi,
                'kategori' => $request->kategori,
            ]);

            // Mengembalikan response JSON dengan pesan sukses jika kategori berhasil diubah
            return response()->json(['status' => 'Kategori berhasil diubah'], 200);          
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Mengambil data kategori berdasarkan ID
        $kategori = Kategori::find($id);

        if (!$kategori) {
            // Mengembalikan response JSON dengan pesan error jika kategori tidak ditemukan
            return response()->json(['status' => 'Kategori tidak ditemukan'], 404);
        }
        
        try {
            // Menghapus data kategori dari database
            $kategori->delete();
            // Mengembalikan response JSON dengan pesan sukses jika kategori berhasil dihapus
            return response()->json(['status' => 'Kategori berhasil dihapus'], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Menangkap pengecualian spesifik dari database (termasuk constraints foreign key)
            // Mengembalikan response JSON dengan pesan error jika terjadi kesalahan
            return response()->json(['status' => 'Kategori tidak dapat dihapus'], 500);
        }
    }
}
