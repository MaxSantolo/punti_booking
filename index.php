


<html>
    <head><link rel="stylesheet" type="text/css" href="tech/css/baseline.css" />
        <script type="text/javascript"  src="//code.jquery.com/jquery-2.1.0.js"></script>
        <style>
            
             h1 {
  font-family: "Avant Garde", Avantgarde, "Century Gothic", CenturyGothic, "AppleGothic", sans-serif;
  font-size: 24px;
  padding: 20px 20px;
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
        <link rel="stylesheet" type="text/css" href="tech/css/baseline.css" />
        <title>PUNTI BOOKING</title>
    </head>
    
    <body>
    <div class="content">
    <H1>GESTIONE PUNTI BOOKING</h1>
    
    <table class="blueTable" style="width: 50%;text-align: center">
        <thead><th colspan="4">MENU PRINCIPALE</th></thead>
        <tr>
            <td style="width: 25%"><A HREF="elenco_contratti.php"><IMG SRC="immagini/contratti_elenco.png" width="75"><BR />ELENCO CONTRATTI</a></td>
            <TD style="width: 25%"><A HREF="elenco_contratti_attivati.php"><IMG SRC="immagini/contratti_attivi.png" width="75"><BR />ELENCO CONTRATTI ATTIVATI</A></td>
            <TD style="width: 25%"><A HREF="elenco_contratti_scaduti.php"><IMG SRC="immagini/scaduti.png" width="75"><BR />ELENCO CONTRATTI SCADUTI</A></td>
            <!--<td style="width: 25%"><A HREF="bolle_fatture_precedenti.php"><IMG SRC="immagini/menu_vecchidoc.png" width="75"><BR /><BR />ANNI PRECEDENTI</A></td>
            <td style="width: 25%"><A HREF="ricerca_generale.php"><IMG SRC="immagini/search.png" width="75"><BR /><BR />RICERCA GENERALE</A></td>-->
        </tr>
    </table>

    </div>
    <div id="divLoading">
    </div>
    <script type="text/javascript">


        $(window).load(function(){

            $(document).ready(function(){
                $("div.content").click(function(){
                    $("div#divLoading").addClass('show');
                });
            });

        });

    </script>

    </body>


</html>

