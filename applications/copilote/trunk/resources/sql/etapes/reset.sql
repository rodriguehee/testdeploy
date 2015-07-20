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

set @year := 2016 ;
insert into cplt_sv_data (id_owner, id_ub, annee) select ub.id_group, ub.id_data, @year from cplt_ub_data ub where ub.id_data not in ( select distinct id_gub from cplt_ub_data where id_gub > 0 ) ;
insert into cplt_sv_data_group (id_data, id_group) select sv.id_data, sv.id_owner from cplt_sv_data sv ;
insert into cplt_sv_data_group (id_data, id_group) select sv.id_data, 1 from cplt_sv_data sv ;
insert into cplt_sv_data_group (id_data, id_group) select sv.id_data, gub.id_group from cplt_sv_data sv join cplt_ub_data ub on sv.id_ub = ub.id_data join cplt_ub_data gub on gub.id_data = ub.id_gub ;

insert into cplt_dmnd_data (id_owner, id_suivi, montant, montant_scsp, montant_convention, date_creation, etat, verrou, budget) 
select sv.id_owner, sv.id_data, 0, 0, 0, now(), 476, 11, 489 from cplt_sv_data sv ;
insert into cplt_dmnd_data_group (id_data, id_group) select d.id_data, d.id_owner from cplt_dmnd_data d ;
insert into cplt_dmnd_data_group (id_data, id_group) select d.id_data, 1 from cplt_dmnd_data d ;
insert into cplt_dmnd_data_group (id_data, id_group) select d.id_data, gub.id_group from cplt_dmnd_data d join cplt_sv_data sv on d.id_suivi = sv.id_data join cplt_ub_data ub on sv.id_ub = ub.id_data join cplt_ub_data gub on gub.id_data = ub.id_gub ;

insert ignore into cplt_dpns_data (id_owner, id_demande, activite, montant, montant_scsp, montant_convention) 
select ub.id_group, dmnd.id_data,
 (select a.id_data from cplt_act_data a where concat('[', a.activite, '] ', a.sousactivite) = ec.ec_activite) as activite, 0 as montant, 0 as montant_scsp, 0 as montant_convention
from table_des_EC ec 
join cplt_pj_group g ON ec.idub = g.name
join cplt_ub_data ub ON g.id_group = ub.id_group 
join cplt_sv_data sv ON ub.id_data = sv.id_ub 
join cplt_dmnd_data dmnd ON dmnd.id_suivi = sv.id_data
where sv.annee = @year 
  and coalesce(dmnd.verrou, 11) = 11 ;
  
insert ignore into cplt_dpns_data_group( id_data, id_group ) select id_data, id_owner from cplt_dpns_data ;
insert ignore into cplt_dpns_data_group( id_data, id_group ) select id_data, 1 from cplt_dpns_data ;
insert ignore into cplt_dpns_data_group( id_data, id_group ) select dpns.id_data, gub.id_group from cplt_dpns_data dpns join cplt_ub_data ub on dpns.id_owner = ub.id_group join cplt_ub_data gub on ub.id_gub = gub.id_data ;

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

insert ignore into cplt_dmp_data_group (id_data, id_group) select id_data, id_owner from cplt_dmp_data ;
insert ignore into cplt_dmp_data_group (id_data, id_group) select id_data, 1 from cplt_dmp_data ;
insert ignore into cplt_dmp_data_group (id_data, id_group) select dmp.id_data, gub.id_group from cplt_dmp_data dmp join cplt_ub_data ub on dmp.id_owner = ub.id_group join cplt_ub_data gub on ub.id_gub = gub.id_data ;
