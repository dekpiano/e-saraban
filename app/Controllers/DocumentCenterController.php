<?php

namespace App\Controllers;

use App\Models\DocumentModel;
use App\Models\TravelRequestModel;

class DocumentCenterController extends BaseController
{
    public function index()
    {
        $documents = new DocumentModel();
        $travel = new TravelRequestModel();

        $recentDocuments = $documents->select('documents.id, documents.doc_type, documents.doc_no, documents.title, documents.status, documents.created_at, users.first_name, users.last_name')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->orderBy('documents.created_at', 'DESC')
            ->findAll(8);

        $recentTravel = $travel->select('travel_requests.id, travel_requests.doc_no, travel_requests.title, travel_requests.status, travel_requests.start_date, travel_requests.created_at, users.first_name, users.last_name')
            ->join('users', 'users.id = travel_requests.user_id', 'left')
            ->orderBy('travel_requests.created_at', 'DESC')
            ->findAll(6);

        return view('documents/center', [
            'totalDocuments' => $documents->countAllResults(),
            'totalMemos' => $documents->where('doc_type', 'internal')->countAllResults(),
            'totalTravel' => $travel->countAllResults(),
            'recentDocuments' => $recentDocuments,
            'recentTravel' => $recentTravel,
        ]);
    }
}
