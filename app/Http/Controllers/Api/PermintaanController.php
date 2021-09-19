<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\Permintaan\PermintaanService;
use App\Models\Barang\Barang;
use App\Models\Permintaan\DetailPermintaan;
use App\Models\Permintaan\Permintaan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class PermintaanController extends Controller
{
    protected $permintaanService;

    public function __construct(PermintaanService $permintaanService) {
        $this->permintaanService = $permintaanService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = ['status' => 200];

        try {
            $result['data'] = $this->permintaanService->getAll();
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }

        return response()->json($result, $result['status']);
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
        return [
            'status' => true,
            'data' => $this->permintaanService->savePostData($request)
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
        $result = ['status' => 200];

        try {
            $result['data'] = $this->permintaanService->getById($id);
        } catch(Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }

        return response()->json($result, $result['status']);
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
    public function update(Request $request,$id) {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePost(Request $request)
    {
        $permintaan = Permintaan::where('id',$request->id)->first();

        $permintaan->id_user = $request->id_user;
        $permintaan->tanggal_permintaan = $request->tanggal_permintaan;
        $permintaan->created_at = date('Y-m-d H:i:s');
        $permintaan->updated_at = date('Y-m-d H:i:s');
        $permintaan->created_by = Auth::user()->id;
        $permintaan->updated_by = Auth::user()->id;

        $permintaan->save();

        foreach ($request->deleted_items as $key => $value) {
            # code...
            $barang = Barang::where('id',$value['id_barang'])->first();
            if($barang) {
                Barang::where('id',$value['id_barang'])->update([
                    'kuantiti' => $barang->kuantiti + $value['kuantiti']
                ]);
            }
            DetailPermintaan::where('id',$value['id'])->delete();
        }

        foreach ($request->items as $value) {
            if($value['id'] == '') {
                $barang = Barang::where('id',$value['id_barang'])->first();
                if($barang) {
                    if($barang->kuantiti < $value['kuantiti']) {
                        Barang::where('id',$value['id_barang'])->update([
                            'kuantiti' => $barang->kuantiti - $value['kuantiti']
                        ]);
                    }
                }
                DetailPermintaan::insert([
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
            } else {
                $detail = DetailPermintaan::where('id',$value['id'])->first();
                if($detail) {
                    $barang = Barang::where('id',$value['id_barang'])->first();
                    if($detail['kuantiti'] < $value['kuantiti']) {
                        if($barang) {
                            if($barang->kuantiti < $value['kuantiti']) {
                                Barang::where('id',$value['id_barang'])->update([
                                    'kuantiti' => $barang->kuantiti - $value['kuantiti']
                                ]);
                            }
                        }
                    } else if($detail['kuantiti'] > $value['kuantiti']) {
                        if($barang) {
                            if($barang->kuantiti < $value['kuantiti']) {
                                Barang::where('id',$value['id_barang'])->update([
                                    'kuantiti' => $barang->kuantiti + $value['kuantiti']
                                ]);
                            }
                        }
                    }
                }
                DetailPermintaan::where('id',$value['id'])->update([
                    'id_barang' => $value['id_barang'],
                    'kuantiti' => $value['kuantiti'],
                    'status' => $value['status'],
                    'keterangan' => $value['keterangan'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id
                ]);
            }

        }

        // insert($dataPermintaan);

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
        $permintaan = Permintaan::where('id',$id)->first();

        $permintaan->delete();

        $dataDetail = DetailPermintaan::where('id_permintaan',$id)->get();

        foreach ($dataDetail as $key => $value) {
            $barang = Barang::where('id',$value->id_barang)->first();

            if($barang) {
                Barang::where('id',$value->id_barang)->update([
                    'kuantiti' => $barang->kuantiti + $value->kuantiti
                ]);
            }
        }

        return [
            'status' => true
        ];
    }
}
