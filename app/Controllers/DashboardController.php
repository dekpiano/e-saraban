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

        // 1. ข้อมูลผู้ใช้งานปัจจุบัน
        $currentUser = $userId ? $this->userModel->getUserWithDetails($userId) : null;
        $hasSignature = !empty($currentUser['signature_img']) || !empty(session()->get('signature_img'));

        // 2. สถิติหนังสือตามบทบาทผู้ใช้
        $pendingDocs = $this->documentModel->getDocumentsForUser($userId, 'inbox');
        $signedDocs  = $this->documentModel->getDocumentsForUser($userId, 'signed');
        $myDocs      = $this->documentModel->getDocumentsForUser($userId, 'my_created');
        $totalDocs   = (clone $this->documentModel)->countAllResults();

        // 3. สถิติตามประเภทหนังสือราชการสารบรรณ (4 หมวดหมู่หลัก)
        $inCount       = (clone $this->documentModel)->where('doc_type', 'in')->countAllResults();
        $outCount      = (clone $this->documentModel)->where('doc_type', 'out')->countAllResults();
        $internalCount = (clone $this->documentModel)->where('doc_type', 'internal')->countAllResults();
        $orderCount    = (clone $this->documentModel)->where('doc_type', 'order')->countAllResults();
        $circularCount = (clone $this->documentModel)->where('doc_type', 'circular')->countAllResults();

        // 4. การแจ้งเตือนงานด่วนของระบบสารบรรณ (Alerts & Action Items)
        // 4.1 บันทึกข้อความรอสารบรรณตรวจสอบและออกเลข
        $pendingSarabanCount = (clone $this->documentModel)
            ->where('doc_type', 'internal')
            ->where('status', 'pending_saraban')
            ->countAllResults();

        $pendingSarabanMemos = (clone $this->documentModel)
            ->select('documents.*, users.first_name, users.last_name, users.prefix, departments.name as creator_department')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->where('documents.doc_type', 'internal')
            ->where('documents.status', 'pending_saraban')
            ->orderBy('documents.created_at', 'ASC')
            ->findAll(5);

        // 4.2 หนังสือเร่งด่วนที่กำลังอยู่ระหว่างการเสนอเกษียน
        $urgentDocsCount = (clone $this->documentModel)
            ->whereIn('urgency', ['urgent', 'very_urgent', 'most_urgent'])
            ->where('status', 'routing')
            ->countAllResults();

        // 4.3 คำสั่งโรงเรียนที่เป็นแบบร่าง
        $draftOrderCount = (clone $this->documentModel)
            ->where('doc_type', 'order')
            ->where('status', 'draft')
            ->countAllResults();

        // 5. สถิติงานไปราชการ
        $pendingTravelMe       = $this->travelModel->getRequestsForUser($userId, 'pending_me');
        $myTravel              = $this->travelModel->getRequestsForUser($userId, 'my');
        $totalTravel           = (clone $this->travelModel)->countAllResults();
        $allPendingTravelCount = (clone $this->travelModel)->where('status', 'pending')->countAllResults();
        $returnTravelCount     = (clone $this->travelModel)->where('status', 'approved')->where('return_step >=', 1)->where('return_step <=', 5)->countAllResults();

        // 6. รายการตารางสำหรับแท็บต่าง ๆ
        $recentPending = array_slice($pendingDocs, 0, 5);

        // รายการหนังสือราชการล่าสุดทุกประเภท
        $recentAll = (clone $this->documentModel)
            ->select('documents.*, users.first_name, users.last_name, users.prefix')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->orderBy('documents.created_at', 'DESC')
            ->findAll(5);

        // รายการคำสั่งโรงเรียนล่าสุด
        $recentOrders = (clone $this->documentModel)
            ->select('documents.*, users.first_name, users.last_name, users.prefix')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->where('documents.doc_type', 'order')
            ->orderBy('documents.created_at', 'DESC')
            ->findAll(5);

        // รายการคำขอไปราชการล่าสุด
        $recentTravel = (clone $this->travelModel)
            ->select('travel_requests.*, users.first_name, users.last_name, users.prefix')
            ->join('users', 'users.id = travel_requests.user_id', 'left')
            ->orderBy('travel_requests.created_at', 'DESC')
            ->findAll(5);

        // รายการบันทึกข้อความที่ฉันสร้าง (สำหรับครูและบุคลากร)
        $myMemos = (clone $this->documentModel)
            ->where('doc_type', 'internal')
            ->where('created_by', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll(10);

        // Switchable users for demo bar
        $switchUsers = $this->userModel->getAllActiveUsers();

        return view('dashboard/dashboard_index', [
            'hasSignature'          => $hasSignature,
            // สถิติตัวเลขทั่วไป
            'pendingCount'          => count($pendingDocs),
            'signedCount'           => count($signedDocs),
            'myDocsCount'           => count($myDocs),
            'totalCount'            => $totalDocs,
            'myMemos'               => $myMemos,
            'myMemosCount'          => count($myMemos),
            // 4 เสาหลักสารบรรณ
            'inCount'               => $inCount,
            'outCount'              => $outCount,
            'internalCount'         => $internalCount,
            'orderCount'            => $orderCount,
            'circularCount'         => $circularCount,
            // การแจ้งเตือนงานด่วน
            'pendingSarabanCount'   => $pendingSarabanCount,
            'urgentDocsCount'       => $urgentDocsCount,
            'draftOrderCount'       => $draftOrderCount,
            'allPendingTravelCount' => $allPendingTravelCount,
            'returnTravelCount'     => $returnTravelCount,
            // ไปราชการ
            'pendingTravelCount'    => count($pendingTravelMe),
            'myTravelCount'         => count($myTravel),
            'myTravelList'          => array_slice($myTravel, 0, 5),
            'totalTravelCount'      => $totalTravel,
            // ข้อมูลสำหรับแท็บ
            'pendingSarabanMemos'   => $pendingSarabanMemos,
            'recentPending'         => $recentPending,
            'recentAll'             => $recentAll,
            'recentOrders'          => $recentOrders,
            'recentTravel'          => $recentTravel,
            'switchUsers'           => $switchUsers,
        ]);
    }
}
