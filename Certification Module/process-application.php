<?php
require_once dirname(__DIR__) . '/includes/accommodation_form_helpers.php';

$errors = [];
$successId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = parseAccommodationFormPost($_POST);
    $errors = validateAccommodationApplication($data);

    if (empty($errors)) {
        $successId = saveAccommodationApplication($data);
        if ($successId) {
            header('Location: application-success.php?id=' . (int) $successId);
            exit;
        }
        $errors[] = 'Unable to save your application. Please try again.';
    }
}
