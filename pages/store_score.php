<?php
session_start();
if(isset($_POST['score']) && isset($_POST['total'])) {
    $_SESSION['lastExamScore'] = $_POST['score'];
    $_SESSION['lastExamTotal'] = $_POST['total'];
    echo json_encode(['success' => true]);
}
?>