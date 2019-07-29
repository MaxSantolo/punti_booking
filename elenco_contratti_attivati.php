<html>
<head><link rel="stylesheet" type="text/css" href="tech/css/baseline.css" />
    <script type="text/javascript"  src="//code.jquery.com/jquery-2.1.0.js"></script>
    <meta charset="utf-8">

    <title>CONTRATTI ATTIVATI PER BOOKING</title>

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

    <?php include 'tech/menu.php' ?>
    <H1>CONTRATTI ATTIVATI PER BOOKING</H1>

        <form action="" method="post" class="search">
        <table class="blueTable" style="width:20%;">
                <TR style="font-size:small;text-align:center;">
                        <TD width="75%"><input name="variabile" height="48" style="color:black" value="<?php echo isset($_POST['variabile']) ? $_POST['variabile'] : '' ?>"></TD><TD><input type="submit" name="cerca_contr" value="CERCA" style="color:black"></TD>
                </TR>
        </table>
        </form>


<?php

include 'tech/connection.php';
include 'tech/variables.php';

isset($_POST['cerca_contr']) ? $conditions = " WHERE nome LIKE '%".$_POST['variabile']."%' OR partita_iva LIKE '%".$_POST['variabile']."%' "
        . "OR risorse LIKE '%".$_POST['variabile']."%' OR punti_accredito LIKE '%".$_POST['variabile']."%' OR num_accrediti LIKE '%".$_POST['variabile']."%' "
        . "OR codice_fiscale LIKE '%".$_POST['variabile']."%' OR email LIKE '%".$_POST['variabile']."%' " : $conditions = ' ';

$contratti = $conn_prod_punti->query("SELECT * FROM anagrafica_punti" .$conditions. "ORDER BY nome");

if ($contratti->num_rows > 0) {
    
    echo "<table class='blueTable'><thead><th>AZIENDA</th><th>INIZIO</th><th>FINE</th><th>EMAIL</th><th>N. RIS.</th><th>P. ACC.</th><th>N. ACC.</th><th>RISORSE</th><th>SCADENZA PUNTI</th>"
    . "<th valign='middle'><H5></H5></th><th valign='middle'><H5></H5></th><th valign='middle'><H5></H5></th></thead>";

    while ($cont = $contratti->fetch_assoc()) {

    echo "<TR><TD>".$cont['nome']."</TD><TD>".date('d/m/y',strtotime($cont['data_inizio']))."</TD><TD>".date('d/m/y', strtotime($cont['data_fine']))."</TD><TD>".$cont['email']."</TD>"
         ."<TD width='5%'>".$cont['num_ris']."</TD><TD width='5%'>".$cont['punti_accredito']."</TD><TD width='5%'>".number_format($cont['num_accrediti'],1)."</TD><TD>".$cont['risorse']."</TD><TD>".date('d/m/y',strtotime($cont['data_scadenza']))."</TD>"
         ."<td  style='text-align:center;' width='25'><a href='pagina_punti_dettagli.php?id_cliente=".$cont['id_cliente_dom2']."'><IMG SRC='immagini/dettagli.png' width='25' TITLE='DETTAGLI'></a></td>"
         .""
         ."<td  style='text-align:center;' width='25'><!-- <a href='tech/funzioni.php?fx=elimina_contratto&id_cliente=".$cont['id_cliente_dom2']."'>-->";

        $today = strtotime('now');
        $data_fine_contratto = strtotime($cont['data_fine']);
        if ($data_fine_contratto <= $today) {
        echo "<IMG SRC='immagini/scaduto.png' width='25' TITLE='CONTRATTO SCADUTO'>";
        } else echo "<IMG SRC='immagini/attivo.png' width='25' TITLE='CONTRATTO ATTIVO'>";

        echo "<!--</a>--></td><td  style='text-align:center;' width='25'><a href='tech/funzioni.php?fx=elimina_contratto&id_cliente=".$cont['id_cliente_dom2']."' onclick='return confirm(\"Elimino il contratto a punti?\");'><IMG SRC='immagini/elimina.png' width='25' TITLE='ELIMINA CONTRATTO'></a></td></TR>";

    }
    echo "</table>";
} else echo "Non ci sono contratti.";

$conn_prod_intra->close();
$conn_prod_punti->close();
$conn_siteground->close();
$conn_prod_crm->close();



?>

</body>


</html>




