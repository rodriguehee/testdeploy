insert into copilote.cplt_dico_data (id_owner, dico_name, label, position, code )
select 2, idliste, libelle, idcomm, valeur1
from copilote.lci01 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lci02 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lg01 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lg02 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lg03 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lg09 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lpe01 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lrh01 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lrh02 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lrh03 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lrh04 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lsi01 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lsi02 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lsi03 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lsi04 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lsi05 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lsi06 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lsi07 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lsi08 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lsi09 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lsi10 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lsi11 union 
select 2, idliste, libelle, idcomm, valeur1
from copilote.lsi12 ;

insert into copilote.cplt_dico_data_group (id_data, id_group)
select d.id_data, 1 
from copilote.cplt_dico_data d 
left join copilote.cplt_dico_data_group dg
 on d.id_data = dg.id_data and dg.id_group = 1
where dg.id_data_group is null ;
