<?php

include ('connection.php');
include ('agent.php');
include ('variables.php');
require ('class/PHPMailerAutoload.php');




//elimina il contratto e tutti gli accrediti: deve lasciare i punti su booking?
if ($_GET['fx'] == 'elimina_contratto') {
    
    $id_c = $_GET['id_cliente'];
    
    $cliente = $conn_prod_punti->query("SELECT * FROM anagrafica_punti WHERE id_cliente_dom2 ='".$id_c."'")->fetch_assoc();
    
    $conn_prod_punti->query("DELETE FROM anagrafica_punti WHERE id ='".$cliente['id']."'");
    $conn_prod_punti->query("DELETE FROM accrediti WHERE id_cliente = '".$cliente['id']."' ");
     
    
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    
}

if ($_GET['fx'] == 'elimina_accredito') {

    $id_c = $_GET['id_cliente'];
    $id_a = $_GET['id_accredito'];

    $conn_prod_punti->query("DELETE FROM accrediti WHERE id = '".$id_a."' AND accreditato = 'In attesa' OR accreditato = 'Scaduto'");


    header("Location: http://192.168.1.51:83/pagina_punti_dettagli.php?id_cliente=".$id_c);

}

if ($_GET['fx'] == 'aggiungi_accredito') {

    $id_c = $_GET['id_cliente'];
    $id_c_d2 = $_GET['id_cliente_dom2'];

    $conn_prod_punti->query("INSERT INTO accrediti (id_cliente) VALUES (".$id_c.")");


    header("Location: http://192.168.1.51:83/pagina_punti_dettagli.php?id_cliente=".$id_c_d2);

}

if ($_GET['fx'] == 'elimina_tutti_accrediti') {

    $id_c = $_GET['id_cliente'];
    $id = $_GET['id'];
    resetta_punti($id,$id_c,"TUTTI");

    header("Location: http://192.168.1.51:83/pagina_punti_dettagli.php?id_cliente=".$id_c);



}


if ($_GET['fx'] == 'reset_online') {

    $id_c = $_GET['id_cliente'];
    $id = $_GET['id'];
    resetta_punti($id,$id_c,$quali = "ONLINE");

    header("Location: http://192.168.1.51:83/pagina_punti_dettagli.php?id_cliente=".$id_c);



}



if ($_GET['fx'] == 'forza_accredito') {

    $id_accredito = $_GET['id_accredito'];
    $id_dom2_cliente = $_GET['id_cliente'];

    $dati_accre = $conn_prod_punti->query("SELECT * FROM accrediti WHERE id='".$id_accredito."'")->fetch_assoc();
    $dati_contr = $conn_prod_punti->query("SELECT * FROM anagrafica_punti WHERE id_cliente_dom2 ='".$id_dom2_cliente."'")->fetch_assoc();

    //echo "SELECT * FROM accrediti WHERE id='".$id_accredito."'";
    //echo "SELECT * FROM anagrafica_punti WHERE id_cliente_dom2 ='".$id_dom2_cliente."'";

    $puntid = $dati_accre['punti'] + $dati_accre['bonus_punti'];

    aggiungi_punti($puntid,$dati_contr['id_cliente_booking'],$dati_accre['id'],$dati_accre['accreditato']);


    header("Location: http://192.168.1.51:83/pagina_punti_dettagli.php?id_cliente=".$id_dom2_cliente);



}


if ($_GET['fx'] == 'elimina_note') {

    $id = $_GET['id'];
    $id_dom2_cliente = $_GET['iddom2'];

    $conn_prod_punti->query("UPDATE anagrafica_punti SET note = '' WHERE id = '".$id."'");


    header("Location: http://192.168.1.51:83/pagina_punti_dettagli.php?id_cliente=".$id_dom2_cliente);



}

if ($_GET['fx'] == 'invia_mail_completa') {

    include ('var_mail.php');

    $id_booking = $_GET['id'];
    $id_dom2_cliente = $_GET['iddom2'];

    if ($id_booking == '' || $id_booking == NULL) {
            $dati_contr_email = $conn_prod_punti->query("SELECT * FROM anagrafica_punti where id_cliente_dom2 ='".$id_dom2_cliente."'")->fetch_assoc();        
        }
    else {
        $dati_contr_email = $conn_prod_punti->query("SELECT * FROM anagrafica_punti where id_cliente_booking ='".$id_booking."'")->fetch_assoc(); 
        }
    
    $tabella_mail = crea_tabella_accrediti_mail($dati_contr_email['id']);

    $bodytext = $header_email_standard . $testohead_mail_completa_avvio . $tabella_mail . $testofoot . $footer_email_standard;
    
    
    $destinatario = $dati_contr_email['email'];
    $nome_destinatario = $dati_contr_email['nome'];
    
    $mail = new PHPMailer(true );

    
    $mail->CharSet = 'UTF-8';
    $mail->IsSMTP();
    $mail->Host = "10.20.20.227";
    $mail->SMTPAuth = false;
    $mail->From = "info@pickcenter.com";
    $mail->FromName = "Pick Center - Sistema a punti";
    $mail->AddAddress($destinatario,$nome_destinatario);
    include 'email_notif.php';
    $mail->WordWrap = 50;
    $mail->IsHTML(true);
    $mail->Subject = $oggetto_mail_completa_avvio. ' - ' . $nome_destinatario;
    $mail->Body    = $bodytext;
    $mail->AltBody = $corpodeltestotxt;
    
    $mail->send();


    header("Location: http://192.168.1.51:83/pagina_punti_dettagli.php?id_cliente=".$id_dom2_cliente);



}

//elimina il contratto e tutti gli accrediti: deve lasciare i punti su booking?
if ($_GET['fx'] == 'riattiva_contratto') {

    $id_c = $_GET['id_cliente'];
    $testo_header = "Location: http://192.168.1.51:83/pagina_punti_dettagli.php?id_cliente=".$id_c;

    $cliente = $conn_prod_punti->query("SELECT * FROM anagrafica_punti WHERE id_cliente_dom2 ='".$id_c."'")->fetch_assoc();

    $conn_prod_punti->query("DELETE FROM anagrafica_punti WHERE id ='".$cliente['id']."'");
    $conn_prod_punti->query("DELETE FROM accrediti WHERE id_cliente = '".$cliente['id']."' ");

    //echo  "Location: http://192.168.1.51:83/pagina_punti_dettagli.php?id_cliente=".$id_c;
    header($testo_header);

}


?>

