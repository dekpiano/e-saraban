<?php

namespace App\Controllers;

use App\Models\DocumentModel;
use App\Models\DocumentRouteModel;
use App\Models\TravelRequestModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    protected $documentModel;
    protected $routeModel;
    protected $travelModel;
    protected $userModel;

    public function __construct()
    {
        $this->documentModel = new DocumentModel();
        $this->routeModel    = new DocumentRouteModel();
        $this->travelModel   = new TravelRequestModel();
        $this->userModel     = new UserModel();
        helper(['thai_date', 'url']);
    }

    public function index()
    {
        $userId = session()->get('user_id');

        // Document Stats
        $pendingDocs = $this->documentModel->getDocumentsForUser($userId, 'inbox');
        $signedDocs  = $this->documentModel->getDocumentsForUser($userId, 'signed');
        $myDocs      = $this->documentModel->getDocumentsForUser($userId, 'my_created');
        $totalDocs   = $this->documentModel->countAllResults();

        // Travel Request Stats
        $pendingTravel = $this->travelModel->getRequestsForUser($userId, 'pending_me');
        $myTravel      = $this->travelModel->getRequestsForUser($userId, 'my');
        $totalTravel   = $this->travelModel->countAllResults();

        // Recent documents awaiting action
        $recentPending = array_slice($pendingDocs, 0, 5);

        // Recent all documents
        $recentAll = $this->documentModel->select('documents.*, users.first_name, users.last_name, users.prefix')
                                         ->join('users', 'users.id = documents.created_by', 'left')
                                         ->orderBy('documents.created_at', 'DESC')
                                         ->findAll(5);

        // Switchable users for demo bar
        $switchUsers = $this->userModel->getAllActiveUsers();

        return view('dashboard/index', [
            'pendingCount'       => count($pendingDocs),
            'signedCount'        => count($signedDocs),
            'myDocsCount'        => count($myDocs),
            'totalCount'         => $totalDocs,
            'pendingTravelCount' => count($pendingTravel),
            'myTravelCount'      => count($myTravel),
            'totalTravelCount'   => $totalTravel,
            'recentPending'      => $recentPending,
            'recentTravel'       => $pendingTravel,
            'recentAll'          => $recentAll,
            'switchUsers'        => $switchUsers,
        ]);
    }
}
