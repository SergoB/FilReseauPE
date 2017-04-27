<?php

    // destinataire
    $to  = 'aidan@example.com';
    // Sujet
    $subject = 'Mail de test';

    // message
    $message = '
      <html>
     <head>
      <title>Magnifique mail de test</title>
     </head>
     <body>
      <p>Cet incroyable mail a pour but de tester l\'envoi d\'un mail en html via php</p>
      <table>
      <tr>
        <td>tout</td>
        <td>va</td>
        <td>bien</td>
      </tr>

      <tr>
        <td>In</td>
        <td>Cro</td>
        <td>yable</td>
      </tr>
      </table>
      <p><b>en gras</b></p>
     </body>
    </html>
    ';

    // Pour envoyer un mail HTML, l'en-tête Content-type doit être défini
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    // En-têtes additionnels
    $headers .= 'To: Sergo <sergo.baltaci@gmail.com>'. "\r\n";
    $headers .= 'From: NoReply <admin@filreseau.com' . "\r\n";
    // Envoi
    $envoi=mail($to, $subject, $message, $headers);

if ($envoi) {
  echo "success";
}
else {
  echo "échec";
}
