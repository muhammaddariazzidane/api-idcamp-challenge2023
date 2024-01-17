<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\Feedback;
use App\Models\Report;

class FeedbackController extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */

    protected $reportModel;
    protected $feedbackModel;

    public function __construct()
    {
        $this->feedbackModel = new Feedback();
        $this->reportModel = new Report();

        helper('auth_helper');
    }

    public function index()
    {
        //
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        //
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
        $report = $this->reportModel->getReport($this->request->getVar('report_id'));

        if (!$report) return $this->failNotFound('Laporan tidak ditemukan');

        if (!auth_user()->is_admin && $report->user->email != auth_user()->email) return $this->failForbidden('Akses dilarang');

        if (in_array($report->status, ['pending', 'ditinjau'])) return $this->failForbidden('Akses dilarang');

        try {
            $feedback = [
                'report_id' => $this->request->getVar('report_id'),
                'user_id' => auth_user()->id,
                'comment' => $this->request->getVar('comment'),
            ];

            $this->feedbackModel->save($feedback);

            return $this->respondCreated(['status' => 201, 'message' => 'Berhasil Memberikan Umpan Balik']);
        } catch (\Throwable $th) {
            return $this->fail($th);
        }
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
        $feedback = $this->feedbackModel->find($id);

        if (!$feedback) return $this->failNotFound('Umpan Balik tidak ditemukan');

        if (!auth_user()->is_admin && $feedback->user_id != auth_user()->id) return $this->failForbidden('Akses dilarang');

        $rules = ['comment' => 'required'];

        if ($this->validate($rules)) {
            try {
                $this->feedbackModel->where('id', $id)->set('comment', $this->request->getVar('comment'))->update();

                return $this->respond([
                    'status' => 200,
                    'message' => 'Berhasil memperbarui umpan balik',
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
        $feedback = $this->feedbackModel->find($id);

        if (!$feedback) return $this->failNotFound('Umpan Balik tidak ditemukan');

        if (!auth_user()->is_admin && $feedback->user_id != auth_user()->id) return $this->failForbidden('Akses dilarang');

        try {
            $this->feedbackModel->delete($id);

            return $this->respond([
                'status' => 200,
                'message' => 'Berhasil Menghapus Umpan Balik',
            ], 200);
        } catch (\Throwable $th) {
            return $this->fail($th);
        }
    }
}
