<html>
<head><link rel="stylesheet" type="text/css" href="tech/css/baseline.css" />
    <script type="text/javascript"  src="//code.jquery.com/jquery-2.1.0.js"></script>
    <meta charset="utf-8">
<style>
                 h1 {
  font-family: "Avant Garde", Avantgarde, "Century Gothic", CenturyGothic, "AppleGothic", sans-serif;
  font-size: 18px;
  padding: 10px 10px;
  text-align: center;
  text-transform: uppercase;
  text-rendering: optimizeLegibility;
  
    color: #ffffff;
    background-color: #D0D0D0;
    letter-spacing: .05em;
    text-shadow: 
      2px 2px 0px #1C6EA4, 
      3px 3px 0px rgba(0, 0, 0, 0.2);
  
}   
    
    
    body {
        background-image: url(immagini/sfondo.png);
        background-position: center center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
        background-color: #dddddd;
     } 
    
</style>

<title>DETTAGLI CONTRATTO/PUNTI</title>

</head>

<?php include 'tech/menu.php' ?>
<body>

    <H1>DETTAGLI CONTRATTO / PUNTI</H1>
    
<?php

include 'tech/connection.php';
include 'tech/variables.php';
include 'tech/var_mail.php';
require 'tech/class/PHPMailerAutoload.php';
include 'tech/agent.php';

$id = $_GET['id_cliente'];

$contratto = $conn_prod_intra->query($q_all_contr." WHERE id_cliente ='".$id."' ".$q_all_contr_grord);

$daticont = $contratto->fetch_assoc();
$num_acc_conv = number_format($daticont['num_accrediti']/100,1);

$presenza_contratto = $conn_prod_punti->query("SELECT * FROM anagrafica_punti WHERE id_cliente_dom2 = '".$id."' OR id_cliente_esolver = '".$id."'");
$dati_contr_punti = $presenza_contratto->fetch_assoc();

$crm = $conn_prod_crm->query($query_email_crm. "AND (email_address ='".$daticont['email']."' OR email_address = '".$dati_contr_punti['email']."')");
$crm_data = $crm->fetch_assoc();

$booking = $conn_siteground->query($query_userid_sito . "  WHERE user_email='".$daticont['email']."' OR user_email='".$dati_contr_punti['email']."'");
$book_data = $booking->fetch_assoc();

$elenco_accrediti = $conn_prod_punti->query("SELECT * FROM accrediti WHERE id_cliente = '".$dati_contr_punti['id']."' ORDER BY id");


if (isset($_POST["aggiorna_salva"])) { 
    
    $f_paccre = $_POST['f_puntiaccredito'];
    $f_datascad = $_POST['f_datascad'];
    $f_email = $_POST['f_email'];
    
    if ($presenza_contratto->num_rows>0) {          
      $conn_prod_punti->query("UPDATE anagrafica_punti SET punti_accredito = '".$f_paccre."', data_scadenza = '".$f_datascad."', email = '".$f_email."'  WHERE id_cliente_dom2 ='".$id."'");
      
    } else { 
      $conn_prod_punti->query("INSERT INTO anagrafica_punti (id_cliente_dom2, id_cliente_suite, id_cliente_booking, data_inizio, data_fine, nome, email, partita_iva, codice_fiscale, num_ris, punti_accredito, num_accrediti,"
                . "risorse, data_scadenza) VALUES ('".$id."', '".$crm_data['id']."', '".$book_data['ID']."', '".$daticont['data_inizio']."', '".$daticont['data_fine']."', '".$daticont['nome']."',"
                . "'".$daticont['email']."','".$daticont['partita_iva']."','".$daticont['codice_fiscale']."', '".$daticont['numero_risorse']."', '".$daticont['punti_accredito']."', '".$num_acc_conv."',"
                . "'".$daticont['risorse_group']."', '".$daticont['data_scadenza_punti']."')");
    }
    echo "<meta http-equiv='refresh' content='0'>";
}

//creazione accrediti automatici
if (isset($_POST['crea_accrediti'])) {
    
    $f_paccre = $_POST['f_puntiaccredito'];
    $f_datascad = $_POST['f_datascad'];
    $f_numacc = $dati_contr_punti['num_accrediti'];

    $dati_contr_punti['data_inizio']<'2018-02-01' ? $data_accredito = '2018-02-01' : $data_accredito = $dati_contr_punti['data_inizio'];


       
    while ($f_numacc >= 1) { 
            
        $f_paccre>600 ? $punti = 600 : $punti = $f_paccre;
        
        $conn_prod_punti->query("INSERT INTO accrediti (id_cliente, data_accredito, punti, scadenza, accreditato) VALUES ('".$dati_contr_punti['id']."', '".$data_accredito."', '".$punti."', '".date('Y-m-d', strtotime($data_accredito. '+60 days'))."', 'In attesa') ");
        $data_accredito = date('Y-m-d', strtotime($data_accredito. '+60 days'));
        $f_numacc = $f_numacc -1;
             
    } 

    if ($f_numacc == '0.5') { $f_paccre>600 ? $punti = 300 : $punti = $f_paccre/2; $conn_prod_punti->query("INSERT INTO accrediti (id_cliente, data_accredito, punti, scadenza, accreditato) VALUES ('".$dati_contr_punti['id']."', '".$data_accredito."', '".$punti."' , '".date('Y-m-d', strtotime($data_accredito. '+60 days'))."', 'In attesa')  ");}


    $conn_prod_punti->query("UPDATE anagrafica_punti SET note = '".$dati_contr_punti['note'].date('d-m-Y H:i').": Generati accrediti da contratto.<BR>' WHERE id = '".$dati_contr_punti['id']."'");


    if ($dati_contr_punti['num_accrediti']>=3 and $dati_contr_punti['num_ris']<=2) { $conn_prod_punti->query("INSERT INTO accrediti (id_cliente, data_accredito, bonus_punti, causale_bonus, scadenza, accreditato) VALUES ('".$dati_contr_punti['id']."', '".$dati_contr_punti['data_fine']."', '50', 'Bonus Rinnovo', '".date('Y-m-d', strtotime($dati_contr_punti['data_fine']. '+60 days'))."', 'In attesa') ");    }

    
//manda mail al cliente per avvio contratto
    $tabella_mail = crea_tabella_accrediti_mail($dati_contr_punti['id']);
    $bodytext = $header_email_standard . $testohead_mail_completa_avvio . $tabella_mail . $testofoot_mail_completa_avvio . $footer_email_standard;
    $destinatario = $dati_contr_punti['email'];
    $nome_destinatario = $dati_contr_punti['nome'];
    
    $mail = new PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->IsSMTP();
    $mail->Host = "10.20.20.227";
    $mail->SMTPAuth = false;
    $mail->From = "info@pickcenter.com";
    $mail->FromName = "Pick Center - Sistema a punti";
    $mail->headerLine("Content-Type: text/html; charset=UTF-8");
    $mail->AddAddress($destinatario,$nome_destinatario);
    include 'tech/email_notif.php';
    $mail->WordWrap = 50;
    $mail->IsHTML(true);
    $mail->Subject = $oggetto_mail_completa_avvio . ' - '. $nome_destinatario;
    $mail->Body    = $bodytext;
    $mail->AltBody = $corpodeltestotxt;
    $mail->send();
    
    echo "<meta http-equiv='refresh' content='0'>";

}
 //salvataggio modifiche accrediti automatici

if (isset($_POST['salva_accrediti'])) {


    $quanti = $elenco_accrediti->num_rows;

    $rows = [];
    while($row = mysqli_fetch_array($elenco_accrediti))
    { $rows[] = $row;  }

    while ($quanti > 0) {

        $n_data_accr = "data_accr_".$rows[$quanti-1]['id'];
        $n_punti_accr = "punti_accr_".$rows[$quanti-1]['id'];
        $n_punbon_accr = "punbon_accr_".$rows[$quanti-1]['id'];
        $n_punboncaus_accr = "punboncaus_accr_".$rows[$quanti-1]['id'];
        $n_accr = "accr_".$rows[$quanti-1]['id'];

        $id_accr = $rows[$quanti-1]['id'];

        $agg_data = $_POST[$n_data_accr];
        $agg_punti = $_POST[$n_punti_accr];
        $agg_punbonus = $_POST[$n_punbon_accr];
        $agg_punboncaus = $_POST[$n_punboncaus_accr];
        $agg_accr = $_POST[$n_accr];

        $conn_prod_punti->query("UPDATE accrediti SET data_accredito = '".$agg_data."', punti ='".$agg_punti."', bonus_punti ='".$agg_punbonus."', causale_bonus = '".$agg_punboncaus."', accreditato = '".$agg_accr."' WHERE id = '".$id_accr."'");

        //echo "UPDATE accrediti SET data_accredito = '".$agg_data."', punti ='".$agg_punti."', bonus_punti ='".$agg_punbonus."', causale_bonus = '".$agg_punboncaus."'  WHERE id = '".$id_accr."'";

        $quanti = $quanti -1;
    }
    echo "<meta http-equiv='refresh' content='0'>";

}



?>
    

<form method="post">
    <table class='blueTable' style="width:80%;margin-left:auto;margin-right:auto;text-align:center"><thead><TH>DATI CONTRATTO</th></thead></table>
    <table class='blueTable' style="width:80%;margin-left:auto;margin-right:auto;">
        <tr><th>AZIENDA</th><td><?php echo $presenza_contratto->num_rows>0 ? $dati_contr_punti['nome'] : $daticont['nome'] ?></td><th>EMAIL**</th><td><INPUT type="email" style='width: 250px;' name="f_email" value="<?php echo $presenza_contratto->num_rows>0 ? $dati_contr_punti['email'] : $daticont['email'] ?>"></td><th>CRM</th><td><?php if ($crm->num_rows>0) {echo "<IMG SRC='immagini/ok.png' WIDTH=20 TITLE='ID: ".$crm_data['id']."'>";} else echo "<IMG SRC='immagini/not-ok.png' WIDTH=20 TITLE='NON PRESENTE'>"; ?> </td></tr>
        <tr><TH>PARTITA IVA</TH><TD><?php echo $presenza_contratto->num_rows>0 ? $dati_contr_punti['partita_iva'] : $daticont['partita_iva'] ?></TD><TH>CODICE FISCALE</TH><TD><?php echo $presenza_contratto->num_rows>0 ? $dati_contr_punti['codice_fiscale'] : $daticont['codice_fiscale'] ?></TD><th>ESOLVER</th><td>X/V</td></tr>
        <tr><th colspan="4">CONTRATTO</th><th>BOOKING</th><td><?php if ($booking->num_rows>0) {echo "<IMG SRC='immagini/ok.png' WIDTH=20 TITLE='ID: ".$book_data['ID']."'>";} else echo "<IMG SRC='immagini/not-ok.png' WIDTH=20 TITLE='NON PRESENTE'>"; ?> </td></tr>
        <tr><th>INIZIO</th><td><?php echo date('d/m/Y',strtotime($presenza_contratto->num_rows>0 ? $dati_contr_punti['data_inizio'] : $daticont['data_inizio'])) ?></td><TH>FINE</TH><td><?php echo date('d/m/Y',strtotime($presenza_contratto->num_rows>0 ? $dati_contr_punti['data_fine'] : $daticont['data_fine'])) ?></td><td colspan="20"></td></tr>
        <tr><th>RISORSE</th><td COLSPAN="6"><?php echo $presenza_contratto->num_rows>0 ? $dati_contr_punti['risorse'] : $daticont['risorse_group'] ?></td></tr>
        <tr><th COLSPAN="6">DATI SUI PUNTI</th></tr>
        <TR><TH>PUNTI PER ACCREDITO*</TH><TD><INPUT TYPE="number" name="f_puntiaccredito" value="<?php echo $presenza_contratto->num_rows>0 ? $dati_contr_punti['punti_accredito'] : $daticont['punti_accredito'] ?>"></TD><TH>NUMERO DI ACCREDITI</TH><TD><?php echo $presenza_contratto->num_rows>0 ? $dati_contr_punti['num_accrediti'] : $num_acc_conv ?></TD></TD><TH>SCADENZA PUNTI</TH><td><INPUT TYPE="date" name="f_datascad" value="<?php echo $presenza_contratto->num_rows>0 ? $dati_contr_punti['data_scadenza'] : $daticont['data_scadenza_punti'] ?>"></TD></TR>
        <TR>
                <TD COLSPAN="4">**L'email deve corrispondere a quella con cui ci si è registrati sul Booking (modificandola qui non si modifica sul CRM)<br>*Bimestrale/L'accredito massimo viene calcolato automaticamente</TD>
                <TH>SALDO PUNTI</TH><TD><P style="font-size: large; font-weight: bold;color: dodgerblue"><?php echo saldo_punti($book_data['ID']) ?></p></TD>
        </TR>
        <TR><TD colspan='<?php echo ($elenco_accrediti->num_rows>0 || $presenza_contratto->num_rows==0)?'6':'3'?>' style='text-align: center'><input type="submit" name="aggiorna_salva" value="<?php echo $presenza_contratto->num_rows>0 ? "AGGIORNA" : "SALVA"  ?>"/> </Td>
            <td colspan="3" style='text-align: center;display:<?php echo ($elenco_accrediti->num_rows>0 || $presenza_contratto->num_rows==0)?'none':''?>;'><input type="submit" name="crea_accrediti" style="visibility:<?php echo $presenza_contratto->num_rows>0?'visible':'hidden'?>" value="GENERA ACCREDITI" />
        </tr>
    </table>
</form>
    
<?php
// $elenco_accrediti = $conn_prod_punti->query("SELECT * FROM accrediti WHERE id_cliente = '".$dati_contr_punti['id']."' ORDER BY id");

if  ($elenco_accrediti->num_rows>0)  {     
    echo "<table class='blueTable' style='width:80%;margin-left:auto;margin-right:auto;'><form method='post' name='accrediti'><THEAD><th>ID</th><TH>DATA ACCREDITO</TH><TH>PUNTI</TH><TH>PUNTI BONUS</TH><TH>CAUSALE DEL BONUS</TH><TH style='text-align: center'>STATO</TH>"
    . "<TH></TH><TH style='text-align:center;'><a href='tech/funzioni.php?fx=aggiungi_accredito&id_cliente=".$dati_contr_punti['id']."&id_cliente_dom2=".$id."'><IMG SRC='immagini/aggiungi_accredito.png' width='25' TITLE='AGGIUNGI ACCREDITO'></a></TH></THEAD>";
    while ($accred = $elenco_accrediti->fetch_assoc()){

                echo "<TR><td width='5%'>".$accred['id']."</td>"
                . "<TD width='10%'><input type='date' name='data_accr_".$accred['id']."' value='".$accred['data_accredito']."'></td>"
                . "<TD width='10%'><INPUT TYPE='NUMBER' NAME='punti_accr_".$accred['id']."' value='".$accred['punti']."'> </td>"
                . "<TD width='10%'><INPUT TYPE='NUMBER' NAME='punbon_accr_".$accred['id']."' value='".$accred['bonus_punti']."'> </td>"
                . "<TD><INPUT TYPE='text' NAME='punboncaus_accr_".$accred['id']."' value='".$accred['causale_bonus']."' style='width: 500px;'> </td>"
                . "<TD width='5%' style='text-align:center;'><SELECT NAME='accr_".$accred['id']."' value='".$accred['accreditato']."'>"
                ." <option value='".$accred['accreditato']."'>".$accred['accreditato']."</option><option value='In attesa'>In attesa</option><option value='Accreditato'>Accreditato</option><option value='Scaduto'>Scaduto</option></SELECt></td>"
                . "<td  style='text-align:center;'><a href='tech/funzioni.php?fx=forza_accredito&id_accredito=".$accred['id']."&id_cliente=".$id."'><IMG SRC='immagini/forza_accredito.png' width='25' TITLE='FORZA ACCREDITO'></a></td>"
                . "<td  style='text-align:center;'><a href='tech/funzioni.php?fx=elimina_accredito&id_accredito=".$accred['id']."&id_cliente=".$id."'><IMG SRC='immagini/cancella_accredito.png' width='25' TITLE='ELIMINA ACCREDITO' onclick=\"return confirm('Sicuro di voler eliminare questo accredito? Ricorda che possono essere eliminati solo gli accrediti in attesa')\"></a></td></tr>";
    }
    echo "<tr><td colspan='8' style='text-align: center;'><input type='submit' name='salva_accrediti' value='SALVA ACCREDITI'></td></tr></form>";

    echo "<tr><td colspan='8' style='text-align: center;'>"
        ."<a href='tech/funzioni.php?fx=invia_mail_completa&id=".$dati_contr_punti['id_cliente_booking']."&iddom2=".$id."' ><IMG SRC='immagini/mail.png' width='35' TITLE='INVIA MAIL ISCRIZIONE' vspace='15' onclick=\"return confirm('Mando la mail con le informazioni di iscrizione?')\"></a>"
        ."<a href='tech/funzioni.php?fx=elimina_note&id=".$dati_contr_punti['id']."&iddom2=".$id."'><IMG SRC='immagini/cancella_note.png' width='35' TITLE='ELIMINA NOTE' vspace='15'></a>"
        ."<a href='tech/funzioni.php?fx=elimina_tutti_accrediti&id=".$dati_contr_punti['id_cliente_booking']."&id_cliente=".$id."'><IMG SRC='immagini/delete_all_credits.png' width='35' TITLE='ELIMINA TUTTI GLI ACCREDITI' vspace='15' onclick=\"return confirm('Sicuro di voler eliminare tutti gli accrediti programmati e resettare i punti online?')\"></a>"
        ."<a href='tech/funzioni.php?fx=reset_online&id=".$dati_contr_punti['id_cliente_booking']."&id_cliente=".$id."'><IMG SRC='immagini/reset.png' width='35' TITLE='RESETTA I PUNTI ONLINE' vspace='15' onclick=\"return confirm('Sicuro di voler resettare i punti online?')\"></a>"

        ."</td></tr></table>";
}


    if  ($presenza_contratto->num_rows>0)  {
    echo "<table class='blueTable' style='width:80%;margin-left:auto;margin-right:auto;'><THEAD><th style='text-align: center'>NOTE</th></THEAD>";

            echo "<tbody><TR><td>".$dati_contr_punti['note']."</td>";

            echo "</tbody></table>";


    }



$conn_prod_intra->close();
$conn_prod_punti->close();
$conn_siteground->close();
$conn_prod_crm->close();

?>



</body>
</html>




