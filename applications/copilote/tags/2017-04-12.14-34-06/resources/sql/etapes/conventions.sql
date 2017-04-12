delete from cplt_conv_data_group ;
delete from cplt_conv_data ;

insert into cplt_conv_data (id_owner, id_ub, libelle, formule, code_ined, bailleur, montant_ae, montant_cp, disponible_ae, disponible_cp, date_deb, date_fin)
select 2, ub.id_data, conv_libelle, conv_formule, conv_id, conv_bailleur, conv_montant_AE, conv_montant_CP, conv_disponible_AE, conv_disponible_CP, conv_date_deb, conv_date_fin
from liste_conventions_epi src
join cplt_pj_group g on src.id_ub = g.name
join cplt_ub_data ub on g.id_group = ub.id_group ;

insert into cplt_conv_data_group (id_data, id_group) select id_data, 1 from cplt_conv_data ;
insert into cplt_conv_data_group (id_data, id_group) select c.id_data, ub.id_group from cplt_conv_data c join cplt_ub_data ub on c.id_ub = ub.id_data ;

/* affectations de "has_conv" */
update cplt_ub_data set has_conv = 11 ;

select distinct concat( "update cplt_ub_data set has_conv = 10 where id_data = ", id_ub, " or id_gub = ", id_ub, " ;" ) 
into outfile "/home/rodrigue/tmp/hasconv.sql"
from cplt_conv_data ;

source /home/rodrigue/tmp/hasconv.sql

/* conventions non chargés pour recoder les valeurs de l'UB erronées*/
select src.*
from liste_conventions_epi src
left join cplt_pj_group g on src.id_ub = g.name
where g.id_group is null \G
