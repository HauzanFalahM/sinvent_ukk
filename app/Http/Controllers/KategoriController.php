<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;


class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use ValidatesRequests;

    public function index(Request $request)
    {
        $query = Kategori::select('id', 'deskripsi', 'kategori',
            DB::raw('(CASE
                WHEN kategori = "M" THEN "Modal"
                WHEN kategori = "A" THEN "Alat"
                WHEN kategori = "BHP" THEN "Bahan Habis Pakai"
                ELSE "Bahan Tidak Habis Pakai"
                END) AS ketKategori'));

        // Jika ada parameter pencarian
        if ($request->has('search') && !empty($request->input('search'))) {
            $query->where('deskripsi', 'like', '%' . $request->input('search') . '%');
        }

        $rsetkategori = $query->paginate(10);

        return view('v_kategori.index', compact('rsetkategori'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('v_kategori.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'deskripsi' => 'required',
            'kategori' => 'required|in:M,A,BHP,BTHP',
        ]);
    
        // Memulai transaksi database
        DB::beginTransaction();
    
        try {
            // Cek apakah deskripsi sudah ada
            $existingCategory = Kategori::where('deskripsi', $request->deskripsi)->first();
            if ($existingCategory) {
                // Jika deskripsi sudah ada, kembalikan ke halaman sebelumnya dengan pesan error
                return redirect()->back()->withInput()->withErrors(['deskripsi' => 'Kategori ' . $request->deskripsi . ' sudah ada']);
            }
    
            // Buat kategori baru
            Kategori::create([
                'deskripsi' => $request->deskripsi,
                'kategori' => $request->kategori,
            ]);
    
            // Commit transaksi jika tidak ada kesalahan
            DB::commit();
    
            // Redirect ke index dengan pesan sukses
            return redirect()->route('kategori.index')->with(['success' => 'Data berhasil disimpan!']);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();
            // Kembalikan ke halaman sebelumnya dengan pesan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data.']);
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rsetkategori= kategori::find($id);

        //return $Barang;A

        //return view
        return view('v_kategori.show', compact('rsetkategori'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $rsetkategori = Kategori::find($id);
        return view('v_kategori.edit', compact('rsetkategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'kategori'  => 'required',
            'deskripsi'    => 'required'
        ]);

        $rsetkategori = Kategori::find($id);


        //update post without image
        $rsetkategori->update([
            'kategori'   => $request->kategori,
            'deskripsi'     => $request->deskripsi
        ]);

        //redirect to index
        return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
     
        if (DB::table('barang')->where('kategori_id', $id)->exists()){
            return redirect()->route('kategori.index')->with(['Gagal' => 'Data Gagal Dihapus!']);
        } else {
            $rsetKategori = Kategori::find($id);
            $rsetKategori->delete();
            return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Dihapus!']);
        }
    }
}