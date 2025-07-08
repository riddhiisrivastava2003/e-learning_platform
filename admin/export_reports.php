<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

// Helper to write CSV
function array_to_csv($data, $header = true) {
    $fh = fopen('php://temp', 'rw');
    if ($header && !empty($data)) {
        fputcsv($fh, array_keys($data[0]));
    }
    foreach ($data as $row) {
        fputcsv($fh, $row);
    }
    rewind($fh);
    $csv = stream_get_contents($fh);
    fclose($fh);
    return $csv;
}

// Fetch data
$students = $pdo->query("SELECT * FROM users WHERE role = 'student'")->fetchAll(PDO::FETCH_ASSOC);
$teachers = $pdo->query("SELECT * FROM users WHERE role = 'teacher'")->fetchAll(PDO::FETCH_ASSOC);
$courses = $pdo->query("SELECT * FROM courses ORDER BY id ASC LIMIT 150")->fetchAll(PDO::FETCH_ASSOC);
$enrollments = $pdo->query("SELECT * FROM enrollments")->fetchAll(PDO::FETCH_ASSOC);
$payments = $pdo->query("SELECT * FROM payments")->fetchAll(PDO::FETCH_ASSOC);
$attendance = $pdo->query("SELECT * FROM attendance")->fetchAll(PDO::FETCH_ASSOC);

// Create temp files
$tmpdir = sys_get_temp_dir() . '/elms_export_' . uniqid();
mkdir($tmpdir);
file_put_contents("$tmpdir/students.csv", array_to_csv($students));
file_put_contents("$tmpdir/teachers.csv", array_to_csv($teachers));
file_put_contents("$tmpdir/courses.csv", array_to_csv($courses));
file_put_contents("$tmpdir/enrollments.csv", array_to_csv($enrollments));
file_put_contents("$tmpdir/payments.csv", array_to_csv($payments));
file_put_contents("$tmpdir/attendance.csv", array_to_csv($attendance));
file_put_contents("$tmpdir/summary.json", json_encode([
    'students' => $students,
    'teachers' => $teachers,
    'courses' => $courses,
    'enrollments' => $enrollments,
    'payments' => $payments,
    'attendance' => $attendance
], JSON_PRETTY_PRINT));

// Zip files
$zipfile = tempnam(sys_get_temp_dir(), 'elms_zip_') . '.zip';
$zip = new ZipArchive();
$zip->open($zipfile, ZipArchive::CREATE);
foreach (['students.csv','teachers.csv','courses.csv','enrollments.csv','payments.csv','attendance.csv','summary.json'] as $file) {
    $zip->addFile("$tmpdir/$file", $file);
}
$zip->close();

// Output zip
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="elms_reports_export.zip"');
header('Content-Length: ' . filesize($zipfile));
readfile($zipfile);

// Cleanup
foreach (['students.csv','teachers.csv','courses.csv','enrollments.csv','payments.csv','attendance.csv','summary.json'] as $file) {
    @unlink("$tmpdir/$file");
}
@rmdir($tmpdir);
@unlink($zipfile);

<div class="text-center mb-4">
    <i class="fas fa-user-shield fa-2x text-primary mb-2"></i>
    <i class="fas fa-graduation-cap fa-2x text-primary"></i>
    <h5 class="text-white mt-2">EduTech Pro</h5>
    <small class="text-muted">Admin Panel</small>
</div>
exit; 