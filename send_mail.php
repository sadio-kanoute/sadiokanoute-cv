<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = htmlspecialchars($_POST['nom']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $message = htmlspecialchars($_POST['message']);

    if ($email) {
        $to = "05kanoutesadioka@gmail.com"; 
        $subject = "Message de $nom via Portfolio";
        $headers = "From: $email\r\nReply-To: $email\r\n";
        $headers .= "Content-type: text/plain; charset=UTF-8\r\n";
        $content = "Nom : $nom\nEmail : $email\n\nMessage :\n$message";

        if (mail($to, $subject, $content, $headers)) {
            echo "Message envoyé avec succès.";
        } else {
            echo "Erreur d’envoi.";
        }
    } else {
        echo "Email invalide.";
    }
}
?>
