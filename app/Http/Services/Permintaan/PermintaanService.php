<?php

namespace App\Http\Services\Barang;

use App\Http\Repositories\Permintaan\PermintaanRepository;
use App\Models\Permintaan\DetailPermintaan;
use App\Repositories\PostRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

class PermintaanService
{
    /**
     * @var $permintaanRepository
     */
    protected $permintaanRepository;

    /**
     * PostService constructor.
     *
     * @param PermintaanRepository $permintaanRepository
     */
    public function __construct(PermintaanRepository $permintaanRepository)
    {
        $this->permintaanRepository = $permintaanRepository;
    }

    /**
     * Delete post by id.
     *
     * @param $id
     * @return String
     */
    public function deleteById($id)
    {
        DB::beginTransaction();

        try {
            $post = $this->permintaanRepository->delete($id);

        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());

            throw new InvalidArgumentException('Unable to delete post data');
        }

        DB::commit();

        return $post;

    }

    /**
     * Get all post.
     *
     * @return String
     */
    public function getAll()
    {
        return $this->permintaanRepository->getAll();
    }

    /**
     * Get post by id.
     *
     * @param $id
     * @return String
     */
    public function getById($id)
    {
        return $this->permintaanRepository->getById($id);
    }

    /**
     * Update post data
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function updatePost($data, $id)
    {
        DB::beginTransaction();
        try {

            $permintaan =  $this->permintaanRepository->getById($id);

            $permintaan->id_user = $data['id_user'];
            $permintaan->tanggal_permintaan = $data['tanggal_permintaan'];
            $permintaan->created_at = date('Y-m-d H:i:s');
            $permintaan->updated_at = date('Y-m-d H:i:s');
            $permintaan->created_by = Auth::user()->id;
            $permintaan->updated_by = Auth::user()->id;

            $permintaan->save();

            $dataPermintaan = [];

            foreach ($data['dat_detail_permintaan'] as $value) {
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

        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());

            throw new InvalidArgumentException('Unable to update barang data');
        }

        DB::commit();

        return $permintaan;

    }

    /**
     * Validate post data.
     * Store to DB if there are no errors.
     *
     * @param array $data
     * @return String
     */
    public function savePostData($data)
    {
        $validator = Validator::make($data, [
            'title' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        $result = $this->permintaanRepository->save($data);

        return $result;
    }

}
