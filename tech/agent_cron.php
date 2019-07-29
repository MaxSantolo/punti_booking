<?php

include 'agent.php';
include 'connection.php';
include ('variables.php');
include ('var_mail.php');
require ('class/PHPMailerAutoload.php');

////funzione aggiorna id booking iscritti

$contr_aggiornare = $conn_prod_punti->query("SELECT * FROM anagrafica_punti WHERE id_cliente_booking = '' and email != ''");


//echo "SELECT * FROM anagrafica_punti WHERE id_cliente_booking = '' and email != ''";
//echo '<BR>';

if ($contr_aggiornare->num_rows>0) {

    while ($contratto_agg = $contr_aggiornare->fetch_assoc()) {

        $dati_booking = $conn_siteground->query($query_booking_email . " and user_email = '".$contratto_agg['email']."'");
//        echo $query_booking_email . " and user_email = '".$contratto_agg['email']."'";
//        echo '<br>';
        

        if ($dati_booking->num_rows>0) { 
            
            
            while ($dati_booking_array = $dati_booking->fetch_assoc()) {
            
            $conn_prod_punti->query("UPDATE anagrafica_punti SET id_cliente_booking = '".$dati_booking_array['user_id']."' WHERE id = '".$contratto_agg['id']."'"); 
            
//            echo "UPDATE anagrafica_punti SET id_cliente_booking = '".$dati_booking_array['user_id']."' WHERE id = '".$contratto_agg['id'];
//            echo '<BR>';
            
        }
         
    }

}
}
//fai scadere accrediti non usufruiti

$accrediti_scaduti = $conn_prod_punti->query("UPDATE accrediti SET accreditato = 'Scaduto' WHERE scadenza < curdate() AND accreditato != 'Accreditato'"); 


//manda reminder a chi non si e' registrato

$reminder = $conn_prod_punti->query("SELECT * FROM date_reminder WHERE data_reminder = curdate() and id_cliente_booking IS NULL");

if ($reminder->num_rows>0) {

    while ($reminder_data = $reminder->fetch_assoc()) {

        $reminder_body = $header_email_standard . $testo_mail_reminder . $footer_email_standard;
        $destinatario = $reminder_data['email'];
        $nome_destinatario = $reminder_data['nome'];

        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->IsSMTP();
        $mail->Host = "10.20.20.227";
        $mail->SMTPAuth = false;
        $mail->From = "info@pickcenter.com";
        $mail->FromName = "Pick Center - Sistema a punti";
        $mail->headerLine("Content-Type: text/html; charset=UTF-8");
        $mail->AddAddress($destinatario,$nome_destinatario);
        include 'email_notif.php';
        $mail->AddReplyTo("info@pickcenter.com", "Informazioni");
        $mail->WordWrap = 50;
        $mail->IsHTML(true);
        $mail->Subject = $oggetto_mail_reminder. ' - '. $nome_destinatario;
        $mail->Body    = $reminder_body;
        $mail->AltBody = $corpodeltestotxt;
        $mail->send();

   }

}

//accredita punti giornalieri

$accrediti_todo = $conn_prod_punti->query("SELECT *, anagrafica_punti.id as id_contratto, accrediti.id as id_accredito_todo FROM anagrafica_punti, accrediti WHERE id_cliente_booking !='' and data_accredito <= curdate() and anagrafica_punti.id = accrediti.id_cliente and accreditato = 'In attesa' and data_scadenza >=curdate()"); //cerco gli accrediti papabili

if ($accrediti_todo->num_rows>0) { //se ci sono

    while ($accredito = $accrediti_todo->fetch_assoc()) { //fin ne trovo


        $punti_dare = $accredito['punti'] + $accredito['bonus_punti'];

        $id_todo = $accredito['id_cliente_booking'];
        $accredito_todo = $accredito['id_accredito_todo'];
        $stato_todo = $accredito['accreditato'];

        aggiungi_punti($punti_dare, $id_todo, $accredito_todo, $stato_todo);

        $destinatario = $accredito['email'];
        $nome_destinatario = $accredito['nome'];

        if ($accredito['bonus_punti'] == 0) {
            $bodytext = $header_email_standard . $testo_mail_accredito . $footer_email_standard;
            $oggetto = $oggetto_mail_accredito;
        } else {
            $bodytext = $header_email_standard . $testo_mail_bonus . $footer_email_standard;
            $oggetto = $oggetto_mail_bonus;
        }

        //mail personalizzata compleanno
        if ($accredito['causale_bonus'] == "Compleanno" || $accredito['causale_bonus'] == "compleanno") {

            $oggetto = $nome_destinatario . " " . $oggetto_mail_compleanno;
            $bodytext = $header_email_standard . "<table style=\"width: 100%; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 100%; color: #000; border-collapse: collapse; padding: 0; margin: 0;text-align: center;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                        <tbody><tr><td>
                        <p><span style=\"color: #ff9900; font-weight: bold;\">". $nome_destinatario . " oggi è il tuo compleanno!</span></p>
                        Desideriamo festeggiarlo con te, regalandoti qualcosa di veramente speciale:<br>
                        <p><span style=\"color: #ff9900; font-weight: bold;\">" . $punti_dare . " PUNTI</span></p>
                        che potrai utilizzare sul nostro sistema di <a href=\"https://www.pickcenter.it/booking\" target = \"_blank\" style=\"color:#ff9900;font-weight:bold;\">prenotazione on line</A> per prenotare gratuitamente Day Office e Sale riunioni.
                        <br>
                        <p><span style=\"color: #ff9900; font-weight: bold;\">Tanti Auguri!</span></p>
                        <img src=\"https://www.pickcenter.it/quotesimgs/happy-birthday.png\">
                        <table style=\"width: 100%; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 100%; color: #000; border-collapse: collapse; padding: 0; margin: 0;\" border=\"0\" cellspacing=\"5\" cellpadding=\"5\">
                        <tbody><tr><td style=\"height: 1px; font-size: 1px;border-top: 1;border-top-color:#ff9900;\" colspan=\"2\"></td></tr><tr><td style=\"text-align: left;\"></td><td style=\"text-align: right;\">
                        <p><span style=\"color: #ff9900; font-weight: bold;\">PICK CENTER</span><br /> Lo Staff</p></td></tr><tr><td style=\"height: 10px; font-size: 1px;\" colspan=\"2\" height=\"10\"></td></tr></tbody></table>"
                        . $footer_email_standard;


        }


        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->IsSMTP();
        $mail->Host = "10.20.20.227";
        $mail->SMTPAuth = false;
        $mail->From = "info@pickcenter.com";
        $mail->FromName = "Pick Center - Sistema a punti";
        $mail->headerLine("Content-Type: text/html; charset=UTF-8");
        $mail->AddAddress($destinatario, $nome_destinatario);
        include 'email_notif.php';
        $mail->AddReplyTo("info@pickcenter.com", "Informazioni");
        $mail->WordWrap = 50;
        $mail->IsHTML(true);
        $mail->Subject = $oggetto . ' - ' . $nome_destinatario;
        $mail->Body = $bodytext;
        $mail->AltBody = $corpodeltestotxt;
        $mail->send();
    }

}

//riaccredita prenotazioni cancellate e pagate con punti

/*$array_riaccrediti = $conn_siteground->query("select id, user_id, points, date from wpsd_wc_points_rewards_user_points_log where type = 'order-cancelled'");

$testo_email = '';

while ($riaccredito = $array_riaccrediti->fetch_assoc()) {

    $punti_riaccredito = abs($riaccredito["points"]);
    $id_booking = $riaccredito["user_id"];

    $dati_cliente = $conn_prod_punti->query("SELECT nome, email FROM anagrafica_punti WHERE id_cliente_booking = '".$id_booking."'")->fetch_assoc();
    $data_cancellazione = date('d/m/Y', strtotime($riaccredito["date"]));

    if ($dati_cliente['nome'] != NULL) {
        $testo_email = $testo_email . 'Riaccreditati <strong>'.$punti_riaccredito.' punti</strong> a <strong>'.$dati_cliente["nome"].'</strong> (Email: ' .$dati_cliente["email"]. ') per la prenotazione cancellata il <strong>'.$data_cancellazione.'</strong>.<hR>';

        aggiungi_punti($punti_riaccredito, $id_booking, NULL, 'riaccredito');
        $conn_siteground->query("UPDATE wpsd_wc_points_rewards_user_points_logs SET type = 'admin-adjustment', points = '0' WHERE id = '" . $riaccredito["id"] . "'");

    }

}

if ($testo_email != '') {


        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->IsSMTP();
        $mail->Host = "10.20.20.227";
        $mail->SMTPAuth = false;
        $mail->From = "info@pickcenter.com";
        $mail->FromName = "Pick Center - Sistema a punti";
        $mail->headerLine("Content-Type: text/html; charset=UTF-8");
        $mail->AddAddress('max@swhub.io', 'MS');
        //include 'email_notif.php';
        $mail->AddReplyTo("info@pickcenter.com", "Informazioni");
        $mail->WordWrap = 50;
        $mail->IsHTML(true);
        $mail->Subject = 'Riaccrediti';
        $mail->Body = $testo_email;
        $mail->AltBody = $corpodeltestotxt;
        $mail->send();

}*/
?>