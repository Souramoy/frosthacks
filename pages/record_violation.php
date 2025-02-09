<?php
// record_violation.php
session_start();
require_once './conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $examId = $data['examId'];
    $violationType = $data['type'];
    $timestamp = $data['timestamp'];
    $studentId = $_SESSION['student_id']; // Assuming you store student ID in session
    
    // Record violation in database
    $stmt = $conn->prepare("INSERT INTO exam_violations (exam_id, student_id, violation_type, timestamp) VALUES (?, ?, ?, ?)");
    $stmt->execute([$examId, $studentId, $violationType, $timestamp]);
    
    // Get violation count for this exam
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM exam_violations WHERE exam_id = ? AND student_id = ?");
    $stmt->execute([$examId, $studentId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If violations exceed threshold, mark exam for review
    if ($result['count'] >= 10) {
        $stmt = $conn->prepare("UPDATE exam_attempts SET needs_review = 1 WHERE exam_id = ? AND student_id = ?");
        $stmt->execute([$examId, $studentId]);
    }
    
    echo json_encode(['status' => 'success']);
}
?>