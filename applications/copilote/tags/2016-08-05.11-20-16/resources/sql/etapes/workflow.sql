/* exemple de la fiche de suivi [1250] => demande [1252], lors du passage à l'état "arbitrée" */
set @status := 147 ;
set @id := 1252 ;

update cplt_dmnd_data set verrou = 10, etat = @status where id_data = @id ;

insert into cplt_dmnd_data ( id_owner, id_suivi, etat, montant, date_creation, date_modif, verrou ) 
select id_owner, id_suivi, etat, montant, current_datetime, current_datetime, 11 
from cplt_dmnd_data where id_data = @id ;

set @newid := last_insert_id() ;

insert into cplt_dpns_data ( id_owner, id_demande, activite, activite_plus, montant )
select id_owner, @new, activite, activite_plus, montant from cplt_dpns_data where id_demande = @id ;

/* etc  .... */
