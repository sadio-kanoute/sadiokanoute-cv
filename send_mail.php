<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $message = htmlspecialchars($_POST['message']);
    // Prepare
    $to = "05kanoutesadioka@gmail.com";
    $subject = "Message de $nom via Portfolio";
    $headers = "From: $email\r\nReply-To: $email\r\n";
    $headers .= "Content-type: text/plain; charset=UTF-8\r\n";
    $content = "Nom : $nom\nEmail : $email\n\nMessage :\n$message";

    // Detect AJAX request (fetch/XHR)
    $isAjax = false;
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $isAjax = true;
    }

    // Dev mode: force success when ?dev=1 or header X-DEV:1 is present (local testing)
    $dev = false;
    if ((isset($_REQUEST['dev']) && $_REQUEST['dev'] === '1') || (isset($_SERVER['HTTP_X_DEV']) && $_SERVER['HTTP_X_DEV'] === '1')) {
        $dev = true;
    }

    if ($email) {
        $sent = false;
        if ($dev) {
            // simulate success in dev mode and save the message locally for inspection
            $sent = true;
            $logDir = __DIR__ . DIRECTORY_SEPARATOR . 'assets';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $logFile = $logDir . DIRECTORY_SEPARATOR . 'dev_emails.log';
            $entry = "---\n" . date('Y-m-d H:i:s') . "\nSubject: $subject\nHeaders: $headers\n\n$content\n";
            @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
        } else {
            $sent = mail($to, $subject, $content, $headers);
        }

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            if ($sent) {
                echo json_encode(['status' => 'success', 'text' => 'Message envoyé avec succès.']);
            } else {
                echo json_encode(['status' => 'error', 'text' => 'Erreur d\'envoi.']);
            }
            exit;
        }

        // Fallback for non-AJAX (iframe submit): return small HTML that posts to parent
        if ($sent) {
            echo "<!doctype html><html><head><meta charset=\"utf-8\"></head><body>Message envoyé avec succès.<script>if(window.parent){window.parent.postMessage({status:'success', text:'Message envoyé avec succès.'}, '*');}</script></body></html>";
        } else {
            echo "<!doctype html><html><head><meta charset=\"utf-8\"></head><body>Erreur d'envoi.<script>if(window.parent){window.parent.postMessage({status:'error', text:'Erreur d\'envoi.'}, '*');}</script></body></html>";
        }
    } else {
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'text' => 'Email invalide.']);
            exit;
        }
        echo "<!doctype html><html><head><meta charset=\"utf-8\"></head><body>Email invalide.<script>if(window.parent){window.parent.postMessage({status:'error', text:'Email invalide.'}, '*');}</script></body></html>";
    }
}
?>
