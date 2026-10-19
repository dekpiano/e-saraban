<?php

if (!function_exists('thai_date')) {
    function thai_date($date, $format = 'full', $showTime = false)
    {
        if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return '-';
        }

        $timestamp = is_numeric($date) ? $date : strtotime($date);
        if (!$timestamp) {
            return '-';
        }

        $thaiMonthsFull = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
            5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
            9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
        ];

        $thaiMonthsShort = [
            1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
            5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
            9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
        ];

        $day   = date('j', $timestamp);
        $month = (int)date('n', $timestamp);
        $year  = (int)date('Y', $timestamp) + 543;

        $monthName = ($format === 'short') ? $thaiMonthsShort[$month] : $thaiMonthsFull[$month];
        $timeStr   = $showTime ? ' เวลา ' . date('H:i', $timestamp) . ' น.' : '';

        return "{$day} {$monthName} {$year}{$timeStr}";
    }
}

if (!function_exists('urgency_badge')) {
    function urgency_badge($urgency)
    {
        switch ($urgency) {
            case 'most_urgent':
                return '<span class="badge bg-danger text-white fw-bold"><i class="bx bxs-flame me-1"></i> ด่วนที่สุด</span>';
            case 'very_urgent':
                return '<span class="badge bg-warning text-dark fw-bold"><i class="bx bx-error me-1"></i> ด่วนมาก</span>';
            case 'urgent':
                return '<span class="badge bg-info text-white fw-bold"><i class="bx bx-bell me-1"></i> ด่วน</span>';
            default:
                return '<span class="badge bg-secondary text-white fw-medium">ปกติ</span>';
        }
    }
}

if (!function_exists('doc_type_badge')) {
    function doc_type_badge($type)
    {
        switch ($type) {
            case 'in':
                return '<span class="badge bg-primary text-white fw-semibold"><i class="bx bx-log-in-circle me-1"></i> หนังสือรับ</span>';
            case 'out':
                return '<span class="badge bg-success text-white fw-semibold"><i class="bx bx-log-out-circle me-1"></i> หนังสือส่ง</span>';
            case 'internal':
                return '<span class="badge bg-info text-white fw-semibold"><i class="bx bx-message-detail me-1"></i> บันทึกข้อความ</span>';
            case 'order':
                return '<span class="badge bg-warning text-dark fw-bold"><i class="bx bx-award me-1"></i> คำสั่ง รร.</span>';
            case 'circular':
                return '<span class="badge bg-secondary text-white fw-semibold"><i class="bx bx-broadcast me-1"></i> หนังสือเวียน</span>';
            case 'travel':
                return '<span class="badge bg-warning text-dark fw-bold"><i class="bx bx-briefcase-alt-2 me-1"></i> ขอไปราชการ</span>';
            default:
                return '<span class="badge bg-secondary text-white">' . esc($type) . '</span>';
        }
    }
}

if (!function_exists('doc_status_badge')) {
    function doc_status_badge($status)
    {
        switch ($status) {
            case 'routing':
                return '<span class="badge bg-primary text-white fw-semibold"><i class="bx bx-time me-1"></i> อยู่ระหว่างเสนอเกษียน</span>';
            case 'completed':
                return '<span class="badge bg-success text-white fw-semibold"><i class="bx bx-check-circle me-1"></i> เสร็จสิ้น/อนุมัติแล้ว</span>';
            case 'returned':
                return '<span class="badge bg-danger text-white fw-bold"><i class="bx bx-revision me-1"></i> ส่งคืนแก้ไข</span>';
            case 'draft':
                return '<span class="badge bg-secondary text-white">ร่างเอกสาร</span>';
            default:
                return '<span class="badge bg-secondary text-white">' . esc($status) . '</span>';
        }
    }
}

if (!function_exists('travel_status_badge')) {
    function travel_status_badge($status)
    {
        switch ($status) {
            case 'pending':
                return '<span class="badge bg-label-warning"><i class="bx bx-time me-1"></i> อยู่ระหว่างพิจารณา</span>';
            case 'approved':
                return '<span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i> อนุมัติไปราชการแล้ว</span>';
            case 'returned':
                return '<span class="badge bg-label-danger"><i class="bx bx-revision me-1"></i> ส่งคืนแก้ไข</span>';
            case 'reported':
                return '<span class="badge bg-label-info"><i class="bx bx-task me-1"></i> รายงานผลแล้ว</span>';
            default:
                return '<span class="badge bg-label-secondary">' . esc($status) . '</span>';
        }
    }
}

if (!function_exists('travel_transport_badge')) {
    function travel_transport_badge($type)
    {
        switch ($type) {
            case 'private_car':
                return '<span class="badge bg-label-primary"><i class="bx bx-car me-1"></i> รถยนต์ส่วนบุคคล</span>';
            case 'school_car':
                return '<span class="badge bg-label-info"><i class="bx bx-bus me-1"></i> รถยนต์ส่วนกลาง รร.</span>';
            case 'public_transport':
                return '<span class="badge bg-label-secondary"><i class="bx bx-train me-1"></i> ยานพาหนะสาธารณะ</span>';
            default:
                return '<span class="badge bg-label-secondary">' . esc($type) . '</span>';
        }
    }
}

if (!function_exists('travel_budget_badge')) {
    function travel_budget_badge($type, $amount = 0)
    {
        switch ($type) {
            case 'school_budget':
                return '<span class="badge bg-label-danger"><i class="bx bx-money me-1"></i> เบิกงบ รร. (' . number_format($amount, 2) . ' บ.)</span>';
            case 'organizer_budget':
                return '<span class="badge bg-label-info"><i class="bx bx-wallet me-1"></i> ผู้จัดออกให้</span>';
            case 'no_budget':
            default:
                return '<span class="badge bg-label-secondary"><i class="bx bx-shield-x me-1"></i> ไม่ขอเบิกงบประมาณ</span>';
        }
    }
}

if (!function_exists('travel_step_badge')) {
    function travel_step_badge($step, $status = 'pending', $approverTitle = null, $returnStep = 0)
    {
        if ($status === 'approved') {
            if ((int)$returnStep === 3) {
                return '<span class="badge bg-success text-white"><i class="bx bx-check-double me-1"></i> ผู้ขอรับเอกสารตัวจริงแล้ว</span>';
            }
            if ((int)$returnStep === 2) {
                return '<span class="badge bg-label-info"><i class="bx bx-archive-in me-1"></i> ธุรการ รร. รับตัวจริงแล้ว</span>';
            }
            if ((int)$returnStep === 1) {
                return '<span class="badge bg-label-primary"><i class="bx bx-paper-plane me-1"></i> อบจ. ส่งตัวจริงคืน รร.</span>';
            }
            if (!empty($approverTitle) && str_contains($approverTitle, 'ปลัด')) {
                return '<span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i> ปลัด อบจ. อนุมัติแล้ว</span>';
            }
            return '<span class="badge bg-label-success"><i class="bx bx-check-double me-1"></i> นายก อบจ. อนุมัติแล้ว</span>';
        }
        if ($status === 'returned') {
            return '<span class="badge bg-label-danger"><i class="bx bx-revision me-1"></i> ส่งคืนแก้ไข</span>';
        }
        if ($status === 'reported') {
            return '<span class="badge bg-label-info"><i class="bx bx-task me-1"></i> รายงานผลแล้ว</span>';
        }
        switch ((int)$step) {
            case 1:
                return '<span class="badge bg-label-info"><i class="bx bx-paper-plane me-1"></i> 1. ยื่นขออนุมัติ</span>';
            case 2:
                return '<span class="badge bg-label-secondary"><i class="bx bx-building me-1"></i> 2. โรงเรียน (จัดทำเรื่อง/แนบไฟล์)</span>';
            case 3:
                return '<span class="badge bg-label-primary"><i class="bx bx-book-reader me-1"></i> 3. กองการศึกษา</span>';
            case 4:
                return '<span class="badge bg-label-info"><i class="bx bx-id-card me-1"></i> 4. กองการเจ้าหน้าที่</span>';
            case 5:
                return '<span class="badge bg-label-warning text-dark"><i class="bx bx-briefcase-alt-2 me-1"></i> 5. สำนักปลัด</span>';
            case 6:
                return '<span class="badge bg-label-danger"><i class="bx bx-crown me-1"></i> 6. นายก อบจ. (รออนุมัติ)</span>';
            default:
                return '<span class="badge bg-label-primary">ขั้นที่ ' . esc($step) . '</span>';
        }
    }
}

if (!function_exists('travel_return_badge')) {
    function travel_return_badge($returnStep = 0)
    {
        switch ((int)$returnStep) {
            case 1:
                return '<span class="badge bg-label-primary"><i class="bx bx-paper-plane me-1"></i> 1. นายก อบจ. ส่งตัวจริง</span>';
            case 2:
                return '<span class="badge bg-label-info"><i class="bx bx-briefcase-alt-2 me-1"></i> 2. สำนักปลัด</span>';
            case 3:
                return '<span class="badge bg-label-info"><i class="bx bx-id-card me-1"></i> 3. กองการเจ้าหน้าที่</span>';
            case 4:
                return '<span class="badge bg-label-info"><i class="bx bx-book-reader me-1"></i> 4. กองการศึกษา</span>';
            case 5:
                return '<span class="badge bg-label-warning text-dark"><i class="bx bx-archive-in me-1"></i> 5. ธุรการโรงเรียนลงรับ</span>';
            case 6:
                return '<span class="badge bg-success text-white"><i class="bx bx-check-double me-1"></i> 6. ผู้ขอรับตัวจริงแล้ว (เสร็จสิ้น)</span>';
            default:
                return '<span class="badge bg-label-secondary"><i class="bx bx-time me-1"></i> ยังไม่เริ่มส่งคืนตัวจริง</span>';
        }
    }
}

if (!function_exists('document_url')) {
    function document_url($filePath)
    {
        if (empty($filePath)) {
            return '';
        }
        // If it's already an absolute URL (http:// or https://)
        if (preg_match('#^https?://#i', $filePath)) {
            return $filePath;
        }
        // If stored under remote upload path (saraban/... or travel/...)
        $baseUrl = env('upload.server.baseurl') ?: 'https://skj.nsnpao.go.th/uploads/';
        if (str_starts_with($filePath, 'saraban/') || str_starts_with($filePath, 'travel/')) {
            return rtrim($baseUrl, '/') . '/' . ltrim($filePath, '/');
        }
        return base_url($filePath);
    }
}


