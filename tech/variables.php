<?php

$q_all_contr = "select id_cliente, nome, data_inizio, data_fine, email, partita_iva, codice_fiscale, sum(numris) as numero_risorse, sum(punti_accredito) as punti_accredito, MAX(num_accrediti) as num_accrediti, group_concat(risorse SEPARATOR ' | ') as risorse_group, max(data_scadenza_punti) as data_scadenza_punti from punti_booking";
$q_all_contr_grord = " group by id_cliente order by nome";
$q_all_contr_conditions = "  ";
$query_email_crm = "select leads.id, email.email_address from leads, email_addr_bean_rel as bean, email_addresses as email where email.id = bean.email_address_id and bean.bean_id = leads.id and leads.deleted != '1'";
$query_booking_email = "select wpsd_wc_points_rewards_user_points.id, wpsd_wc_points_rewards_user_points.date, user_id, points, points_balance, user_email from wpsd_wc_points_rewards_user_points, wpsd_users where wpsd_wc_points_rewards_user_points.user_id = wpsd_users.ID";
$query_userid_sito = "select ID, user_email from wpsd_users";


?>