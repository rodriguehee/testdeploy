/* unite budgetaire */
alter table cplt_ub_data modify id_group int(10) unsigned default null ;
alter table cplt_ub_data add foreign key (id_group) references cplt_pj_group (id_group) ;
alter table cplt_ub_data modify id_structure int(10) unsigned default null ;
alter table cplt_ub_data add foreign key (id_structure) references cplt_pj_group (id_group) ;
alter table cplt_ub_data add foreign key (id_gub) references cplt_ub_data (id_data) ;

/* activite */
update cplt_act_data set id_ub = null where id_ub = 0 ;
alter table cplt_act_data add foreign key (id_ub) references cplt_ub_data (id_data) ;

/* convention */
alter table cplt_conv_data add foreign key (id_ub) references cplt_ub_data (id_data) ;

/* suivi */
alter table cplt_sv_data add foreign key (id_ub) references cplt_ub_data (id_data) ;

/* demande */
alter table cplt_dmnd_data add foreign key (id_suivi) references cplt_sv_data (id_data) ;

/* arbitrage */
alter table cplt_bdgt_data add foreign key (id_demande) references cplt_dmnd_data (id_data) ;

/* validation */
alter table cplt_vld_data add foreign key (id_demande) references cplt_dmnd_data (id_data) ;

/* depense */
alter table cplt_dpns_data add foreign key (id_demande) references cplt_dmnd_data (id_data) ;
alter table cplt_dpns_data add unique (id_demande, activite) ; 

/* support interne */
alter table cplt_si_data add foreign key (id_depense) references cplt_dpns_data (id_data) ;

/* mobilite internationale */
alter table cplt_mi_data add foreign key (id_depense) references cplt_dpns_data (id_data) ;
alter table cplt_mi_data add foreign key (mi_identite) references cplt_id_data (id_data) ;
alter table cplt_mi_data add foreign key (mi_destouresid) references cplt_pays_data (id_data) ;

/* deplacement temporaire */ 
alter table cplt_dt_data add foreign key (id_depense) references cplt_dpns_data (id_data) ;
alter table cplt_dt_data add foreign key (dt_identite) references cplt_id_data (id_data) ;
alter table cplt_dt_data add foreign key (dt_numconv) references cplt_conv_data (id_data) ;
alter table cplt_dt_data add foreign key (dt_lieudest) references cplt_pays_data (id_data) ;

/* grand colloque international */
alter table cplt_gci_data add foreign key (id_depense) references cplt_dpns_data (id_data) ;
alter table cplt_gci_data add foreign key (gci_identite) references cplt_id_data (id_data) ;

/* stagiaire */
alter table cplt_sta_data add foreign key (id_depense) references cplt_dpns_data (id_data) ;
alter table cplt_sta_data add foreign key (sta_impc1) references cplt_conv_data (id_data) ;
-- update cplt_sta_data set sta_impc2 = null where sta_impc2 = 0 ;
-- alter table cplt_sta_data add foreign key (sta_impc2) references cplt_conv_data (id_data) ; 

/* ressource humaine */
alter table cplt_rh_data add foreign key (id_depense) references cplt_dpns_data (id_data) ;
alter table cplt_rh_data add foreign key (rh_identite) references cplt_id_data (id_data) ;
alter table cplt_rh_data add foreign key (rh_impc1) references cplt_conv_data (id_data) ;
-- update cplt_rh_data set rh_impc2 = null where rh_impc2 = 0 ;
-- alter table cplt_rh_data add foreign key (rh_impc2) references cplt_conv_data (id_data) ; 

/* depense sur marche */
alter table cplt_dmp_data add foreign key (id_depense) references cplt_dpns_data (id_data) ;

/* prestation externe */
alter table cplt_pe_data add foreign key (id_depense) references cplt_dpns_data (id_data) ;

/* organisation de colloque */
alter table cplt_oc_data add foreign key (id_depense) references cplt_dpns_data (id_data) ;

/* ventilation */
alter table cplt_vntl_data add foreign key (id_dmp) references cplt_dmp_data (id_data) ;
alter table cplt_vntl_data add foreign key (id_oc) references cplt_oc_data (id_data) ;
alter table cplt_vntl_data add foreign key (id_pe) references cplt_pe_data (id_data) ;
alter table cplt_vntl_data add foreign key (convention) references cplt_conv_data (id_data) ;

