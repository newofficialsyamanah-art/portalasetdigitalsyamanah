<?php

namespace App\Services;

use App\Models\GuideBook;
use Illuminate\Support\Str;

class SimplePdfBuilder
{
    /**
     * Generate a professional PDF document for a GuideBook
     */
    public static function generateForGuide(GuideBook $guide): string
    {
        $dir = storage_path('app/guidebooks');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'guide_' . $guide->id . '_' . Str::slug($guide->title) . '.pdf';
        $fullPath = $dir . DIRECTORY_SEPARATOR . $filename;

        $builder = new self();
        $builder->setTitle($guide->title ?: 'Buku Panduan Sistem');
        $builder->setTargetRole($guide->target_role ?: 'Semua Karyawan (Umum)');
        $builder->setSystem($guide->system_name ?: 'Sistem Digital Syamanah');
        $builder->setFeatureName($guide->feature_name ?: '');
        $builder->setCategory($guide->category ?: 'SOP Operasional');
        $builder->setVersion($guide->version ?: 'v1.0');
        $builder->setAuthor($guide->author ?: 'Tim IT Syamanah');
        $builder->setSummary($guide->summary ?: '');
        $builder->setContent($guide->content ?: '');

        $pdfBinary = $builder->render();
        file_put_contents($fullPath, $pdfBinary);

        return $fullPath;
    }

    private string $title = 'Buku Panduan Sistem';
    private string $targetRole = 'Semua Karyawan (Umum)';
    private string $system = 'Sistem Digital Syamanah';
    private string $featureName = '';
    private string $category = 'SOP Operasional';
    private string $version = 'v1.0';
    private string $author = 'Tim IT Syamanah';
    private string $summary = '';
    private string $content = '';

    public function setTitle(?string $title): void { $this->title = $title ?: 'Buku Panduan Sistem'; }
    public function setTargetRole(?string $targetRole): void { $this->targetRole = $targetRole ?: 'Semua Karyawan (Umum)'; }
    public function setSystem(?string $system): void { $this->system = $system ?: 'Sistem Digital Syamanah'; }
    public function setFeatureName(?string $featureName): void { $this->featureName = $featureName ?: ''; }
    public function setCategory(?string $category): void { $this->category = $category ?: 'SOP Operasional'; }
    public function setVersion(?string $version): void { $this->version = $version ?: 'v1.0'; }
    public function setAuthor(?string $author): void { $this->author = $author ?: 'Tim IT Syamanah'; }
    public function setSummary(?string $summary): void { $this->summary = $summary ?: ''; }
    public function setContent(?string $content): void { $this->content = $content ?: ''; }

    /**
     * Build the raw PDF 1.4 binary
     */
    public function render(): string
    {
        $pages = [];
        $currentPageOps = [];
        $y = 780; // Starting Y coordinate (A4 height is 842 pt)

        // Helper to add operations and handle pagination
        $checkNewPage = function(int $neededSpace = 30) use (&$y, &$currentPageOps, &$pages) {
            if ($y - $neededSpace < 65) {
                $pages[] = $currentPageOps;
                $currentPageOps = [];
                $y = 780;
                return true;
            }
            return false;
        };

        // --- PAGE 1 HEADER ---
        // Header band
        $currentPageOps[] = "0.95 0.96 0.98 rg 40 760 515 55 re f"; // light background box
        $currentPageOps[] = "0.23 0.49 0.54 RG 2 w 40 760 515 55 re S"; // teal border
        
        // Brand & Subtitle
        $currentPageOps[] = "BT /F2 13 Tf 0.06 0.11 0.18 rg 55 794 Td (CV GENERASI SUGIH SEJAHTERA - SYAMANAH) Tj ET";
        $currentPageOps[] = "BT /F1 9 Tf 0.23 0.49 0.54 rg 55 778 Td (ENTERPRISE ASSET MANAGEMENT | STANDAR OPERASIONAL PROSEDUR) Tj ET";
        $currentPageOps[] = "BT /F2 8 Tf 0.85 0.45 0.05 rg 440 794 Td (CONFIDENTIAL) Tj ET";
        $currentPageOps[] = "BT /F1 8 Tf 0.4 0.4 0.4 rg 440 778 Td (BUSINESS ANALYST) Tj ET";

        $y = 735;

        // Title
        $wrappedTitle = $this->wrapText($this->title, 55);
        foreach ($wrappedTitle as $tLine) {
            $checkNewPage(24);
            $currentPageOps[] = "BT /F2 15 Tf 0.06 0.11 0.18 rg 40 {$y} Td (" . $this->escapeText($tLine) . ") Tj ET";
            $y -= 20;
        }

        // Metadata box (Expanded to fit Target Role and Feature Name)
        $y -= 5;
        $metaY = $y;
        $currentPageOps[] = "0.98 0.98 0.98 rg 40 " . ($metaY - 46) . " 515 48 re f";
        $currentPageOps[] = "0.85 0.88 0.91 RG 0.5 w 40 " . ($metaY - 46) . " 515 48 re S";

        // Row 1: Target Role & Sistem Terkait
        $currentPageOps[] = "BT /F2 8 Tf 0.25 0.35 0.45 rg 50 " . ($metaY - 12) . " Td (TARGET PERAN / AUDIENCE:) Tj ET";
        $currentPageOps[] = "BT /F1 8 Tf 0.08 0.12 0.2 rg 175 " . ($metaY - 12) . " Td (" . $this->escapeText($this->targetRole) . ") Tj ET";

        $currentPageOps[] = "BT /F2 8 Tf 0.25 0.35 0.45 rg 345 " . ($metaY - 12) . " Td (SISTEM:) Tj ET";
        $currentPageOps[] = "BT /F1 8 Tf 0.08 0.12 0.2 rg 395 " . ($metaY - 12) . " Td (" . $this->escapeText($this->system) . ") Tj ET";

        // Row 2: Fitur yang Dijelaskan & Versi / Author
        $featureDisplay = $this->featureName ?: $this->category;
        $currentPageOps[] = "BT /F2 8 Tf 0.25 0.35 0.45 rg 50 " . ($metaY - 26) . " Td (FITUR SPESIFIK:) Tj ET";
        $currentPageOps[] = "BT /F1 8 Tf 0.08 0.12 0.2 rg 135 " . ($metaY - 26) . " Td (" . $this->escapeText($featureDisplay) . ") Tj ET";

        $currentPageOps[] = "BT /F2 8 Tf 0.25 0.35 0.45 rg 345 " . ($metaY - 26) . " Td (VERSI / EDISI:) Tj ET";
        $currentPageOps[] = "BT /F1 8 Tf 0.08 0.12 0.2 rg 415 " . ($metaY - 26) . " Td (" . $this->escapeText($this->version) . ") Tj ET";

        // Row 3: Penyusun & Tanggal Dokumen
        $currentPageOps[] = "BT /F2 8 Tf 0.25 0.35 0.45 rg 50 " . ($metaY - 40) . " Td (PENYUSUN DOKUMEN:) Tj ET";
        $currentPageOps[] = "BT /F1 8 Tf 0.08 0.12 0.2 rg 155 " . ($metaY - 40) . " Td (" . $this->escapeText($this->author) . ") Tj ET";

        $currentPageOps[] = "BT /F2 8 Tf 0.25 0.35 0.45 rg 345 " . ($metaY - 40) . " Td (TANGGAL BERLAKU:) Tj ET";
        $currentPageOps[] = "BT /F1 8 Tf 0.08 0.12 0.2 rg 430 " . ($metaY - 40) . " Td (" . date('d F Y') . ") Tj ET";

        $y -= 60;

        // Summary Callout Box
        if (!empty($this->summary)) {
            $summaryLines = $this->wrapText($this->summary, 82);
            $sumHeight = (count($summaryLines) * 14) + 18;
            $checkNewPage($sumHeight);

            $currentPageOps[] = "0.94 0.97 0.98 rg 40 " . ($y - $sumHeight + 14) . " 515 {$sumHeight} re f";
            $currentPageOps[] = "0.23 0.49 0.54 RG 3 w 40 " . ($y - $sumHeight + 14) . " 0 {$sumHeight} re S"; // Left thick accent line

            $currentPageOps[] = "BT /F2 9 Tf 0.15 0.35 0.4 rg 52 " . ($y - 2) . " Td (RINGKASAN EKSEKUTIF / TUJUAN SOP:) Tj ET";
            $y -= 16;

            foreach ($summaryLines as $sLine) {
                $currentPageOps[] = "BT /F1 9 Tf 0.2 0.25 0.3 rg 52 {$y} Td (" . $this->escapeText($sLine) . ") Tj ET";
                $y -= 13;
            }
            $y -= 12;
        }

        // Horizontal line separator
        $checkNewPage(15);
        $currentPageOps[] = "0.85 0.88 0.9 RG 0.5 w 40 {$y} m 555 {$y} l S";
        $y -= 15;

        // Content Parsing Line-by-Line
        $rawLines = explode("\n", $this->content);
        $inCodeBlock = false;
        $codeLines = [];

        foreach ($rawLines as $rawLine) {
            $trimmed = trim($rawLine);

            // Handle Code blocks ```
            if (str_starts_with($trimmed, '```')) {
                if ($inCodeBlock) {
                    // Flush code block
                    $codeHeight = (count($codeLines) * 12) + 14;
                    $checkNewPage($codeHeight);

                    $currentPageOps[] = "0.12 0.14 0.18 rg 40 " . ($y - $codeHeight + 10) . " 515 {$codeHeight} re f";
                    $currentPageOps[] = "0.2 0.25 0.3 RG 0.5 w 40 " . ($y - $codeHeight + 10) . " 515 {$codeHeight} re S";

                    $cy = $y - 2;
                    foreach ($codeLines as $cl) {
                        $currentPageOps[] = "BT /F4 8 Tf 0.9 0.95 1.0 rg 50 {$cy} Td (" . $this->escapeText($cl) . ") Tj ET";
                        $cy -= 12;
                    }

                    $y -= ($codeHeight + 6);
                    $codeLines = [];
                    $inCodeBlock = false;
                } else {
                    $inCodeBlock = true;
                    $codeLines = [];
                }
                continue;
            }

            if ($inCodeBlock) {
                $codeLines[] = substr($rawLine, 0, 95);
                continue;
            }

            if (empty($trimmed)) {
                $y -= 7;
                continue;
            }

            // Headings
            if (str_starts_with($trimmed, '# ')) {
                $checkNewPage(30);
                $headingText = trim(substr($trimmed, 2));
                $currentPageOps[] = "BT /F2 13 Tf 0.06 0.11 0.18 rg 40 {$y} Td (" . $this->escapeText($headingText) . ") Tj ET";
                $y -= 4;
                $currentPageOps[] = "0.23 0.49 0.54 RG 1 w 40 {$y} m 555 {$y} l S";
                $y -= 16;
            } elseif (str_starts_with($trimmed, '## ')) {
                $checkNewPage(24);
                $headingText = trim(substr($trimmed, 3));
                $currentPageOps[] = "BT /F2 11 Tf 0.15 0.3 0.4 rg 40 {$y} Td (" . $this->escapeText($headingText) . ") Tj ET";
                $y -= 15;
            } elseif (str_starts_with($trimmed, '### ')) {
                $checkNewPage(20);
                $headingText = trim(substr($trimmed, 4));
                $currentPageOps[] = "BT /F2 10 Tf 0.2 0.35 0.4 rg 40 {$y} Td (" . $this->escapeText($headingText) . ") Tj ET";
                $y -= 14;
            } elseif (str_starts_with($trimmed, '> [!NOTE]') || str_starts_with($trimmed, '> [!TIP]') || str_starts_with($trimmed, '> [!WARNING]')) {
                $checkNewPage(24);
                $alertText = trim(preg_replace('/>\s*\[!(NOTE|TIP|WARNING)\]\s*/i', '', $trimmed));
                $currentPageOps[] = "0.98 0.95 0.88 rg 40 " . ($y - 12) . " 515 20 re f";
                $currentPageOps[] = "0.85 0.5 0.1 RG 2 w 40 " . ($y - 12) . " 0 20 re S";
                $currentPageOps[] = "BT /F2 8.5 Tf 0.6 0.3 0.0 rg 48 " . ($y - 2) . " Td (PERHATIAN: " . $this->escapeText($alertText) . ") Tj ET";
                $y -= 24;
            } elseif (str_starts_with($trimmed, '- ') || str_starts_with($trimmed, '* ')) {
                $itemText = trim(substr($trimmed, 2));
                $wrapped = $this->wrapText($itemText, 78);
                foreach ($wrapped as $idx => $wLine) {
                    $checkNewPage(13);
                    if ($idx === 0) {
                        $currentPageOps[] = "BT /F2 9 Tf 0.23 0.49 0.54 rg 48 {$y} Td (\x95) Tj ET"; // Bullet symbol
                        $currentPageOps[] = "BT /F1 9 Tf 0.15 0.15 0.15 rg 58 {$y} Td (" . $this->escapeText($wLine) . ") Tj ET";
                    } else {
                        $currentPageOps[] = "BT /F1 9 Tf 0.15 0.15 0.15 rg 58 {$y} Td (" . $this->escapeText($wLine) . ") Tj ET";
                    }
                    $y -= 12;
                }
            } elseif (preg_match('/^([0-9]+)\.\s+(.*)$/', $trimmed, $numMatch)) {
                $num = $numMatch[1];
                $itemText = $numMatch[2];
                $wrapped = $this->wrapText($itemText, 78);
                foreach ($wrapped as $idx => $wLine) {
                    $checkNewPage(13);
                    if ($idx === 0) {
                        $currentPageOps[] = "BT /F2 9 Tf 0.06 0.11 0.18 rg 45 {$y} Td ({$num}.) Tj ET";
                        $currentPageOps[] = "BT /F1 9 Tf 0.15 0.15 0.15 rg 60 {$y} Td (" . $this->escapeText($wLine) . ") Tj ET";
                    } else {
                        $currentPageOps[] = "BT /F1 9 Tf 0.15 0.15 0.15 rg 60 {$y} Td (" . $this->escapeText($wLine) . ") Tj ET";
                    }
                    $y -= 12;
                }
            } else {
                // Normal paragraph line
                $wrapped = $this->wrapText($trimmed, 82);
                foreach ($wrapped as $wLine) {
                    $checkNewPage(13);
                    $currentPageOps[] = "BT /F1 9 Tf 0.2 0.2 0.2 rg 40 {$y} Td (" . $this->escapeText($wLine) . ") Tj ET";
                    $y -= 12;
                }
            }
        }

        // Add last page
        if (!empty($currentPageOps)) {
            $pages[] = $currentPageOps;
        }

        // Ensure at least 1 page
        if (empty($pages)) {
            $pages[] = $currentPageOps;
        }

        // --- ASSEMBLE PDF OBJECTS ---
        $objects = [];
        $objIndex = 1;

        $catalogId = $objIndex++;
        $pagesId = $objIndex++;
        $fontF1Id = $objIndex++;
        $fontF2Id = $objIndex++;
        $fontF3Id = $objIndex++;
        $fontF4Id = $objIndex++;

        $pageIds = [];
        $contentIds = [];

        $totalPages = count($pages);
        for ($p = 0; $p < $totalPages; $p++) {
            $pageIds[] = $objIndex++;
            $contentIds[] = $objIndex++;
        }

        // Font 1: Regular Helvetica
        $objects[$fontF1Id] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>";
        // Font 2: Bold Helvetica
        $objects[$fontF2Id] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>";
        // Font 3: Italic Helvetica
        $objects[$fontF3Id] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Oblique /Encoding /WinAnsiEncoding >>";
        // Font 4: Courier Monospace
        $objects[$fontF4Id] = "<< /Type /Font /Subtype /Type1 /BaseFont /Courier /Encoding /WinAnsiEncoding >>";

        // Pages root
        $kidsStr = implode(' 0 R ', $pageIds) . ' 0 R';
        $objects[$pagesId] = "<< /Type /Pages /Kids [ {$kidsStr} ] /Count {$totalPages} >>";

        // Catalog
        $objects[$catalogId] = "<< /Type /Catalog /Pages {$pagesId} 0 R >>";

        // Generate each Page and Content Stream
        for ($p = 0; $p < $totalPages; $p++) {
            $pageNum = $p + 1;
            $streamOps = $pages[$p];

            // Add standard footer to each page
            $streamOps[] = "0.85 0.88 0.9 RG 0.5 w 40 45 m 555 45 l S";
            $streamOps[] = "BT /F3 7.5 Tf 0.5 0.5 0.5 rg 40 32 Td (CV Generasi Sugih Sejahtera - CONFIDENTIAL | Kewenangan Penuh Tim Business Analyst) Tj ET";
            $streamOps[] = "BT /F2 7.5 Tf 0.3 0.3 0.3 rg 495 32 Td (Hal. {$pageNum} / {$totalPages}) Tj ET";

            $streamContent = implode("\n", $streamOps);
            $streamLen = strlen($streamContent);

            $objects[$contentIds[$p]] = "<< /Length {$streamLen} >>\nstream\n{$streamContent}\nendstream";
            $objects[$pageIds[$p]] = "<< /Type /Page /Parent {$pagesId} 0 R /MediaBox [ 0 0 595.28 841.89 ] /Contents {$contentIds[$p]} 0 R /Resources << /Font << /F1 {$fontF1Id} 0 R /F2 {$fontF2Id} 0 R /F3 {$fontF3Id} 0 R /F4 {$fontF4Id} 0 R >> >> >>";
        }

        // Build Final Binary File with xref table
        $out = "%PDF-1.4\n%\xe2\xe3\xcf\xd3\n";
        $xref = [];
        $xref[0] = "0000000000 65535 f \n";

        for ($i = 1; $i < $objIndex; $i++) {
            $offset = strlen($out);
            $xref[$i] = sprintf("%010d 00000 n \n", $offset);
            $out .= "{$i} 0 obj\n" . $objects[$i] . "\nendobj\n";
        }

        $xrefStart = strlen($out);
        $out .= "xref\n0 {$objIndex}\n";
        for ($i = 0; $i < $objIndex; $i++) {
            $out .= $xref[$i];
        }

        $out .= "trailer\n<< /Size {$objIndex} /Root {$catalogId} 0 R >>\nstartxref\n{$xrefStart}\n%%EOF";

        return $out;
    }

    /**
     * Wrap text into multiple lines by character count
     */
    private function wrapText(string $text, int $maxChars = 80): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $current = '';

        foreach ($words as $w) {
            if (strlen($current . ' ' . $w) <= $maxChars) {
                $current = empty($current) ? $w : ($current . ' ' . $w);
            } else {
                if (!empty($current)) $lines[] = $current;
                $current = $w;
            }
        }
        if (!empty($current)) $lines[] = $current;

        return empty($lines) ? [''] : $lines;
    }

    /**
     * Escape special PDF string characters (parentheses and backslashes)
     */
    private function escapeText(string $str): string
    {
        $clean = preg_replace('/[^\x20-\x7E\xA0-\xFF]/', ' ', $str); // ASCII printable & latin-1
        $clean = str_replace('\\', '\\\\', $clean);
        $clean = str_replace('(', '\\(', $clean);
        $clean = str_replace(')', '\\)', $clean);
        return $clean;
    }
}
