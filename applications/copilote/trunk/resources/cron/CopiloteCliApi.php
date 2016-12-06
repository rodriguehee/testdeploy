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
			$message = "Fin des calculs";
			
			$libPath = Core_Library_Options::get('lib.path');
			require $libPath . "/Record.php";
			require $libPath . "/Ub.php";
			require $libPath . "/Demande.php";
			require $libPath . "/Depense.php";
			require $libPath . "/Convention.php";
			require $libPath . "/Programmation.php";
			
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
				echo sprintf("Calculs pour la convention '%s: %s'\n", $row['code_ined'], $row['libelle']);
				$convention = new Copilote_Library_Convention("cplt_conv_data", $row['id_data']);
				$convention->computeProgrammations();
				foreach (array("ae", "cp") as $aspect) {
					$convention->computeAnteriority($aspect);
					$convention->computeFraisGestion($aspect);
					$convention->computeCreditOuvert($aspect);
					$convention->computeCreditConsomme($aspect);
					$convention->computeProvisionChomage($aspect);
					$convention->computeCreditDisponible($aspect);
				}
				$convention->commit();
			}
		}
		catch( Exception $ex ) {
			$message = $ex->GetMessage();
		}
		finally {
			echo sprintf("%s\n", $message);
		}
	}
}