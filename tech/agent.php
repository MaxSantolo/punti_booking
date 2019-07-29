<?php



function aggiungi_punti($punti,$id,$id_accredito,$stato)
{ //aggiungi punti

    include('connection.php');

    $dati_contrattuali = $conn_prod_punti->query("SELECT * FROM anagrafica_punti WHERE id_cliente_booking = '" . $id . "' ")->fetch_assoc();
    $valori_attuali = $conn_siteground->query("SELECT * FROM wpsd_wc_points_rewards_user_points WHERE user_id = '" . $id . "' ORDER BY date DESC LIMIT 1")->fetch_assoc();

    if (!isset($id) or $id == '' or $id == NULL) {

        $dati_accredito_utente = $conn_prod_punti->query("SELECT * FROM accrediti WHERE id = '".$id_accredito."'")->fetch_assoc();
        $dati_contratto_utente = $conn_prod_punti->query("SELECT * FROM anagrafica_punti WHERE id = '".$dati_accredito_utente['id_cliente']."'")->fetch_assoc();
        $messaggio = $dati_contratto_utente['note'] . date('d-m-Y H:i') . ": Accredito impossibile, utente non registrato.<BR>";
        $conn_prod_punti->query("UPDATE anagrafica_punti SET note = '" . $messaggio . "' WHERE id = '" . $dati_contratto_utente['id'] . "'");

    } else {

        if ($stato != 'In attesa') {

            $messaggio = $dati_contrattuali['note'] . date('d-m-Y H:i') . ": Accredito già effettuato.<BR>";
            $conn_prod_punti->query("UPDATE anagrafica_punti SET note = '" . $messaggio . "' WHERE id_cliente_booking = '" . $id . "'");

        } else {

            $punti + $valori_attuali['points_balance'] > 600 ? $punti_agg = 600 : $punti_agg = $punti + $valori_attuali['points_balance'];
            $conn_siteground->query("UPDATE wpsd_wc_points_rewards_user_points SET points_balance = '" . $punti_agg . "', points = '" . $punti_agg . "'   WHERE user_id = '" . $id . "' ORDER BY date DESC LIMIT 1");
            $messaggio = $dati_contrattuali['note'] . date('d-m-Y H:i') . ": Aggiunti " . $punti . " punti al contratto.<BR>";
            $conn_prod_punti->query("UPDATE anagrafica_punti SET note = '" . $messaggio . "' WHERE id_cliente_booking = '" . $id . "'");

            if (isset($id_accredito)) {
                $conn_prod_punti->query("UPDATE accrediti SET accreditato = 'Accreditato', data_accredito = '" . date('Y-m-d') . "' WHERE id = '" . $id_accredito . "'");
            }

        }
    }
}

function resetta_punti($id,$id_dom2,$quali) { //reset punti sia solo online che tutti

    include ('connection.php');

    $dati_contrattuali = $conn_prod_punti->query("SELECT * FROM anagrafica_punti WHERE id_cliente_dom2 = '".$id_dom2."'")->fetch_assoc();

    if ($quali == "ONLINE") {

        $conn_siteground->query("UPDATE wpsd_wc_points_rewards_user_points SET points = '0', points_balance = '0' WHERE user_id = '".$id."'");
        $conn_prod_punti->query("UPDATE accrediti SET accreditato = 'In attesa' WHERE id_cliente = '".$dati_contrattuali['id']."' AND data_accredito > curdate()");

    }

    if ($quali == "TUTTI") {

        $conn_siteground->query("UPDATE wpsd_wc_points_rewards_user_points SET points = '0', points_balance = '0' WHERE user_id = '".$id."'");


        $conn_prod_punti->query("DELETE FROM accrediti WHERE id_cliente = '".$dati_contrattuali['id']."'");

        }

    $messaggio = date('d-m-Y H:i') . ": Reset punti (".$quali.")<BR>";
    $conn_prod_punti->query("UPDATE anagrafica_punti SET note = '".$dati_contrattuali['note'] . $messaggio."' WHERE id_cliente_booking = '".$id."'");
}

function crea_tabella_accrediti_mail($id_cliente) {

    include ('connection.php');

    $dati_accr = $conn_prod_punti->query("SELECT * FROM accrediti WHERE id_cliente='".$id_cliente."' and accreditato !='Scaduto' and accreditato != 'Accreditato' and (causale_bonus NOT LIKE '%rinnov%' OR causale_bonus IS NULL) ORDER BY data_accredito ASC");

    $dati_accr_bonus = $conn_prod_punti->query("SELECT *, sum(bonus_punti) as somma_bonus, max(data_accredito) as data_bonus FROM accrediti WHERE id_cliente='".$id_cliente."' and accreditato ='In attesa' and causale_bonus LIKE '%rinnov%'");


    $bonus = $dati_accr_bonus->fetch_assoc();


    $tabella = $tabella . '<table id="sugar_text_dettagli" style="width: 65%; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 100%; color: #777; border-collapse: collapse; padding: 0; margin-left: auto;margin-right: auto" cellspacing="0" cellpadding="0"><tbody>'
            . '<tr><td style="height: 5px; font-size: 1px;" height="5"></td></tr><tr><td style="text-align: left; font-weight: bold; color: #666; border-right: 1px solid #c1c1c1; padding-left: 5px;text-align: center;" valign="middle" width="50%" height="20">Prossimo accredito:</td>'
            . '<td style="text-align: left; font-weight: bold; color: #666; padding-left: 5px;text-align: center;" valign="middle" width="50%" height="20">Punti che riceverai:</td></tr><tr><td style="height: 5px; font-size: 1px;" height="5"></td></tr><tr style="height: 2px; font-size: 1px;">'
            . '<td style="height: 2px; background: #FF9900; font-size: 1px;" colspan="6" valign="top" height="2"></td></tr><tr><td style="height: 2px; font-size: 1px;" height="2"></td></tr>';

    
        
    while ($dati_accrediti = $dati_accr->fetch_assoc()) {

        $tabella = $tabella . '<tr style="height: 20px;"><td style="padding-left: 5px;text-align:center;border-right: 1px solid #c1c1c1;">'.date('d/m/Y', strtotime($dati_accrediti['data_accredito'])).'</td><td style="padding-left: 5px;text-align:center;">'.$dati_accrediti['punti'].'</td></tr><tr><td style="height: 2px; font-size: 1px; border-bottom: 1px solid #c1c1c1;" colspan="6" height="2"></td></tr>';

        }

    if ($bonus['data_bonus']!=NULL) {

        $tabella = $tabella . '<tr style="height: 20px;"><td style="padding-left: 5px;" colspan="2">Inoltre se rinnoverai entro il <strong>' . date('d/m/Y', strtotime($bonus['data_bonus'])) . '</strong> riceverai <strong>' . $bonus['bonus_punti'] . ' punti</strong> bonus!</td></tr>';

    }

    $tabella = $tabella . '<tr style="height: 2px; background: #eee; font-size: 1px;"><td style="border-bottom: 2px solid #ff9900;height: 2px; background: #eee; font-size: 1px;" colspan="6" valign="top" bgcolor="#eeeeee" height="2"></td></tr></tbody></table>';

    return $tabella;
}

function esiste_contratto_non_attivato($id_cliente) {

    include 'connection.php';
    include 'variables.php';
    $contratti = $conn_prod_intra->query($q_all_contr." WHERE id_cliente = ' ".$id_cliente."'".$q_all_contr_grord);
    if ($contratti->num_rows == 0) {return false;} else {return true;}

}

function saldo_punti($id_booking) {

    include 'connection.php';

    $punti = 0;

    if ($id_booking != NULL) {

    $saldo_punti = $conn_siteground->query("SELECT points_balance FROM wpsd_wc_points_rewards_user_points WHERE user_id = '".$id_booking."' order by date desc limit 1")->fetch_assoc();
    $punti = $saldo_punti['points_balance'];
}
    return $punti;
}

?>