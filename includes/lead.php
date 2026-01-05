<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/site.php';

function handle_lead_submit(): void {
  header('Content-Type: application/json; charset=utf-8');
  try {
    $full_name = trim((string)($_POST['full_name'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $package = trim((string)($_POST['package_name'] ?? ''));
    $trip_date = trim((string)($_POST['trip_date'] ?? ''));
    $persons = trim((string)($_POST['persons'] ?? ''));
    $contact_pref = trim((string)($_POST['contact_pref'] ?? 'phone'));
    $message = trim((string)($_POST['message'] ?? ''));

    if ($full_name === '' || $phone === '') {
      http_response_code(422);
      echo json_encode(['ok'=>false,'message'=>'Name and phone are required.']);
      return;
    }

    q("INSERT INTO leads(full_name, phone, email, package_name, trip_date, persons, contact_pref, message, status, created_at)
       VALUES(?,?,?,?,?,?,?,?,?,?)",
      [$full_name,$phone,$email,$package,$trip_date,$persons,$contact_pref,$message,'new',date('Y-m-d H:i:s')]
    );
    $id = (int)db()->lastInsertId();

    $webhook = setting('lead_webhook_url','');
    if ($webhook) {
      post_webhook($webhook, [
        'id'=>$id,
        'full_name'=>$full_name,
        'phone'=>$phone,
        'email'=>$email,
        'package_name'=>$package,
        'trip_date'=>$trip_date,
        'persons'=>$persons,
        'contact_pref'=>$contact_pref,
        'message'=>$message,
        'created_at'=>date('c'),
      ]);
    }

    echo json_encode(['ok'=>true,'message'=>'Received. We will contact you shortly.', 'id'=>$id]);
  } catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'message'=>'Server error.']);
  }
}
