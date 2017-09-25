<?php

/**
 * Ne pas oublier de renommer le fichier et le nom de la classe
 */
class Mailmerge_Test extends Mailmerge_Mailing {

	/**
	 * Il ne faut pas modifier cette fonction
	 *
	 * @param string $sMailMergeUrl Adresse du webservice de mailmerge, sous la forme: https://[login]:[password]@mailmerge.voozanoo.net
	 * @param string $iIdData identifiant du modèle en base de données
	 */
	public function __construct( $sMailMergeUrl, $iIdModel = null )
	{
		parent::__construct( $sMailMergeUrl, $iIdModel );
		$this->_sModelFileName = $sMailMergeUrl;
	}


	/**
	 * @return array
	 */
	protected function _getData()
	{
		/**
		 * IMPORTANT :
		 * Les paramètres que vous recevez sont stockés dans la variable $this->_aRequestParams
		 * Si votre widget est :
		 * <button label="Mailmerge" action="save" class="btn-primary col-xs-6 col-xs-offset-3">
				<option output="html" option_name="widget" value="WidgetButtonMailmerge"/>
				<redirection module="mailmerge" ctrl="index" action="exec">
					<params>
						<value alias="id_mm">330</value>
						<value alias="class">Test</value>
						<value alias="mon_param">Ceci est un texte</value>
						<dataset dataset_name="ub" field="id_data" alias="id_ub"/>
						<dataset dataset_name="ub" field="label" alias="label"/>
					</params>
				</redirection>
		  </button>
		 *
		 * => Vous pourrez exploiter les paramètres grâce à leur alias :
		 * $this->_aRequestParams['id_mm']
		 * $this->_aRequestParams['class']
		 * $this->_aRequestParams['mon_param']
		 * $this->_aRequestParams['id_ub']
		 * $this->_aRequestParams['label']
		 */

		$oProject = Core_Library_Account::GetInstance()->GetCurrentProject();
		$oDb = $oProject->Db();

		/**
		 * Vérification des paramètres reçus
		 * Ajoutez tous les paramètres dont vous aurez besoin pour votre traitement
		 * Exemple : $aVariables = array( 'id_ub', 'param1', 'annee' );
		 */
		$aVariables = array( 'id_ub' );

		/**
		 * Le code suivant sert à renvoyer une erreur si certains des paramètres attendus ne sont pas présents
		 * Il n'y a pas besoin de modifier cette partie
		 */
		$aMissingVars = array();
		foreach( $aVariables as $sVar ){
			if( FALSE === array_key_exists( $sVar, $this->_aRequestParams ) ){
				$aMissingVars[] = $sVar;
			}
		}
		if( count( $aMissingVars) > 0 ){
			throw new Exception( sprintf( 'Erreur : paramètres manquants : %s', implode( ', ', $aMissingVars ) ) );
		}

		/**
		 * Récupération des données
		 *
		 * Dans l'exemple on récupère les données de l'UB et les demandes qui lui sont associées
		 * On fait ça en deux temps :
		 * 	- On récupère les données de l'UB via une requête
		 *  - On récupère les données des demandes dans une 2e requête (qui contiendra potentiellement plusieurs résultats)
		 *
		 * Chaque tableau du document devra faire l'objet d'une nouvelle requête pour récupérer les données le concernant
		 *
		 */

		/**
		 * La construction d'une requête se fait comme suit :
		 * $oSelect = $oDb
			->select()
			->from( array( #table sur laquelle on base la requête#), array( #colonnes de la table# ) )
			->joinLeft( array( #table sur laquelle faire la jointure# ), "#condition sur laquelle faire la jointure#", array( #colonnes de la table# ) )
			->where( "#condition au format SQL#" )
			->where( '#condition ajoutée avec un AND#')
			->orWhere( '#condition ajoutée avec un OR#')
			->group( '#colonne sur laquelle grouper les valeurs#') //optionnel
			->order( '#colonne sur laquelle trier les données#') //optionnel
			->limit( '#nombre de résultats à récupérer#'); //optionnel
		 *
		 * Notez que la ligne joinLeft est optionelle, et il est possible d'en ajouter plusieurs
		 *
		 * Chaque paramètre peut s"écrire de la forme array( "alias" => valeur )
		 * Pour le from on pourra donc avoir :
		 * ->from( array( "d" => "cplt_dmnd_data" ), array( "identifiant" => "d.id_data", "montant" => "d.montant") )
		 * Dans l'exemple, le nom de la table est récupéré de cette façon : Core_Library_TName::GetVarsetData( 'ub' )
		 * Les deux solutions sont possibles, il suffit de mettre entre guillemets le nom du varset cible
		 *
		 *
		 * IMPORTANT : pour récupérer le résultat d'une requête, il faut utiliser soit la fonction "fetch", soit la fonction "fetchAll"
		 * Dans le cas d'un tableau (ici la liste des demandes), il faudra utiliser un fetchAll
		 *
		 * "fetch" ne renverra qu'un seul enregistrement sous forme de tableau :
		 * array [	"id_data" => 1,
		 * 			"montant" => 2564 ]
		 *
		 * "fetchAll" renverra un tableau avec tous les enregistrements correspondants :
		 * array[
		 * 		[0] => array[ "id_data" => 1,
		 * 					"montant" => 2564 ]
		 * 		[1] => array[ "id_data" => 23,
		 * 					"montant" => 0]
		 * 		[2] => array[ "id_data" => 54,
		 * 					"montant" => 142 ]
		 * 		[3] => array[ "id_data" => 652,
		 * 					"montant" => 7841 ]
		 * ]
		 *
		 * Récupération du fetch : utiliser une nouvelle variable (ici $aTableauFetch )
		 * $aTableauFetch = $oSelect->query()->fetch();
		 *
		 *
		 * Récupération du fetchAll : utiliser une nouvelle variable (ici $aTableauFetchAll )
		 * $aTableauFetchAll = array( '#Array-details#' => $oSelect->query()->fetchAll());
		 * IMPORTANT : #Array-details# correspond au nom du tableau que vous avez défini dans le .docx
		 *
		 */

		//UB service informatique :
		$oSelect = $oDb
			->select()
			->from( array( "ub" => Core_Library_TName::GetVarsetData( 'ub' ) ), array( 'id_data' => 'id_data', 'ub-libelle' => 'libelle' ) )
			->joinLeft(array( "g" => Core_Library_TName::GetPjGroup( $oProject ) ), 'g.id_group = ub.id_group', array( 'ub-id_group' => 'name' ) )
			->where('ub.id_data = ?', $this->_aRequestParams[ 'id_ub' ]);

		$aUb = $oSelect->query()->fetch();


		//Demandes : avec jointure sur suivi car on a la structure suivante : UB > suivi > demande
		$oSelect = $oDb
			->select()
			->from( array( "sv" => Core_Library_TName::GetVarsetData( 'sv' ) ), array( 'suivi-annee' => 'annee' ) )
			->joinLeft(array( "dm" => Core_Library_TName::GetVarsetData( 'dmnd' ) ), 'dm.id_suivi = sv.id_data', array( 'demande-montant_scsp' => 'montant_scsp', 'demande-montant_convention' => 'montant_convention' ) )
			->where('sv.id_ub = ?', $this->_aRequestParams[ 'id_ub' ]);

		$aDemandes = array( 'Array-details' => $oSelect->query()->fetchAll());


		/**
		 * Exemple de récupération des données d'un dataquery
		 * Dataquery utilisé pour l'exemple : id - 98 | DQ_liste_des_ub
		 */
		$this->_aRequestParams[ 'id_dq' ] = 98; // Supprimer cette ligne si l'identifiant est passé en paramètre

		$oDataQuery = new Core_Library_Resource_XML_DataQuery();

		/**
		 * Si le dataquery attend des paramètres (dans une condition par exemple), il faut les initialiser de cette façon
		 */
		$aParams = array( 	'param1' => 'value1',
							'param2' => $this->_aRequestParams[ 'paramduwidget' ] );
		$oParams = new Core_Library_Resource_XML_DataQuery_Params($oDataQuery, $aParams);
		$oDataQuery->SetParams( $oParams );

		
		$oDataQuery->LoadFromDB( $oProject, $this->_aRequestParams[ 'id_dq' ] );
		$oSelect = $oDataQuery->ToZendDBSelect();

		$aDq = $oSelect->query()->fetchAll();


		/**
		 * Cette ligne sert à rassembler toutes les données qui seront envoyées au document Mailmerge pour l'affichage
		 * Il faut y ajouter tous les tableau issus des résultats des requêtes
		 *
		 * Il est possible d'y ajouter des champs fixes :
		 * Par exemple si je veux ajouter dans le docx un champ de fusion appelé "prenom" qui contient "Geoffrey", je peux l'ajouter de cette manière :
		 * $aData = array_merge( $aUb, $aDemandes, $aDq, array( "prenom" => "Geoffrey", "nom" => "Liv" ) );
		 *
		 * S'il y a un paramètre que vous souhaitez afficher directement, il est possible de l'ajouter : Par ex "id_ub"
		 * $aData = array_merge( $aUb, $aDemandes, $aDq, array( "prenom" => "Geoffrey", "nom" => "Liv", "identifiant" => $this->_aRequestParams['id_ub'] ) );
		 */
		$aData = array_merge($aUb, $aDemandes, $aDq);


		return $aData;
	}
}


