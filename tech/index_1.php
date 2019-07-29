
<?php

require_once 'connection.php';
include ('var_mail.php');
require ('class/PHPMailerAutoload.php');


    $bodytext = '<p><img src="https://ci3.googleusercontent.com/proxy/DonhtvV4BBeYXgq0f1x-7dGGL0PGaC1v0OrlE68vsPrI-zcGTmNjH9cN-WWvD_aYfaqzmQZyjyQspC1gTKBum2I7NebfxF2k0yih4elZ-VkxsglppogjQfARP-MmULSW64ItXOus3xfOaLhhmBPdTCgPt1Dx-fbLx619j2nZQo5CApKW0x6HoxMAVSHNPlMMJxE=s0-d-e1-ft#http://www.beniculturali.it/mibac/export/system/modules/it.inera.opencms.templates/MiBAC/images/layout/header/logoMIBACT.jpg" class="CToWUd"></p>
<p>Gentilissima,<br>
<h3>!--email@user_ok_telefonata--!</h3>
<P>Ai seguenti recapiti:</P>
<h3>!--email@user_message_recapiti--!</h3>
<p>Cordiali saluti,<br>
Lo staff di selezione</p>

<hr>
Si ricorda che questa è una mail di solo invio e
che il contenuto è strettamente confidenziale ed
affidato alla cura del destinatario.
';


    $mail = new PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->IsSMTP();
    $mail->Host = "10.20.20.227";
    $mail->SMTPAutoTLS = true;
    $mail->SMTPAuth = false;
    $mail->From = "src.concorsi@mibac.it";
    $mail->FromName = "Ufficio Concorso Ministero per i Beni culturali";
    $mail->headerLine("Content-Type: text/html; charset=UTF-8");
    $mail->AddAddress('ilia.virzi@gmail.com', 'IV');
    //include 'email_notif.php';
    $mail->AddReplyTo("src.concorsi@mibac.it", "SRC Concorsi");
    $mail->WordWrap = 50;
    $mail->IsHTML(true);
    $mail->Subject = 'Posizione II/OOEP001#2017 - a seguire';
    $mail->Body = $bodytext;
    $mail->AltBody = $corpodeltestotxt;
    $mail->send();










