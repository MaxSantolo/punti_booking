<html>
<head><link rel="stylesheet" type="text/css" href="tech/css/baseline.css" />
    <script type="text/javascript"  src="//code.jquery.com/jquery-2.1.0.js"></script>

    <meta charset="utf-8">
    <title>ELENCO COMPLETO CONTRATTI</title>
</head>
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
<body>
<div class="se-pre-con"></div>

    <?php include 'tech/menu.php' ?>
    <H1>ELENCO COMPLETO CONTRATTI</H1>
     
    <form action="" method="post" style="z-index: 999;display: block;">
        <table class="blueTable" style="width:20%;">
                <TR style="font-size:small;text-align:center;">
                        <TD width="75%"><input name="variabile" height="48" style="color:black" value="<?php echo isset($_POST['variabile']) ? $_POST['variabile'] : '' ?>"></TD><TD><input type="submit" name="cerca_contr" value="CERCA" style="color:black"></TD>
                </TR>
        </table>
    </form>

    <?php

include 'tech/connection.php';
include 'tech/variables.php';

isset($_POST['cerca_contr']) ? $conditions = " WHERE nome LIKE '%".$_POST['variabile']."%' OR partita_iva LIKE '%".$_POST['variabile']."%' OR codice_fiscale LIKE '%".$_POST['variabile']."%' OR email LIKE '%".$_POST['variabile']."%' " : $conditions = ' ';

$contratti = $conn_prod_intra->query($q_all_contr.$conditions.$q_all_contr_grord);

if ($contratti->num_rows > 0) {
    
    echo "<table class='blueTable'><thead><th>AZIENDA</th><th>INIZIO</th><th>FINE</th><th>EMAIL</th><th>P.IVA</th><th>COD. FISCALE</th><!--<th>N. RISORSE</th><th>PUNTI/ACCREDITO</th><th>N. ACCREDITI</th><th>RISORSE</th><th>SCADENZA PUNTI</th>--><th></th></thead>";

    while ($cont = $contratti->fetch_assoc()) {
     
        $visibile = $conn_prod_punti->query("SELECT * FROM anagrafica_punti WHERE id_cliente_dom2 = '".$cont['id_cliente']."'");
        $visibile->num_rows>0 ? $visib = "style='display:none;'" : $visib = "";

    echo "<TR ".$visib."><TD>".$cont['nome']."</TD><TD>".date('d/m/y',strtotime($cont['data_inizio']))."</TD><TD>".date('d/m/y', strtotime($cont['data_fine']))."</TD><TD>".$cont['email']."</TD><TD>".$cont['partita_iva']."</TD><TD>".$cont['codice_fiscale']."</TD>"
         ."<!--<TD>".$cont['numero_risorse']."</TD><TD>".$cont['punti_accredito']."</TD><TD>".$cont['num_accrediti']."</TD><TD>".$cont['risorse_group']."</TD><TD>".date('d/m/y',strtotime($cont['data_scadenza_punti']))."</TD>-->"
         ."<td  width='25'><a href='pagina_punti_dettagli.php?id_cliente=".$cont['id_cliente']."'><IMG SRC='immagini/trasferisci.png' width='25' TITLE='CHECK-IN PUNTI'></a></td></TR>";

    }
    echo "</table>";
} else echo "Non ci sono contratti.";


$conn_prod_intra->close();
$conn_prod_punti->close();
$conn_siteground->close();
$conn_prod_crm->close();

?>

<script>
    $(document).ready(function() {
//            //$(window).load(function() {
        $(".se-pre-con").fadeOut("slow");
//            //};
    });


</script>
</body>


</html>




