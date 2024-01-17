<?php

namespace App\Controllers;

use App\Models\Feedback;
use App\Models\Report;
use CodeIgniter\RESTful\ResourceController;
use App\Models\User;

class ReportController extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */

    protected $reportModel;
    protected $feedbackModel;
    protected $userModel;

    public function __construct()
    {
        $this->reportModel = new Report();
        $this->userModel = new User();
        $this->feedbackModel = new Feedback();

        helper('auth_helper');
    }
    public function index()
    {
        try {
            $reports = auth_user()->is_admin
                ? $this->reportModel->getReports()
                : $this->reportModel->getOwnReports(auth_user()->id);

            return $this->respond([
                'status' => 200,
                'message' => 'Berhasil Mengambil Data Laporan',
                'reports' => $reports
            ], 200);
        } catch (\Throwable $th) {
            return $this->fail($th, 400);
        }
    }


    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $report = $this->reportModel->getReport($id);

        if (!$report) return $this->failNotFound('Laporan tidak ditemukan');

        if (!auth_user()->is_admin && $report->user->email != auth_user()->email) return $this->failForbidden('Akses dilarang');

        return $this->respond([
            'status' => 200,
            'report'   => $report,
            'message' => 'Berhasil mendapatkan detail laporan',
        ], 200);
    }


    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
            'image' => 'required',
        ];

        $user = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'password' => password_hash('apha123', PASSWORD_BCRYPT),
        ];

        if ($this->request->getVar('name') || $this->request->getVar('email')) {
            $rules += ['name' => 'required', 'email' => 'required'];

            if ($this->validate($rules)) {
                $this->userModel->save($user);
                $user = ['user_id' => $this->userModel->getInsertID()];
            }
        } elseif ($this->validate($rules)) {
            $user = ['user_id' => auth_user()->id];
        } else {
            return $this->fail(['status' => 400, 'message' => 'Gagal Membuat Laporan']);
        }

        $report = [
            'user_id' => $user['user_id'],
            'title' => $this->request->getVar('title'),
            'description' => $this->request->getVar('description'),
            'category' => $this->request->getVar('category'),
            'image' => $this->request->getVar('image'),
        ];

        $this->reportModel->save($report);

        return $this->respondCreated(['status' => 201, 'message' => 'Berhasil Membuat Laporan']);
    }


    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $report = $this->reportModel->getReport($id);

        if (!$report) return $this->failNotFound('Laporan tidak ditemukan');

        if (!auth_user()->is_admin && $report->user->email != auth_user()->email) return $this->failForbidden('Akses dilarang');

        $rules = [
            'category' => 'required',
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',
            'status' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->respond([
                'status' => 409,
                'errors' => $this->validator->getErrors(),
                'message' => 'Periksa kembali inputan',
            ], 409);
        }

        try {
            $data = [
                'category' => $this->request->getVar('category'),
                'title' => $this->request->getVar('title'),
                'description' => $this->request->getVar('description'),
                'image' => $this->request->getVar('image'),
                'status' => $this->request->getVar('status'),
            ];

            $this->reportModel->update($id, $data);

            return $this->respond([
                'status' => 200,
                'message' => 'Berhasil memperbarui laporan'
            ], 200);
        } catch (\Throwable $th) {
            return $this->fail($th);
        }
    }


    public function update_status($id = null)
    {
        $report = $this->reportModel->getReport($id);

        if (!$report) return $this->failNotFound('Laporan tidak ditemukan');

        if (!auth_user()->is_admin && $report->user->email != auth_user()->email) return $this->failForbidden('Akses dilarang');

        $rules = ['status' => 'required'];

        if ($this->validate($rules)) {
            try {
                $this->reportModel->where('id', $id)->set('status', $this->request->getVar('status'))->update();

                return $this->respond([
                    'status' => 200,
                    'message' => 'Berhasil memperbarui status',
                ], 200);
            } catch (\Throwable $th) {
                return $this->fail($th);
            }
        } else {

            return $this->respond([
                'status' => 409,
                'errors' => $this->validator->getErrors(),
                'message' => 'Periksa kembali inputan',
            ], 409);
        }
    }
    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $report = $this->reportModel->getReport($id);

        if (!$report) return $this->failNotFound('Laporan tidak ditemukan');

        if (!auth_user()->is_admin && $report->user->email != auth_user()->email) return $this->failForbidden('Akses dilarang');

        try {
            $this->reportModel->delete($id);

            return $this->respond([
                'status' => 200,
                'message' => 'Berhasil Menghapus Laporan',
            ], 200);
        } catch (\Throwable $th) {
            return $this->fail($th);
        }
    }
}
