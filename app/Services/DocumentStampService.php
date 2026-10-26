<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;

class DocumentStampService
{
    protected string $fontPath;
    protected string $fontBoldPath;

    public function __construct()
    {
        $this->fontPath = FCPATH . 'assets/vendor/fonts/THSarabunIT๙.ttf';
        $this->fontBoldPath = FCPATH . 'assets/vendor/fonts/THSarabunIT๙ Bold.ttf';
    }

    /**
     * ประทับตรายางรับหนังสือลงในไฟล์เอกสาร (PDF หรือ รูปภาพ) กลืนเป็นไฟล์เดียวกัน
     * 
     * @param string $filePath พาธไฟล์เต็มบนดิสก์
     * @param array $stampData ข้อมูลในตรายาง ['reg_no', 'doc_date', 'doc_time', 'receiver_name']
     * @param array $coords ข้อมูลพิกัด ['x' => '65%', 'y' => '1.2cm', 'scale' => 1.0]
     * @return string|false พาธไฟล์ผลลัพธ์ (อาจเป็นพาธเดิมหรือพาธใหม่) หรือ false หากล้มเหลว
     */
    public function imprintReceiveStamp(string $filePath, array $stampData, array $coords = [])
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            return $this->stampImage($filePath, $stampData, $coords);
        }

        if ($ext === 'pdf') {
            return $this->stampPdf($filePath, $stampData, $coords);
        }

        return $filePath;
    }

    /**
     * สร้างรูปภาพตรายางรับหนังสือ (PNG Transparent) คุณภาพสูง
     * 
     * @param array $stampData
     * @param float $scale
     * @return \GdImage|resource|false
     */
    public function createStampImageResource(array $stampData, float $scale = 1.0)
    {
        // ============================================================
        // ตรายางรับหนังสือราชการ ตัดจากรูปตรายางจริง ๑:๑
        // ใช้ไฟล์ภาพแม่แบบ: assets/img/stamp_template_skj.png (รร.ส.ก.จ.)
        // ============================================================
        $templatePath = FCPATH . 'assets/img/stamp_template_skj.png';
        if (!file_exists($templatePath)) {
            $templatePath = '/var/www/html/public/assets/img/stamp_template_skj.png';
        }

        if (file_exists($templatePath)) {
            $tpl = imagecreatefrompng($templatePath);
            $baseW = imagesx($tpl);
            $baseH = imagesy($tpl);

            $img = imagecreatetruecolor($baseW, $baseH);
            imagealphablending($img, false);
            imagesavealpha($img, true);
            $trans = imagecolorallocatealpha($img, 0, 0, 0, 127);
            imagefill($img, 0, 0, $trans);

            // Copy template onto working image
            imagecopy($img, $tpl, 0, 0, 0, 0, $baseW, $baseH);
            imagedestroy($tpl);
            imagealphablending($img, true);

            $fontBold = file_exists($this->fontBoldPath) ? $this->fontBoldPath : $this->fontPath;
            $inkColor = imagecolorallocate($img, 20, 42, 130);

            $regNo = $stampData['reg_no'] ?? '-';
            $dateText = $stampData['doc_date'] ?? thai_date(date('Y-m-d'), 'medium');
            $timeText = $stampData['doc_time'] ?? (date('H:i') . ' น.');

            // ปรับตำแหน่งแกน Y และ X ให้ตัวเลขและข้อความวางลอยเหนือเส้นจุดไข่ปลาเล็กน้อยอย่างสง่างาม
            if ($fontBold) {
                // ขนาดภาพแม่แบบคือ 452px x 227px (พิมพ์ลง A4 กว้าง 50mm)
                // ดังนั้นฟอนต์ 28pt บนภาพนี้ เมื่อย่อลงกระดาษจริงจะเท่ากับ ~16pt สวยงาม ชัดเจน ไม่เล็กเกินไป
                $fontSize = 32;
                imagettftext($img, $fontSize, 0, 122, 94, $inkColor, $fontBold, $regNo);
                imagettftext($img, $fontSize, 0, 114, 147, $inkColor, $fontBold, $dateText);
                imagettftext($img, $fontSize, 0, 108, 200, $inkColor, $fontBold, $timeText);
            }
        } else {
            // Fallback กรณีหาไฟล์ภาพไม่พบ
            $baseW = 452;
            $baseH = 227;
            $img = imagecreatetruecolor($baseW, $baseH);
            $white = imagecolorallocate($img, 255, 255, 255);
            $blue = imagecolorallocate($img, 20, 42, 130);
            imagefill($img, 0, 0, $white);
            imagerectangle($img, 2, 2, $baseW - 3, $baseH - 3, $blue);
            imagestring($img, 5, 180, 20, "รร.ส.ก.จ.555", $blue);
        }

        // หากมีการปรับ Scale
        if ($scale !== 1.0 && $scale > 0.2 && $scale < 3.0) {
            $newW = (int) round($baseW * $scale);
            $newH = (int) round($baseH * $scale);
            $scaledImg = imagecreatetruecolor($newW, $newH);
            imagealphablending($scaledImg, false);
            imagesavealpha($scaledImg, true);
            $transparentScaled = imagecolorallocatealpha($scaledImg, 0, 0, 0, 127);
            imagefilledrectangle($scaledImg, 0, 0, $newW, $newH, $transparentScaled);
            imagecopyresampled($scaledImg, $img, 0, 0, 0, 0, $newW, $newH, $baseW, $baseH);
            imagedestroy($img);
            return $scaledImg;
        }

        return $img;
    }

    /**
     * วาดข้อความกึ่งกลางแกน X
     */
    protected function drawCenteredText($image, $size, $angle, $y, $color, $fontFile, $text, $imgWidth)
    {
        $box = imagettfbbox($size, $angle, $fontFile, $text);
        $textWidth = abs($box[4] - $box[0]);
        $x = (int) round(($imgWidth - $textWidth) / 2);
        imagettftext($image, $size, $angle, $x, $y, $color, $fontFile, $text);
    }

    /**
     * วาดเส้นประจุดไข่ปลา (Dotted line) เหมือน CSS border-bottom: 1px dotted
     */
    protected function drawDottedLine($image, int $x1, int $y, int $x2, int $color, int $dotGap = 4): void
    {
        imagesetthickness($image, 1);
        for ($x = $x1; $x < $x2; $x += $dotGap) {
            imagesetpixel($image, $x, $y, $color);
            if ($x + 1 < $x2) {
                imagesetpixel($image, $x + 1, $y, $color);
            }
        }
    }

    /**
     * ประทับตรายางลงบนไฟล์ภาพ (JPG, PNG, WEBP)
     */
    public function stampImage(string $imagePath, array $stampData, array $coords = [])
    {
        $info = getimagesize($imagePath);
        if (!$info)
            return false;

        $mime = $info['mime'];
        $srcImg = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($imagePath),
            'image/png' => imagecreatefrompng($imagePath),
            'image/webp' => imagecreatefromwebp($imagePath),
            default => null,
        };

        if (!$srcImg)
            return false;

        $imgW = imagesx($srcImg);
        $imgH = imagesy($srcImg);

        $stampImg = $this->createStampImageResource($stampData, 1.0);
        if (!$stampImg) {
            imagedestroy($srcImg);
            return false;
        }

        $baseW = imagesx($stampImg);
        $baseH = imagesy($stampImg);

        // คำนวณความกว้างตรายางให้ตรงตามที่ผู้ใช้เลือกบนหน้าจอ 100%
        if (!empty($coords['w_pct']) && str_ends_with(trim($coords['w_pct']), '%')) {
            $wRatio = (float) rtrim(trim($coords['w_pct']), '%') / 100.0;
            $targetStampW = (int) round($imgW * $wRatio);
        } else {
            $userScale = isset($coords['scale']) ? (float) $coords['scale'] : 1.0;
            // มาตรฐาน 5cm บนหน้ากระดาษ A4 (21cm x 29.7cm)
            $baseRatio = ($imgW > $imgH) ? (50.0 / 297.0) : (50.0 / 210.0);
            $targetStampW = (int) round($imgW * $baseRatio * $userScale);
        }
        $targetStampH = (int) round($targetStampW * ($baseH / $baseW));

        // คำนวณพิกัด X, Y
        $posX = $this->parseCoordinate($coords['x'] ?? '65%', $imgW, $targetStampW, '0.65');
        $posY = $this->parseCoordinate($coords['y'] ?? '4%', $imgH, $targetStampH, '0.04');

        // ผสานรูปตรายางลงบนภาพเอกสาร (Alpha Blending)
        imagealphablending($srcImg, true);
        imagecopyresampled($srcImg, $stampImg, $posX, $posY, 0, 0, $targetStampW, $targetStampH, imagesx($stampImg), imagesy($stampImg));

        imagedestroy($stampImg);

        // บันทึกทับไฟล์เดิมอย่างคมชัดสูงสุด
        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        if ($ext === 'jpg' || $ext === 'jpeg') {
            imagejpeg($srcImg, $imagePath, 95);
        } elseif ($ext === 'png') {
            imagepng($srcImg, $imagePath, 6);
        } elseif ($ext === 'webp') {
            imagewebp($srcImg, $imagePath, 90);
        } else {
            imagejpeg($srcImg, $imagePath, 95);
        }

        imagedestroy($srcImg);
        return $imagePath;
    }

    /**
     * ตรวจสอบว่าระบบมีโปรแกรม qpdf ติดตั้งอยู่หรือไม่
     */
    public function hasQpdf(): bool
    {
        static $hasQpdf = null;
        if ($hasQpdf === null) {
            exec('which qpdf 2>/dev/null', $output, $returnVar);
            $hasQpdf = ($returnVar === 0);
        }
        return $hasQpdf;
    }

    /**
     * ดึงจำนวนหน้าทั้งหมดของไฟล์ PDF
     */
    public function getPdfPageCount(string $pdfPath): int
    {
        if (!file_exists($pdfPath)) return 0;

        // 1. ลองใช้ qpdf (เร็วและแม่นยำที่สุด)
        if ($this->hasQpdf()) {
            exec('qpdf --show-npages ' . escapeshellarg($pdfPath) . ' 2>/dev/null', $out, $ret);
            if ($ret === 0 && !empty($out[0]) && is_numeric(trim($out[0]))) {
                return (int)trim($out[0]);
            }
        }

        // 2. ลองใช้ pdfinfo (poppler-utils)
        exec('pdfinfo ' . escapeshellarg($pdfPath) . ' 2>/dev/null', $infoOut, $infoRet);
        if ($infoRet === 0) {
            foreach ($infoOut as $line) {
                if (preg_match('/^Pages:\s+(\d+)/i', $line, $matches)) {
                    return (int)$matches[1];
                }
            }
        }

        // 3. ลองใช้ FPDI
        try {
            $pdf = new Fpdi();
            return (int)$pdf->setSourceFile($pdfPath);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * ลบไฟล์ชั่วคราว
     */
    protected function cleanupTempFiles(array $files): void
    {
        foreach ($files as $f) {
            if (!empty($f) && file_exists($f)) {
                @unlink($f);
            }
        }
    }

    /**
     * ประทับตรายางลงบนไฟล์ PDF:
     * - ประทับเฉพาะหน้าแรก (Page 1) ตามระเบียบสารบรรณ
     * - รักษาหน้าทั้งหมดของเอกสาร (Page 2 ถึง N) ไว้ครบถ้วน 100% ไม่ตัดทิ้ง
     */
    public function stampPdf(string $pdfPath, array $stampData, array $coords = [])
    {
        // หากระบบมี qpdf ให้ใช้ engine แยกหน้า 1 มาสแตมป์แล้วนำกลับไปประกบกับหน้าที่เหลือ 100%
        if ($this->hasQpdf()) {
            $stamped = $this->stampPdfQpdf($pdfPath, $stampData, $coords);
            if ($stamped) {
                return $stamped;
            }
        }

        // Fallback ใช้ FPDI วนลูปทุกหน้า
        return $this->stampPdfFallbackFpdi($pdfPath, $stampData, $coords);
    }

    /**
     * ประทับตรายางด้วย qpdf + FPDI Hybrid:
     * ดึงเฉพาะหน้าแรกมาสแตมป์ แล้วผสานเข้ากับหน้าที่เหลือ (2-z) จากไฟล์ต้นฉบับเดิม
     * รับประกันว่าไฟล์มีกี่หน้าก็คงอยู่ครบถ้วน 100% และไม่ติดปัญหา PDF 1.5+ compression
     */
    protected function stampPdfQpdf(string $pdfPath, array $stampData, array $coords = [])
    {
        $tempDir = sys_get_temp_dir();
        $unique = uniqid('stamp_qpdf_', true);

        $totalPageCount = $this->getPdfPageCount($pdfPath);
        if ($totalPageCount <= 0) {
            return false;
        }

        $stampImgRes = $this->createStampImageResource($stampData, 1.0);
        if (!$stampImgRes) {
            return false;
        }
        $baseW = imagesx($stampImgRes);
        $baseH = imagesy($stampImgRes);
        $tempStampPng = $tempDir . DIRECTORY_SEPARATOR . $unique . '_stamp.png';
        imagepng($stampImgRes, $tempStampPng);
        imagedestroy($stampImgRes);

        $tempFiles = [$tempStampPng];

        try {
            // 1. แยกเฉพาะหน้า 1 ออกมา
            $tempP1 = $tempDir . DIRECTORY_SEPARATOR . $unique . '_p1.pdf';
            $tempFiles[] = $tempP1;
            $cmdExtract = sprintf('qpdf %s --pages . 1 -- %s 2>&1', escapeshellarg($pdfPath), escapeshellarg($tempP1));
            exec($cmdExtract, $out1, $ret1);
            if ($ret1 !== 0 || !file_exists($tempP1)) {
                $this->cleanupTempFiles($tempFiles);
                return false;
            }

            // 2. Normalize หน้า 1 (Uncompress streams & object streams เพื่อให้ FPDI อ่านได้ 100%)
            $tempP1Norm = $tempDir . DIRECTORY_SEPARATOR . $unique . '_p1_norm.pdf';
            $tempFiles[] = $tempP1Norm;
            $cmdNorm = sprintf('qpdf --decrypt --object-streams=disable %s %s 2>&1', escapeshellarg($tempP1), escapeshellarg($tempP1Norm));
            exec($cmdNorm, $outNorm, $retNorm);
            $p1Source = ($retNorm === 0 && file_exists($tempP1Norm)) ? $tempP1Norm : $tempP1;

            // 3. ประทับตรายางลงบนหน้า 1 ด้วย FPDI
            $tempStampedP1 = $tempDir . DIRECTORY_SEPARATOR . $unique . '_stamped_p1.pdf';
            $tempFiles[] = $tempStampedP1;

            $pdf = new Fpdi();
            $pdf->setSourceFile($p1Source);
            $templateId = $pdf->importPage(1);
            $size = $pdf->getTemplateSize($templateId);

            $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);

            $pageW = $size['width'];
            $pageH = $size['height'];

            if (!empty($coords['w_pct']) && str_ends_with(trim($coords['w_pct']), '%')) {
                $wRatio = (float) rtrim(trim($coords['w_pct']), '%') / 100.0;
                $stampMmW = $pageW * $wRatio;
            } else {
                $userScale = isset($coords['scale']) ? (float) $coords['scale'] : 1.0;
                $stampMmW = 50.0 * $userScale;
            }
            $stampMmH = ($stampMmW * ($baseH / $baseW));

            $posX = $this->parseCoordinateMm($coords['x'] ?? '65%', $pageW, $stampMmW, 0.65 * $pageW);
            $posY = $this->parseCoordinateMm($coords['y'] ?? '4%', $pageH, $stampMmH, 0.04 * $pageH);

            $pdf->Image($tempStampPng, $posX, $posY, $stampMmW, $stampMmH, 'PNG');
            $pdf->Output($tempStampedP1, 'F');
            $pdf->cleanUp();
            unset($pdf);

            // 4. ผสานหน้า 1 ที่สแตมป์แล้ว เข้ากับหน้าที่เหลือ (หน้า 2 ถึงหน้าสุดท้าย 2-z) จากไฟล์เดิม
            $tempFinal = $tempDir . DIRECTORY_SEPARATOR . $unique . '_final.pdf';
            $tempFiles[] = $tempFinal;

            if ($totalPageCount === 1) {
                copy($tempStampedP1, $tempFinal);
            } else {
                $cmdCombine = sprintf(
                    'qpdf %s --pages %s 1 %s 2-z -- %s 2>&1',
                    escapeshellarg($pdfPath),
                    escapeshellarg($tempStampedP1),
                    escapeshellarg($pdfPath),
                    escapeshellarg($tempFinal)
                );
                exec($cmdCombine, $outCombine, $retCombine);
                if ($retCombine !== 0 || !file_exists($tempFinal)) {
                    $this->cleanupTempFiles($tempFiles);
                    return false;
                }
            }

            // 5. ตรวจสอบยืนยันว่าจำนวนหน้าเท่าเดิม 100%
            $finalPageCount = $this->getPdfPageCount($tempFinal);
            if ($finalPageCount !== $totalPageCount) {
                log_message('error', "Page count mismatch: original {$totalPageCount}, stamped {$finalPageCount}");
                $this->cleanupTempFiles($tempFiles);
                return false;
            }

            // คัดลอกผลลัพธ์ทับไฟล์เป้าหมาย
            copy($tempFinal, $pdfPath);
            $this->cleanupTempFiles($tempFiles);
            return $pdfPath;
        } catch (\Throwable $e) {
            log_message('error', 'Error in stampPdfQpdf: ' . $e->getMessage());
            $this->cleanupTempFiles($tempFiles);
            return false;
        }
    }

    /**
     * Fallback FPDI (วนลูปทุกหน้าและบันทึกลง temp ก่อนคัดลอกทับ ป้องกัน stream collision)
     */
    protected function stampPdfFallbackFpdi(string $pdfPath, array $stampData, array $coords = [])
    {
        $tempStampFile = null;
        $tempOutFile = null;
        try {
            $stampImgRes = $this->createStampImageResource($stampData, 1.0);
            if (!$stampImgRes)
                return false;

            $baseW = imagesx($stampImgRes);
            $baseH = imagesy($stampImgRes);

            $tempStampFile = tempnam(sys_get_temp_dir(), 'stamp_') . '.png';
            imagepng($stampImgRes, $tempStampFile);
            imagedestroy($stampImgRes);

            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($pdfPath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);

                if ($pageNo === 1) {
                    $pageW = $size['width'];
                    $pageH = $size['height'];

                    if (!empty($coords['w_pct']) && str_ends_with(trim($coords['w_pct']), '%')) {
                        $wRatio = (float) rtrim(trim($coords['w_pct']), '%') / 100.0;
                        $stampMmW = $pageW * $wRatio;
                    } else {
                        $userScale = isset($coords['scale']) ? (float) $coords['scale'] : 1.0;
                        $stampMmW = 50.0 * $userScale;
                    }
                    $stampMmH = ($stampMmW * ($baseH / $baseW));

                    $posX = $this->parseCoordinateMm($coords['x'] ?? '65%', $pageW, $stampMmW, 0.65 * $pageW);
                    $posY = $this->parseCoordinateMm($coords['y'] ?? '4%', $pageH, $stampMmH, 0.04 * $pageH);

                    $pdf->Image($tempStampFile, $posX, $posY, $stampMmW, $stampMmH, 'PNG');
                }
            }

            @unlink($tempStampFile);
            $tempOutFile = tempnam(sys_get_temp_dir(), 'fpdi_out_') . '.pdf';
            $pdf->Output($tempOutFile, 'F');
            $pdf->cleanUp();
            unset($pdf);

            if (file_exists($tempOutFile) && filesize($tempOutFile) > 0) {
                copy($tempOutFile, $pdfPath);
                @unlink($tempOutFile);
                return $pdfPath;
            }

            return false;
        } catch (\Throwable $e) {
            log_message('error', 'Error in stampPdfFallbackFpdi: ' . $e->getMessage());
            if ($tempStampFile && file_exists($tempStampFile)) @unlink($tempStampFile);
            if ($tempOutFile && file_exists($tempOutFile)) @unlink($tempOutFile);
            return false;
        }
    }

    /**
     * คำนวณพิกัด Pixel จาก String เช่น '65%', '1.2cm', '45px'
     */
    protected function parseCoordinate(string $val, int $totalDimension, int $elementDimension, string $defaultRatio = '0'): int
    {
        $val = trim($val);
        if (str_ends_with($val, '%')) {
            $pct = (float) rtrim($val, '%') / 100.0;
            return (int) round($totalDimension * $pct);
        }
        if (str_ends_with($val, 'px')) {
            return (int) round((float) rtrim($val, 'px'));
        }
        if (str_ends_with($val, 'cm')) {
            // สมมติความละเอียด A4 = 29.7cm
            $cm = (float) rtrim($val, 'cm');
            $pxPerCm = $totalDimension / 29.7;
            return (int) round($cm * $pxPerCm);
        }
        if (is_numeric($val)) {
            return (int) $val;
        }
        return (int) round($totalDimension * (float) $defaultRatio);
    }

    /**
     * คำนวณพิกัดมิลลิเมตร (mm) สำหรับ PDF
     */
    protected function parseCoordinateMm(string $val, float $totalMm, float $elementMm, float $defaultMm): float
    {
        $val = trim($val);
        if (str_ends_with($val, '%')) {
            $pct = (float) rtrim($val, '%') / 100.0;
            return max(0.0, min($totalMm - $elementMm, $totalMm * $pct));
        }
        if (str_ends_with($val, 'cm')) {
            $mm = (float) rtrim($val, 'cm') * 10.0;
            return max(0.0, min($totalMm - $elementMm, $mm));
        }
        if (str_ends_with($val, 'px')) {
            // สมมติหน้าจอ 96 DPI: 1px = 0.264583 mm
            $mm = (float) rtrim($val, 'px') * 0.264583;
            return max(0.0, min($totalMm - $elementMm, $mm));
        }
        if (is_numeric($val)) {
            $mm = (float) $val * 0.264583;
            return max(0.0, min($totalMm - $elementMm, $mm));
        }
        return $defaultMm;
    }
}
