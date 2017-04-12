/* sont déjà chargés les unités budgétaire (groupes & structures & ub), les activités et les conventions */

/* référentiel des pays */
delete from cplt_pays_deleted ;
delete from cplt_pays_data_group_mode ;
delete from cplt_pays_data_group ;
delete from cplt_pays_data ;

insert into cplt_pays_data ( id_data, id_owner, code, label, monnaie, cog, grp1, taux, grp1eur) select PMK, 1, code_pays, libelle_pays, code_monnaie, COG, grp1, taux, grp1eur from liste_pays ; 
insert into cplt_pays_data_group (id_data, id_group) select id_data, 1 from cplt_pays_data ;

/** purge */
delete from cplt_vntl_deleted ;
delete from cplt_vntl_data_group_mode ;
delete from cplt_vntl_data_group ;
delete from cplt_vntl_data ;

delete from cplt_dmp_deleted ;
delete from cplt_dmp_data_group_mode ;
delete from cplt_dmp_data_group ;
delete from cplt_dmp_data ;

delete from cplt_pe_deleted ;
delete from cplt_pe_data_group_mode ;
delete from cplt_pe_data_group ;
delete from cplt_pe_data ;

delete from cplt_oc_deleted ;
delete from cplt_oc_data_group_mode ;
delete from cplt_oc_data_group ;
delete from cplt_oc_data ;

delete from cplt_sta_deleted ;
delete from cplt_sta_data_group_mode ;
delete from cplt_sta_data_group ;
delete from cplt_sta_data ;

delete from cplt_rh_deleted ;
delete from cplt_rh_data_group_mode ;
delete from cplt_rh_data_group ;
delete from cplt_rh_data ;

delete from cplt_dt_deleted ;
delete from cplt_dt_data_group_mode ;
delete from cplt_dt_data_group ;
delete from cplt_dt_data ;

delete from cplt_mi_deleted ;
delete from cplt_mi_data_group_mode ;
delete from cplt_mi_data_group ;
delete from cplt_mi_data ;

delete from cplt_gci_deleted ;
delete from cplt_gci_data_group_mode ;
delete from cplt_gci_data_group ;
delete from cplt_gci_data ;

delete from cplt_si_deleted ;
delete from cplt_si_data_group_mode ;
delete from cplt_si_data_group ;
delete from cplt_si_data ;

delete from cplt_dpns_deleted ;
delete from cplt_dpns_data_group_mode ;
delete from cplt_dpns_data_group ;
delete from cplt_dpns_data ;

delete from cplt_bdgt_deleted ;
delete from cplt_bdgt_data_group_mode ;
delete from cplt_bdgt_data_group ;
delete from cplt_bdgt_data ;

delete from cplt_vld_deleted ;
delete from cplt_vld_data_group_mode ;
delete from cplt_vld_data_group ;
delete from cplt_vld_data ;

delete from cplt_dmnd_deleted ;
delete from cplt_dmnd_data_group_mode ;
delete from cplt_dmnd_data_group ;
delete from cplt_dmnd_data ;

delete from cplt_sv_deleted ;
delete from cplt_sv_data_group_mode ;
delete from cplt_sv_data_group ;
delete from cplt_sv_data ;

/* remplissage id_structure */
update cplt_ub_data set id_structure = null ;

select concat( "update cplt_ub_data set id_structure = ", gl.id_group_parent, " where id_data = ", ub.id_data, ";" )
into outfile "/tmp/copilote.structure1.sql"
from cplt_ub_data ub 
join cplt_pj_group g on ub.id_group = g.id_group
join cplt_pj_group_link gl on g.id_group = gl.id_group
left join cplt_ub_data gub on gl.id_group_parent = gub.id_group
where gub.id_data is null ;

source /tmp/copilote.structure1.sql

select concat( "update cplt_ub_data set id_structure = ", gl2.id_group_parent, " where id_data = ", ub.id_data, ";" )
into outfile "/tmp/copilote.structure2.sql"
from cplt_ub_data ub 
join cplt_pj_group_link gl1 on ub.id_group = gl1.id_group
join cplt_pj_group_link gl2 on gl1.id_group_parent = gl2.id_group
where ub.id_structure is null ;

source /tmp/copilote.structure2.sql

/* remplissage id_gub */
update cplt_ub_data set id_gub = null ;

select concat( "update cplt_ub_data set id_gub = ", gub.id_data, " where id_data = ", ub.id_data, ";" )
into outfile "/tmp/copilote.gub.sql"
from cplt_ub_data ub 
inner join cplt_pj_group g on ub.id_group = g.id_group
inner join cplt_pj_group_link gl on g.id_group = gl.id_group
inner join cplt_ub_data gub on gl.id_group_parent = gub.id_group ;

source /tmp/copilote.gub.sql

/* affectations de "has_conv" renseignant les ub avec conventions */
update cplt_ub_data set has_conv = 11 ;

select distinct concat( "update cplt_ub_data set has_conv = 10 where id_data = ", id_ub, " or id_gub = ", id_ub, " ;" ) 
into outfile "/tmp/hasconv.sql"
from cplt_conv_data ;

source /tmp/hasconv.sql

/* droits: donner l'accès au groupe de l'UB, du GUB et de l'INED */
/* unités budégataires */
update cplt_ub_data set id_owner = id_group ;
delete from cplt_ub_data_group ;
insert into cplt_ub_data_group (id_data, id_group) select ub.id_data, ub.id_group from cplt_ub_data ub ;
insert into cplt_ub_data_group (id_data, id_group) select ub.id_data, 1 from cplt_ub_data ub ; 
insert into cplt_ub_data_group (id_data, id_group) select ub.id_data, gub.id_group from cplt_ub_data ub join cplt_ub_data gub on gub.id_data = ub.id_gub ;

/** fiches de suivi */
set @year := 2016 ;
insert into cplt_sv_data (id_owner, id_ub, annee) select ub.id_group, ub.id_data, @year from cplt_ub_data ub where ub.id_data not in ( select distinct id_gub from cplt_ub_data where id_gub > 0 ) ;
insert into cplt_sv_data_group (id_data, id_group) select sv.id_data, sv.id_owner from cplt_sv_data sv ;
insert into cplt_sv_data_group (id_data, id_group) select sv.id_data, 1 from cplt_sv_data sv ;
insert into cplt_sv_data_group (id_data, id_group) select sv.id_data, gub.id_group from cplt_sv_data sv join cplt_ub_data ub on sv.id_ub = ub.id_data join cplt_ub_data gub on gub.id_data = ub.id_gub ;

/** demandes */
insert into cplt_dmnd_data (id_owner, id_suivi, montant, montant_scsp, montant_convention, date_creation, etat, verrou, budget) 
select sv.id_owner, sv.id_data, 0, 0, 0, now(), 476, 11, 489 from cplt_sv_data sv ;
insert into cplt_dmnd_data_group (id_data, id_group) select d.id_data, d.id_owner from cplt_dmnd_data d ;
insert into cplt_dmnd_data_group (id_data, id_group) select d.id_data, 1 from cplt_dmnd_data d ;
insert into cplt_dmnd_data_group (id_data, id_group) select d.id_data, gub.id_group from cplt_dmnd_data d join cplt_sv_data sv on d.id_suivi = sv.id_data join cplt_ub_data ub on sv.id_ub = ub.id_data join cplt_ub_data gub on gub.id_data = ub.id_gub ;

/* Identités */
delete from cplt_id_data_group ;
update cplt_gci_data set gci_identite = null ;
update cplt_dt_data set dt_identite = null ;
update cplt_mi_data set mi_identite = null ;
update cplt_rh_data set rh_identite = null ;
delete from cplt_id_data ;
insert into cplt_id_data (id_data, id_owner, nom, prenom, label, statut ) select id_identite, 1, nom, prenom, nom_prenom, statut from table_identites ; -- la "table_identites" est fournie par l'INED  
insert into cplt_id_data_group (id_data, id_group) select id_data, 1 from cplt_id_data ;

/* pour chaque tuple (demande, activite) il faut créer une dépense */
insert ignore into cplt_dpns_data (id_owner, id_demande, activite, montant, montant_scsp, montant_convention) 
select ub.id_group, dmnd.id_data,
 (select a.id_data from cplt_act_data a where concat('[', a.activite, '] ', a.sousactivite) = ec.ec_activite) as activite, 0 as montant, 0 as montant_scsp, 0 as montant_convention
from table_des_EC ec -- avoir charger la "table des EC" avant
join cplt_pj_group g ON ec.idub = g.name
join cplt_ub_data ub ON g.id_group = ub.id_group 
join cplt_sv_data sv ON ub.id_data = sv.id_ub 
join cplt_dmnd_data dmnd ON dmnd.id_suivi = sv.id_data
where sv.annee = @year 
  and coalesce(dmnd.verrou, 11) = 11 ;
/* on ajoute les droits sur les dépenses */
insert ignore into cplt_dpns_data_group( id_data, id_group ) select id_data, id_owner from cplt_dpns_data ;
insert ignore into cplt_dpns_data_group( id_data, id_group ) select id_data, 1 from cplt_dpns_data ;
insert ignore into cplt_dpns_data_group( id_data, id_group ) select dpns.id_data, gub.id_group from cplt_dpns_data dpns join cplt_ub_data ub on dpns.id_owner = ub.id_group join cplt_ub_data gub on ub.id_gub = gub.id_data ;

/* ajout des marchés */
insert into cplt_dmp_data (id_owner, id_depense, ec_num, ec_objet, ec_cocontractant, ec_date_deb, ec_date_fin_max, ec_type_duree, ec_montant_initial, ec_ej_contractuel_n1, ec_ej_prev_n1, ec_rap_scsp, ec_rap_rp) 
select ub.id_group, dpns.id_data, ec.ec_num, ec.ec_objet, ec.ec_cocontractant, str_to_date(ec.ec_date_deb, '%d/%m/%Y'), str_to_date(ec.ec_date_fin_max, '%d/%m/%Y'), ec.ec_type_duree, ec.ec_montant_initial, ec.ec_ej_contractuel_n1, ec.ec_ej_prev_n1, ec.ec_rap_scsp, ec.ec_rap_rp
from table_des_EC ec 
join cplt_pj_group g ON ec.idub = g.name
join cplt_ub_data ub ON g.id_group = ub.id_group 
join cplt_sv_data sv ON ub.id_data = sv.id_ub 
join cplt_dmnd_data dmnd ON dmnd.id_suivi = sv.id_data
join cplt_dpns_data dpns ON dpns.id_demande = dmnd.id_data
join cplt_act_data a ON dpns.activite = a.id_data
where sv.annee = @year 
  and coalesce(dmnd.verrou, 11) = 11 
  and concat('[', a.activite, '] ', a.sousactivite) = ec.ec_activite ;
/* on ajoute les droits sur les marchés */
insert ignore into cplt_dmp_data_group (id_data, id_group) select id_data, id_owner from cplt_dmp_data ;
insert ignore into cplt_dmp_data_group (id_data, id_group) select id_data, 1 from cplt_dmp_data ;
insert ignore into cplt_dmp_data_group (id_data, id_group) select dmp.id_data, gub.id_group from cplt_dmp_data dmp join cplt_ub_data ub on dmp.id_owner = ub.id_group join cplt_ub_data gub on ub.id_gub = gub.id_data ;
