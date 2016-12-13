<?php

class CopiloteCliApi extends CliApi
{
	/**
	 * Override parent method to force the project name
	 */
	protected function _initialize()
	{
		parent::_initialize();
		Zend_Registry::set(Core_Library_Account::PROJECT_VAR_NAME, 'copilote');
	}
	
	/**
	 * php cli.php mtd=computeProgrammations
	 */
	public function computeProgrammations()
	{
		try {
			$endOfLine = "\n";
			$endMessage = "Fin des calculs";
			
			$libPath = Core_Library_Options::get('lib.path');
			require $libPath . "/Record.php";
			require $libPath . "/Ub.php";
			require $libPath . "/Demande.php";
			require $libPath . "/Depense.php";
			require $libPath . "/Convention.php";
			require $libPath . "/Programmation.php";
			require $libPath . "/Mock.php";
				
			$project = Core_Library_Account::GetInstance()->GetCurrentProject();
			$user = $project->UserManager()->CreateSysUser();
			$project->Account()->SetCurrentUser($user);
			$dataQuery = null;
			$form = $project->GetFormFromSId("liste_conv_prog");
			foreach ($form->GetDataStructure()->GetDataQueries() as $dataQuery) {
				if ($dataQuery->getAttribute("id") == "conv") {
					break;
				}
			}
			if (! $dataQuery instanceof Core_Library_Resource_XML_DataQuery) {
				throw new LogicException("Dataquery 'conv' not found");
			}
			
			$statement = $dataQuery->ToZendDbSelect()->query() ;
			while ($row = $statement->fetch()) {
				echo sprintf("Calculs pour la convention '%s: %s'%s", $row['code_ined'], $row['libelle'], $endOfLine);
				$convention = new Copilote_Library_Convention("cplt_conv_data", $row['id_data']);
				$convention->computes();
			}
		}
		catch( Exception $ex ) {
			$endMessage = $ex->GetMessage();
		}
		finally {
			echo sprintf("%s%s", $endMessage, $endOfLine);
		}
	}
}