<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Empfänger-E-Mail
    $to = "laestheticcreations@gmail.com";

    // Formulardaten sicher abrufen
    $vorname = filter_var($_POST['Vorname'], FILTER_SANITIZE_STRING);
    $nachname = filter_var($_POST['Nachname'], FILTER_SANITIZE_STRING);
    $from_email = filter_var($_POST['Email'], FILTER_SANITIZE_EMAIL);
    $telefon = filter_var($_POST['Telefonnummer'], FILTER_SANITIZE_STRING);
    $bestellung = filter_var($_POST['Bestellte Pakete'], FILTER_SANITIZE_STRING);
    $nachricht = filter_var($_POST['Zusatzinformationen'], FILTER_SANITIZE_STRING);

    // E-Mail-Betreff
    $subject = "Neue Kontaktanfrage von $vorname $nachname";

    // Boundary für den E-Mail-Header
    $boundary = md5(time());

    // E-Mail-Header
    $headers = "From: $from_email\r\n";
    $headers .= "Reply-To: $from_email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    // E-Mail-Nachricht (Textteil)
    $message_body = "--$boundary\r\n";
    $message_body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $message_body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $message_body .= "Neue Anfrage von der Webseite:\n\n";
    $message_body .= "Name: $vorname $nachname\n";
    $message_body .= "Email: $from_email\n";
    $message_body .= "Telefon: $telefon\n\n";
    $message_body .= "Bestellte Pakete:\n$bestellung\n\n";
    $message_body .= "Zusatzinformationen:\n$nachricht\n";
    $message_body .= "\r\n";

    // Dateianhang verarbeiten
    if (isset($_FILES['Anhang']) && $_FILES['Anhang']['error'] == 0) {
        $file_tmp_name = $_FILES['Anhang']['tmp_name'];
        $file_name = $_FILES['Anhang']['name'];
        $file_size = $_FILES['Anhang']['size'];
        $file_type = $_FILES['Anhang']['type'];

        $file_content = chunk_split(base64_encode(file_get_contents($file_tmp_name)));

        $message_body .= "--$boundary\r\n";
        $message_body .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
        $message_body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
        $message_body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message_body .= $file_content . "\r\n";
    }

    $message_body .= "--$boundary--";

    // E-Mail versenden
    $mail_sent = mail($to, $subject, $message_body, $headers);

    // Weiterleitung nach dem Senden
    if ($mail_sent) {
        // Erstelle eine simple danke.html Seite
        header('Location: danke.html');
        exit();
    } else {
        // Erstelle eine simple fehler.html Seite
        header('Location: fehler.html');
        exit();
    }
} else {
    // Nicht direkt aufrufbar
    echo "Fehler: Direkter Zugriff nicht erlaubt.";
}
?>
