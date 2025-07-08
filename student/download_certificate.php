<?php
session_start();
require_once '../config/database.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    die('You must be logged in as a student.');
}

$student_id = $_SESSION['student_id'];
$course_id = $_GET['course_id'] ?? null;

if (!$course_id) {
    die('Invalid course.');
}

// Get student and course info
$stmt = $pdo->prepare('SELECT u.full_name, c.title, c.id, e.progress FROM users u, courses c, enrollments e WHERE u.id = ? AND c.id = ? AND e.student_id = u.id AND e.course_id = c.id');
$stmt->execute([$student_id, $course_id]);
$info = $stmt->fetch();

if (!$info) {
    die('You are not enrolled in this course.');
}

if ($info['progress'] < 100) {
    die('You must complete the course to download the certificate.');
}

// Check for FPDF
if (!file_exists('../fpdf/fpdf.php')) {
    die('FPDF library not found. Please download FPDF from http://www.fpdf.org/ and place it in a folder named "fpdf" in the project root.');
}
require_once '../fpdf/fpdf.php';

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Draw border
$pdf->SetLineWidth(2);
$pdf->SetDrawColor(13, 110, 253);
$pdf->Rect(10, 10, 277, 190, 'D');

// Background color
$pdf->SetFillColor(240, 248, 255); // AliceBlue
$pdf->Rect(12, 12, 273, 186, 'F');

// Title
$pdf->SetFont('Arial', 'B', 36);
$pdf->SetTextColor(40, 40, 40);
$pdf->Cell(0, 40, 'Certificate of Completion', 0, 1, 'C');

// Subtitle
$pdf->SetFont('Arial', '', 18);
$pdf->SetTextColor(60, 60, 60);
$pdf->Cell(0, 15, 'This is to certify that', 0, 1, 'C');

// Student Name
$pdf->SetFont('Arial', 'B', 28);
$pdf->SetTextColor(13, 110, 253);
$pdf->Cell(0, 20, $info['full_name'], 0, 1, 'C');

// Completion text
$pdf->SetFont('Arial', '', 18);
$pdf->SetTextColor(60, 60, 60);
$pdf->Cell(0, 15, 'has successfully completed the course', 0, 1, 'C');

// Course Title
$pdf->SetFont('Arial', 'B', 22);
$pdf->SetTextColor(40, 167, 69);
$pdf->Cell(0, 18, $info['title'], 0, 1, 'C');

// Date
$pdf->SetFont('Arial', '', 16);
$pdf->SetTextColor(60, 60, 60);
$pdf->Ln(8);
$pdf->Cell(0, 12, 'Date: ' . date('F d, Y'), 0, 1, 'C');

// Signature line
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 14);
$pdf->SetTextColor(120, 120, 120);
$pdf->Cell(0, 10, '__________________________', 0, 1, 'R');
$pdf->Cell(0, 8, 'EduTech Pro Director', 0, 1, 'R');

// Footer
$pdf->SetY(-20);
$pdf->SetFont('Arial', 'I', 12);
$pdf->SetTextColor(180, 180, 180);
$pdf->Cell(0, 10, 'EduTech Pro - Premium E-Learning Platform', 0, 0, 'C');

$pdf->Output('D', 'Certificate_' . preg_replace('/[^a-zA-Z0-9]/', '_', $info['title']) . '.pdf'); 