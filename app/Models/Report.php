<?php

namespace App\Models;

use CodeIgniter\Model;

class Report extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'reports';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'user_id', 'title', 'description', 'image', 'category', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function getReportsQuery()
    {
        $this->select('reports.id, reports.title, reports.created_at, reports.updated_at, reports.description, reports.category, reports.status, users.name, users.email');
        $this->join('users', 'users.id = reports.user_id');
        $this->orderBy('id DESC');
    }

    protected function getReportFeedback()
    {
        $builder = $this->builder('feedbacks');
        $builder->select('comment, feedbacks.created_at, feedbacks.updated_at, users.name');
        $builder->join('users', 'users.id = feedbacks.user_id');
        return $builder;
    }

    public function getReports()
    {
        $this->getReportsQuery();
        $query = $this->get();

        $results = $query->getResult();

        return $this->reports($results);
    }

    public function getOwnReports($id = null)
    {
        $this->getReportsQuery();
        $this->where('reports.user_id', $id);
        $query = $this->get();

        $results = $query->getResult();

        return $this->reports($results);
    }

    protected function reports($results)
    {
        $reports = [];

        foreach ($results as $report) {
            $reports[] = (object)[
                'id' => $report->id,
                'title' => $report->title,
                'created_at' => $report->created_at,
                'updated_at' => $report->updated_at,
                'description' => $report->description,
                'category' => $report->category,
                'status' => $report->status,
                'user' => (object)[
                    'name' => $report->name,
                    'email' => $report->email,
                ],
            ];
        }
        return $reports;
    }

    public function getReport($id)
    {
        $this->getReportsQuery();
        $this->where('reports.id', $id);
        $query = $this->get();

        $result = $query->getRow();

        return $this->report($result);
    }
    public function geFeedback($id)
    {
        $query = $this->getReportFeedback()->where('feedbacks.report_id', $id)->get();
        return $query->getRow();
    }
    protected function report($result)
    {
        if (!$result) return null;

        $report = (object)[
            'id' => $result->id,
            'title' => $result->title,
            'created_at' => $result->created_at,
            'updated_at' => $result->updated_at,
            'description' => $result->description,
            'category' => $result->category,
            'status' => $result->status,
            'user' => (object)[
                'name' => $result->name,
                'email' => $result->email,
            ],
            'feedback' => $this->geFeedback($result->id)
        ];
        return $report;
    }
}
