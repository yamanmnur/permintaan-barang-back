<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang\Barang;
use App\Models\Permintaan\DetailPermintaan;
use App\Models\Permintaan\Permintaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class PermintaanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $data = Permintaan::select(
        'dat_permintaan.id',
        'dat_permintaan.kode',
        'dat_permintaan.id_user',
        'dat_permintaan.tanggal_permintaan',
        'users.name as nama_user'
        )->join('users','users.id','dat_permintaan.id_user')
        ->orderBy('dat_permintaan.created_at','desc')->get();
 
        return [
            'data' => $data
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data_exist = Permintaan::selectRaw('max(right(kode, 5)) as kode')->first();

        if (! $data_exist->kode) {
            $kode = 'KD-00001';
        } else {
            $pertama = $data_exist->kode;
            $int = (int) $pertama;
            $jumlah = $int  + 100001;
            $hasil = substr($jumlah,1);
            $kode = 'KD-'.$hasil;
        }

        $permintaan = new Permintaan();

        $permintaan->id = Uuid::uuid4()->toString();
        $permintaan->kode = $kode;
        $permintaan->id_user = $request->id_user;
        $permintaan->tanggal_permintaan = $request->tanggal_permintaan;
        $permintaan->created_at = date('Y-m-d H:i:s');
        $permintaan->updated_at = date('Y-m-d H:i:s');
        $permintaan->created_by = Auth::user()->id;
        $permintaan->updated_by = Auth::user()->id;

        $permintaan->save();

        $dataPermintaan = [];

        foreach ($request->dat_detail_permintaan as $value) {
            array_push($dataPermintaan,[
                'id' => Uuid::uuid4()->toString(),
                'id_permintaan' => $permintaan->id,
                'id_barang' => $value['id_barang'],
                'kuantiti' => $value['kuantiti'],
                'status' => $value['status'],
                'keterangan' => $value['keterangan'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id
            ]);
        }

        DetailPermintaan::insert($dataPermintaan);

        return [
            'status' => true
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Permintaan::with('detailPermintaan')->where('id',$id)->first();

        if(!$data) {
            return [
                'status' => false
            ];
        }

        return [
            'data' => $data
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $permintaan = Permintaan::where('id',$id)->first();

        $permintaan->id_user = $request->id_user;
        $permintaan->tanggal_permintaan = $request->tanggal_permintaan;
        $permintaan->created_at = date('Y-m-d H:i:s');
        $permintaan->updated_at = date('Y-m-d H:i:s');
        $permintaan->created_by = Auth::user()->id;
        $permintaan->updated_by = Auth::user()->id;

        $permintaan->save();

        $dataPermintaan = [];

        foreach ($request->dat_detail_permintaan as $value) {
            array_push($dataPermintaan,[
                'id' => Uuid::uuid4()->toString(),
                'id_permintaan' => $permintaan->id,
                'id_barang' => $value['id_barang'],
                'kuantiti' => $value['kuantiti'],
                'status' => $value['status'],
                'keterangan' => $value['keterangan'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id
            ]);
        }

        DetailPermintaan::insert($dataPermintaan);

        return [
            'status' => true
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
