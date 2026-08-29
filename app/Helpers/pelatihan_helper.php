<?php

if (!function_exists('get_system_logo')) {
    function get_system_logo() {
        try {
            $db = \Config\Database::connect();
            $row = $db->table('pengaturan_logo_sistem_pelatihan')->where('id', 1)->get()->getRowArray();
            if ($row && !empty($row['logo_path'])) {
                // If it is stored as assets/... return base_url, otherwise if uploaded under uploads return base_url(uploads/...)
                if (strpos($row['logo_path'], 'assets/') === 0) {
                    return base_url($row['logo_path']);
                }
                return base_url('uploads/pelatihan/' . $row['logo_path']);
            }
        } catch (\Exception $e) {
            // fallback
        }
        return base_url('assets/img/logo_rs.jpg');
    }
}

if (!function_exists('get_system_favicon')) {
    function get_system_favicon() {
        try {
            $db = \Config\Database::connect();
            $row = $db->table('pengaturan_logo_sistem_pelatihan')->where('id', 1)->get()->getRowArray();
            if ($row && !empty($row['favicon_path'])) {
                if (strpos($row['favicon_path'], 'assets/') === 0) {
                    return base_url($row['favicon_path']);
                }
                return base_url('uploads/pelatihan/' . $row['favicon_path']);
            }
        } catch (\Exception $e) {
            // fallback
        }
        return base_url('assets/img/logo_rs.jpg');
    }
}

if (!function_exists('tanggal_indo')) {
    function tanggal_indo(?string $tanggal, bool $cetak_hari = false): string
    {
        if (empty($tanggal) || $tanggal === '0000-00-00' || $tanggal === '0000-00-00 00:00:00') {
            return '-';
        }
        
        $hari = [
            1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'
        ];
        
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $ts = strtotime($tanggal);
        $tgl = date('d', $ts);
        $bln = (int)date('m', $ts);
        $thn = date('Y', $ts);
        
        $str = $tgl . ' ' . $bulan[$bln] . ' ' . $thn;
        
        if ($cetak_hari) {
            $num = date('N', $ts);
            return $hari[$num] . ', ' . $str;
        }
        return $str;
    }
}

if (!function_exists("renderPelatihanFilePreview")) {
    function renderPelatihanFilePreview($filePath, $title = "", $fallbackUrl = "") {
        $filePath = $filePath ?: "";
        $title = $title ?: basename($filePath);
        $fileUrl = $fallbackUrl ?: base_url($filePath);
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($ext, ["jpg", "jpeg", "png", "gif", "webp"], true)) {
            return "<div class=\"text-center\"><img src=\"" . esc($fileUrl, "attr") . "\" class=\"img-fluid rounded shadow\" style=\"max-height: 600px;\" alt=\"" . esc($title, "attr") . "\"></div>";
        }

        if (in_array($ext, ["mp4", "webm", "ogg"], true)) {
            $mime = $ext === "mp4" ? "video/mp4" : ($ext === "webm" ? "video/webm" : "video/ogg");
            return "<video controls class=\"w-100 rounded shadow\" style=\"max-height: 500px;\"><source src=\"" . esc($fileUrl, "attr") . "\" type=\"" . esc($mime, "attr") . "\">Browser Anda tidak mendukung pemutaran video.</video>";
        }

        if ($ext === "pdf") {
            return "<div class=\"document-preview-shell\"><div class=\"document-preview-head\"><span>" . esc($title) . "</span></div><iframe src=\"" . esc($fileUrl, "attr") . "\" class=\"inline-document-preview\" title=\"" . esc($title, "attr") . "\"></iframe></div>";
        }

        if (in_array($ext, ["doc", "docx", "xls", "xlsx", "ppt", "pptx"], true)) {
            $label = in_array($ext, ["xls", "xlsx"], true) ? "Excel" : (in_array($ext, ["ppt", "pptx"], true) ? "PowerPoint" : "Word");
            return "<div class=\"document-preview-shell\"><div class=\"document-preview-head\"><span>" . esc($title) . "</span></div><div class=\"p-4 text-center bg-light\" style=\"min-height: 220px;\"><i class=\"fas fa-file-alt fa-3x text-muted mb-3\"></i><h6 class=\"fw-bold text-dark\">Preview " . esc($label) . " tidak tersedia di halaman ini</h6><p class=\"text-muted small mb-0\">File materi ini tidak dapat dibuka langsung dari ruang belajar.</p></div></div>";
        }

        if (in_array($ext, ["txt", "csv"], true)) {
            return "<div class=\"document-preview-shell\"><div class=\"document-preview-head\"><span>" . esc($title) . "</span></div><div class=\"p-4 text-center bg-light\" style=\"min-height: 220px;\"><i class=\"fas fa-file-alt fa-3x text-muted mb-3\"></i><h6 class=\"fw-bold text-dark\">Preview teks tidak tersedia</h6><p class=\"text-muted small mb-0\">File materi ini tidak dapat dibuka langsung dari ruang belajar.</p></div></div>";
        }

        return "<div class=\"py-4 text-center\"><i class=\"fas fa-file-alt fa-4x text-muted mb-3\"></i><h6 class=\"text-dark fw-bold\">File ini tidak punya preview bawaan browser.</h6><div class=\"text-secondary fw-bold\">" . esc($title) . "</div></div>";
    }
}

