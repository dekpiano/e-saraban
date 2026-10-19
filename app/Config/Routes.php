<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Public / Tracking routes (ประชาชนและผู้ใช้ทั่วไปดูขั้นตอนและเอกสารได้แบบ Read-only)
$routes->get('/', 'PublicController::index');
$routes->get('tracking', 'PublicController::index');
$routes->get('tracking/(:num)', 'PublicController::track/$1');
$routes->get('api/track-document/(:num)', 'PublicController::trackDocJson/$1');

// Public / Auth routes
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->get('login/google', 'AuthController::googleRedirect');
$routes->get('login/google/callback', 'AuthController::googleCallback');
$routes->post('login/google/verify', 'AuthController::googleVerify');
$routes->get('logout', 'AuthController::logout');
$routes->get('select-role', 'AuthController::selectRole');
$routes->post('select-role', 'AuthController::processSelectRole');
$routes->get('switch-role/(:segment)', 'AuthController::switchRole/$1');
$routes->get('switch-user/(:num)', 'AuthController::switchUser/$1');
$routes->get('verify/(:segment)', 'DocumentController::verify/$1');

// Public Read-Only Entry: รายงานผลการไปราชการ (ถ้าไม่ได้ login แค่เห็นอย่างเดียว จัดการอะไรไม่ได้)
$routes->get('admin/travel-reports', 'TravelReportController::index');
$routes->get('admin/travel-reports/(:num)', 'TravelReportController::view/$1');
$routes->get('travel-reports', 'TravelReportController::index');
$routes->get('travel-reports/(:num)', 'TravelReportController::view/$1');

// Protected Admin / Back-office Routes (ระบบหลังบ้าน: สมาชิกที่เข้าสู่ระบบแล้ว)
$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
    $routes->get('', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // Profile & e-Signature
    $routes->get('profile', 'AuthController::profile');
    $routes->post('profile/signature', 'AuthController::saveSignature');
    $routes->post('profile/signature/delete', 'AuthController::deleteSignature');

    // Documents & e-Saraban
    $routes->get('documents', 'DocumentController::index');
    $routes->get('documents/inbox', 'DocumentController::inbox');
    $routes->get('documents/signed', 'DocumentController::signed');
    $routes->get('documents/my', 'DocumentController::myDocuments');
    $routes->get('documents/create', 'DocumentController::create');
    $routes->post('documents/store', 'DocumentController::store');
    $routes->get('documents/(:num)', 'DocumentController::view/$1');
    $routes->post('documents/(:num)/sign', 'DocumentController::sign/$1');
    $routes->post('documents/(:num)/upload', 'DocumentController::uploadFile/$1');

    // บันทึกข้อความ (ภายในโรงเรียน)
    $routes->get('memos', 'InternalMemoController::index');
    $routes->get('memos/create', 'InternalMemoController::create');
    $routes->post('memos/store', 'InternalMemoController::store');
    $routes->get('memos/(:num)', 'InternalMemoController::view/$1');
    $routes->get('memos/(:num)/print', 'InternalMemoController::printView/$1');
    $routes->get('memos/(:num)/edit', 'InternalMemoController::edit/$1');
    $routes->post('memos/(:num)/update', 'InternalMemoController::update/$1');
    $routes->post('memos/(:num)/delete', 'InternalMemoController::delete/$1');
    $routes->post('memos/(:num)/sign', 'InternalMemoController::sign/$1');

    // Official Travel Requests & Tracking (ไปราชการและติดตาม)
    $routes->get('travel', 'TravelController::index');
    $routes->get('travel/my', 'TravelController::my');
    $routes->get('travel/inbox', 'TravelController::inbox');
    $routes->get('travel/create', 'TravelController::create');
    $routes->get('travel/check-duplicate', 'TravelController::checkDuplicate');
    $routes->post('travel/store', 'TravelController::store');
    $routes->get('travel/(:num)', 'TravelController::view/$1');
    $routes->post('travel/(:num)/sign', 'TravelController::sign/$1');
    $routes->post('travel/(:num)/update-step', 'TravelController::updateStep/$1');
    $routes->post('travel/(:num)/report', 'TravelController::report/$1');
    $routes->post('travel/(:num)/upload', 'TravelController::uploadFile/$1');
    $routes->post('travel/(:num)/delete', 'TravelController::delete/$1');

    // Dedicated Subsystem: ระบบรายงานผลการไปราชการ (การจัดการต้อง Login แล้วเท่านั้น)
    $routes->get('travel-reports/my', 'TravelReportController::my');
    $routes->get('travel-reports/create', 'TravelReportController::create');
    $routes->get('travel-reports/get-travel-info/(:num)', 'TravelReportController::getTravelInfo/$1');
    $routes->post('travel-reports/store', 'TravelReportController::store');
    $routes->get('travel-reports/(:num)/edit', 'TravelReportController::edit/$1');
    $routes->post('travel-reports/(:num)/update', 'TravelReportController::update/$1');
    $routes->post('travel-reports/(:num)/delete', 'TravelReportController::delete/$1');

    // Admin Only: จัดการผู้ใช้งานและกำหนดสิทธิ์ (ต้องมีสิทธิ์ super_admin/admin)
    $routes->group('', ['filter' => 'admin'], static function ($routes) {
        $routes->get('users', 'Admin\UserController::index');
        $routes->get('users/create', 'Admin\UserController::create');
        $routes->post('users/store', 'Admin\UserController::store');
        $routes->get('users/(:num)/edit', 'Admin\UserController::edit/$1');
        $routes->post('users/(:num)/update', 'Admin\UserController::update/$1');
        $routes->post('users/(:num)/toggle-status', 'Admin\UserController::toggleStatus/$1');
        $routes->post('users/(:num)/reset-password', 'Admin\UserController::resetPassword/$1');
        $routes->post('users/(:num)/delete', 'Admin\UserController::delete/$1');
        $routes->get('roles', 'Admin\UserController::roles');
    });
});

// Backward compatibility redirects for direct legacy paths
$routes->addRedirect('dashboard', 'admin/dashboard');
$routes->addRedirect('profile', 'admin/profile');
$routes->post('profile/signature', 'AuthController::saveSignature', ['filter' => 'auth']);
$routes->post('profile/signature/delete', 'AuthController::deleteSignature', ['filter' => 'auth']);
$routes->addRedirect('documents', 'admin/documents');
$routes->addRedirect('documents/(:any)', 'admin/documents/$1');
$routes->addRedirect('travel', 'admin/travel');
$routes->addRedirect('travel/(:any)', 'admin/travel/$1');
$routes->addRedirect('memos', 'admin/memos');
$routes->addRedirect('memos/(:any)', 'admin/memos/$1');

