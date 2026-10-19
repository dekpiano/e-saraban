<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'กรุณาเข้าสู่ระบบก่อนเข้าใช้งาน');
        }

        if (!in_array($session->get('role'), ['super_admin', 'admin'], true)) {
            return redirect()->to('/admin/dashboard')->with('error', 'ขออภัย เฉพาะผู้ดูแลระบบ (Supper Admin / Admin) เท่านั้นที่สามารถเข้าถึงส่วนงานจัดการเจ้าหน้าที่ระบบได้');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
