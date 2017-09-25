<?php

abstract class Mailmerge_Mailing {

	/**
	 * Chemin vers le xsd de validation des données contenues dans le XML
	 * @string
	 */
	protected $_sXsdFilePath;
	protected $_sWebServiceUrl;
	protected $_sModelFileName;
	protected $_iIdModel;
	protected $_aRequestParams;

	/**
	 *
	 * @param string $sMailMergeUrl Adresse du webservice de mailmerge, sous la forme: https://[login]:[password]@mailmerge.voozanoo.net
	 * @throws Exception
	 */
	public function __construct( $iIdModel ) {
		$this->_sXsdFilePath = sprintf('%s/resources/xsd/mailmerge/mailing_data.xml', APPLICATION_PATH);
		if ( ! file_exists( $this->_sXsdFilePath ) ) {
			throw new Exception( sprintf( "Xsd file %s not found", $this->_sXsdFilePath ) );
		}

		$sUrl = Core_Library_Options::get( 'mailmerge.url' );
		// Ajout dans l'url du nom du projet et du webservice
		$sUrl = rtrim( $sUrl, '/' ) . '/mailmerge/ws/mailing';
		$sLogin = Core_Library_Options::get( 'mailmerge.login' );

		// mailmerge.password
		$sPassword = Core_Library_Options::get( 'mailmerge.password' );

		// Ajout du login et du mot de passe dans l'adresse
		if ( false === preg_match( '#(https://|http://)(.*)#', $sUrl, $aMatches ) ) {
			throw new Core_Library_Exception( sprintf( "Can't parse url %s, preg match error", $sUrl ) );
		}

		if ( ! isset( $aMatches[ 1 ] ) || ! isset( $aMatches[ 2 ] ) ) {
			throw new Core_Library_Exception( sprintf( "Can't parse url %s", $sUrl ) );
		}

		$sUrl = sprintf( "%s%s:%s@%s", $aMatches[1], $sLogin, $sPassword, $aMatches[2] );

		$this->_iIdModel = $iIdModel;
		$this->_sWebServiceUrl = $sUrl;
	}

	/**
	 * Récupération du modéle. L'attribut _sModelFileName indique ou trouver
	 * le modéle, il doit être sette en amont (dans le constructeur par exemple)
	 * @return string Model zippé encodé en base64
	 */
	protected function _getModel(  ){

		if ( ! isset( $this->_sModelFileName ) ) {
			throw new Exception( "Please set model path" );
		}

		if ( ! file_exists( $this->_sModelFileName ) ) {
			throw new Exception( sprintf( "Model not found (path: %s)", $this->_sModelFileName ) );
		}

		$sModelFileName = $this->_sModelFileName;

		$sTempDir = sys_get_temp_dir();

		$sZipFile = $sTempDir . '/' . uniqid() . '.zip';

		$zip = new ZipArchive();
		if ( ! $zip->open( $sZipFile, ZipArchive::CREATE ) ) {
			throw new Exception( sprintf( "Zip open error: %s", $sZipFile ) );
		}

		$zip->addFile( $sModelFileName, 'model.docx' );
		$zip->close();

		$sBase64File = base64_encode( file_get_contents( $sZipFile ) );

		unlink($sZipFile);

		return $sBase64File;
	}

	/**
	 *
	 * @param SimpleXMLElement $oXml
	 * @throws Exception
	 */
	protected function _schemaValidate( SimpleXMLElement $oXml )
	{
		$oDomDocument = new DOMDocument();
		if ( ! $oDomDocument->loadXML( $oXml->asXML() ) ) {
			throw new Exception( "DOMDocument, can't load XML" );
		}

		if ( ! $oDomDocument->schemaValidate( $this->_sXsdFilePath ) ) {
			throw new Exception( "Xml bad format" );
		}
	}

	/**
	 * Génére un mailing à partir d'un modéle et d'un set de données
	 * @return string Fichier renvoyé par mailmerge
	 */
	public function get( $aParams = array() ) {
		$this->_aRequestParams = $aParams;

		// Récupération du fichier XML contenant les données à transmettre
		$oXml = $this->_getXml();

		// Validation du XML
		$this->_schemaValidate( $oXml );

		// Création du client pour communiquer avec le WS de mailmerge
		$oClient = new Zend_Rest_Client( $this->_sWebServiceUrl );

		// Transformation du XML en zip encodé en base 64
		$aDataZip = $this->_prepareData($oXml);

		$aParams = array(
			"data"				=>	$aDataZip[ 'sBase64Zip' ],
			"data_hash"			=>	$aDataZip[ 'sMD5Zip' ],
			"processing_mode"	=>	"synchrone"
		);

		if( $this->_iIdModel ){
			$aParams[ 'id_model' ] = $this->_iIdModel;
		}

		// POST vers le webservice qui va renvoyer le document
		$oResult = $oClient->restPost( "/mailmerge/ws/mailing", $aParams );

		/* @var $oResult Zend_Rest_Client_Result */
		if ( 200 != $oResult->getStatus() ) {
			throw new Exception( sprintf( "Mailmerge return error: %s", $oResult->getBody() ) );
		}


		$sZipString = $this->_getZipStringFromResult( $oResult->getBody() );

		$sFile = $this->_getFileInZip( base64_decode($sZipString) );

		return $sFile;
	}

	public function _getZipStringFromResult( $sData )
	{
		// Récupération du fichier généré
		$sZipString = simplexml_load_string($sData);

		if($sZipString === null || $sZipString->response->generated_file == null){
			throw new Exception( sprintf( "Mailmerge return error: %s", $sData ) );
		}

		return (string)$sZipString->response->generated_file;
	}

	/**
	 * Transforme le XML en string zipé puis l'encode en Base 64 et en md5
	 *
	 * @param SimpleXMLElement $oXml
	 * @return array
	 * @throws Exception
	 */
	protected function _prepareData( SimpleXMLElement  $oXml ) {

		$sTempDir = sys_get_temp_dir();

		$sZipFile = $sTempDir . '/' . uniqid() . '.zip';

		$zip = new ZipArchive();
		if ( ! $zip->open( $sZipFile, ZipArchive::CREATE ) ) {
			throw new Exception( sprintf( "Zip open error: %s", $sZipFile ) );
		}

		$zip->addFromString('data.xml', $oXml->asXML());
		$zip->close();

		$sZip = file_get_contents($sZipFile);
		unlink($sZipFile);

		$oResult = array(
			"sBase64Zip"	=>	base64_encode( $sZip ),
			"sMD5Zip"		=>	md5( $sZip )
		);

		return $oResult;
	}

	/**
	 * Print XML
	 */
	public function PrintXml(){

		$oXml = $this->_getXml();

		$this->_schemaValidate( $oXml );

		Header('Content-type: text/xml');
		echo $oXml->asXML();
	}

	/**
	 *
	 * @param string $sZipString contenu du zip
	 * @return string
	 */
	protected function _getFileInZip($sZipString){

		$sTempDir = sys_get_temp_dir();

		$sTempDir = $sTempDir . '/' . uniqid();

		if( ! mkdir( $sTempDir ) ) {
			throw new Exception( sprintf( "Error create dir : %s", $sTempDir ) );
		}

		$sZipFile = $sTempDir . DIRECTORY_SEPARATOR . uniqid() . '.zip';

		if( ! file_put_contents( $sZipFile, $sZipString ) ) {
			throw new Exception( sprintf("Error put contents : ", $sZipString));
		}

		$oZip = New ZipArchive();
		if ( ! $oZip->open( $sZipFile ) ) {
			throw new Exception( sprintf( "Zip open error: %s", $sZipString ) );
		}

		$oZip->extractTo( $sTempDir );
		$oZip->close();

		unlink( $sZipFile );

		$rDir = opendir( $sTempDir );
		if ( ! $rDir ) {
			throw new Exception( sprintf( "Can't open dir %s", $sTempDir ) );
		}

		$sFile = false;
		while ( false !== ( $sEntry = readdir( $rDir ) ) ) {
			if ( $sEntry != '.' && $sEntry != '..' ) {
				if ( is_file( $sTempDir . '/' . $sEntry ) ) {
					// On prend le premier fichier
					if ( ! $sFile ) {
						$sTmp = file_get_contents( $sTempDir . '/' . $sEntry );
						if ( ! $sTmp ) {
							throw new Exception( sprintf( "Can't read file %s", $sTempDir . '/' . $sEntry ) );
						}
						$sFile = $sTmp;
					}
					unlink( $sTempDir . '/' . $sEntry );
				}
			}
		}

		closedir( $rDir );

		rmdir( $sTempDir );

		if ( ! $sFile ) {
			throw new Exception( "File not found in Zip" );
		}

		return $sFile;
	}

	/**
	 * Get XML for mailmerge
	 * @return \SimpleXMLElement
	 */
	public function _getXml()
	{
		$aData = $this->_getData();
		$oNewDataXML = new SimpleXMLElement('<Mailing id="1" batchId="1" xmlns="http://tempuri.org/XMLSchema.xsd"></Mailing>');

		$oNewRow = $oNewDataXML->addChild('Mail');
		$oNewRow->addAttribute('id', 0);

		foreach ( $aData as $sField => $mValue ) {
			if ( ! is_array( $mValue ) ) {
				$oNewField = $oNewRow->addChild( 'Field', $mValue );
				$oNewField->addAttribute( "name", $sField );
				$oNewField->addAttribute( "type", 'string' );
			}
		}

		foreach ( $aData as $sTableName => $mValue ) {
			if ( is_array( $mValue ) && count( $mValue ) ) {

				$oNewTable = $oNewRow->addChild( 'Table' );
				$oNewTable->addAttribute( "name", $sTableName );

				foreach ( $mValue as $aRow ) {
					$oNewRowTable = $oNewTable->addChild( "TableRow" );

					foreach( $aRow as $sField => $sValue ) {
						$oNewField = $oNewRowTable->addChild( 'Field', $sValue );
						$oNewField->addAttribute( "name", $sField );
						$oNewField->addAttribute( "type", 'string' );
					}
				}
			}
		}

		return $oNewDataXML;
	}
}
