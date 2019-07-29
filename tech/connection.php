<?php

date_default_timezone_set('Europe/Rome');

$servername_prod = '192.168.1.10';
$username_prod = 'root';
$password_prod = 'fm105pick';
$db_prod1 = 'crm_punti';

// creo connessione punti
$conn_prod_punti = new mysqli($servername_prod,$username_prod,$password_prod,$db_prod1); //produzione.cespiti


// creo connessione intranet

$db_prod = 'intranet';
$conn_prod_intra = new mysqli($servername_prod,$username_prod,$password_prod,$db_prod); //produzione.cespiti

if ($conn_prod_intra->connect_error) { die("Errore connessione Produzione, Intranet: " . $conn->connect_error); }
// creo connessione crm

$db_prod2 = 'crm';
$conn_prod_crm = new mysqli($servername_prod,$username_prod,$password_prod,$db_prod2); //produzione.cespiti
if ($conn_prod_crm->connect_error) { die("Errore connessione Produzione, CRM: " . $conn->connect_error); }

//connessione db_online

$db_prod3 = 'pickcent_23';
$server_siteground = 'pickcenter.it';
$username_siteground = 'pickcent_admin';
$password_siteground = '@FM@105Pick!';

$conn_siteground =  new mysqli($server_siteground,$username_siteground,$password_siteground,$db_prod3);
if ($conn_siteground->connect_error) { die("Errore connessione Produzione, Siteground: " . $conn->connect_error); }


/*$conn_prod_punti->query("SET NAMES utf8");
$conn_prod_intra->query("SET NAMES utf8");
$conn_prod_crm->query("SET NAMES utf8");
$conn_siteground->query("SET NAMES utf8");*/




?>