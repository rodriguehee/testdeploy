<?php

class Copilote_Library_Arbitrage
{

    function getMontantC5($iIdDemande){
        $montant = 0;
        $aIdDepenses = $this->getIdDepenses($iIdDemande);
        foreach($aIdDepenses as $iIdDepense){
            $depense = new Copilote_Library_Depense( "cplt_dpns_data", $iIdDepense ) ;
            $montant += $depense->getMontant( "personnel", "rh", "ae", "scsp" );
        }
        return (float) $montant;
    }

    function getMontantD5($iIdDemande){
        $montant = 0;
        $aIdDepenses = $this->getIdDepenses($iIdDemande);
        foreach($aIdDepenses as $iIdDepense){
            $depense = new Copilote_Library_Depense( "cplt_dpns_data", $iIdDepense ) ;
            $montant += $depense->getMontant( "fonctionnement", "dt", "ae", "scsp" ) +
                $depense->getMontant( "fonctionnement", "sta", "ae", "scsp" ) +
                $depense->getMontant( "fonctionnement", "oc", "ae", "scsp" ) +
                $depense->getMontant( "fonctionnement", "pe", "ae", "scsp" ) +
                $depense->getMontant( "fonctionnement", "dmp", "ae", "scsp" );
        }
        return (float) $montant;
    }

    function getMontantE5($iIdDemande){
        $montant = 0;
        $aIdDepenses = $this->getIdDepenses($iIdDemande);
        foreach($aIdDepenses as $iIdDepense){
            $depense = new Copilote_Library_Depense( "cplt_dpns_data", $iIdDepense ) ;
            $montant += $depense->getMontant( "fonctionnement", "dt", "cp", "scsp" ) +
                $depense->getMontant( "fonctionnement", "sta", "cp", "scsp" ) +
                $depense->getMontant( "fonctionnement", "oc", "cp", "scsp" ) +
                $depense->getMontant( "fonctionnement", "pe", "cp", "scsp" ) +
                $depense->getMontant( "fonctionnement", "dmp", "cp", "scsp" );
        }
        return (float) $montant;
    }

    function getMontantF5($iIdDemande){
        $montant = 0;
        $aIdDepenses = $this->getIdDepenses($iIdDemande);
        foreach($aIdDepenses as $iIdDepense){
            $depense = new Copilote_Library_Depense( "cplt_dpns_data", $iIdDepense ) ;
            $montant += $depense->getMontant( "investissement", "pe", "ae", "scsp" ) +
                $depense->getMontant( "investissement", "dmp", "ae", "scsp" );
        }
        return (float) $montant;
    }

    function getMontantG5($iIdDemande){
        $montant = 0;
        $aIdDepenses = $this->getIdDepenses($iIdDemande);
        foreach($aIdDepenses as $iIdDepense){
            $depense = new Copilote_Library_Depense( "cplt_dpns_data", $iIdDepense ) ;
            $montant += $depense->getMontant( "investissement", "pe", "cp", "scsp" ) +
                $depense->getMontant( "investissement", "dmp", "cp", "scsp" );
        }
        return (float) $montant;
    }

    function getMontantC7($iIdDemande){
        $montant = 0;
        $aIdDepenses = $this->getIdDepenses($iIdDemande);
        foreach($aIdDepenses as $iIdDepense){
            $depense = new Copilote_Library_Depense( "cplt_dpns_data", $iIdDepense ) ;
            $montant += $depense->getMontant( "personnel", "rh", "ae", "convention" );
        }
        return (float) $montant;
    }

    function getMontantD7($iIdDemande){
        $montant = 0;
        $aIdDepenses = $this->getIdDepenses($iIdDemande);
        foreach($aIdDepenses as $iIdDepense){
            $depense = new Copilote_Library_Depense( "cplt_dpns_data", $iIdDepense ) ;
            $montant += $depense->getMontant( "fonctionnement", "dt", "ae", "convention" ) +
                $depense->getMontant( "fonctionnement", "sta", "ae", "convention" ) +
                $depense->getMontant( "fonctionnement", "oc", "ae", "convention" ) +
                $depense->getMontant( "fonctionnement", "pe", "ae", "convention" ) +
                $depense->getMontant( "fonctionnement", "dmp", "ae", "convention" );
        }
        return (float) $montant;
    }

    function getMontantE7($iIdDemande){
        $montant = 0;
        $aIdDepenses = $this->getIdDepenses($iIdDemande);
        foreach($aIdDepenses as $iIdDepense){
            $depense = new Copilote_Library_Depense( "cplt_dpns_data", $iIdDepense ) ;
            $montant += $depense->getMontant( "fonctionnement", "dt", "cp", "convention" ) +
                $depense->getMontant( "fonctionnement", "sta", "cp", "convention" ) +
                $depense->getMontant( "fonctionnement", "oc", "cp", "convention" ) +
                $depense->getMontant( "fonctionnement", "pe", "cp", "convention" ) +
                $depense->getMontant( "fonctionnement", "dmp", "cp", "convention" );
        }
        return (float) $montant;
    }

    function getMontantF7($iIdDemande){
        $montant = 0;
        $aIdDepenses = $this->getIdDepenses($iIdDemande);
        foreach($aIdDepenses as $iIdDepense){
            $depense = new Copilote_Library_Depense( "cplt_dpns_data", $iIdDepense ) ;
            $montant += $depense->getMontant( "investissement", "pe", "ae", "convention" ) +
                $depense->getMontant( "investissement", "dmp", "ae", "convention" );
        }
        return (float) $montant;
    }

    function getMontantG7($iIdDemande){
        $montant = 0;
        $aIdDepenses = $this->getIdDepenses($iIdDemande);
        foreach($aIdDepenses as $iIdDepense){
            $depense = new Copilote_Library_Depense( "cplt_dpns_data", $iIdDepense ) ;
            $montant += $depense->getMontant( "investissement", "pe", "cp", "convention" ) +
                $depense->getMontant( "investissement", "dmp", "cp", "convention" );
        }
        return (float) $montant;
    }

    function getIdDepenses($iIdDemande){
        $aIdDepenses = array();

        $db = Core_Library_Account::GetInstance()->GetCurrentProject()->Db() ;
        $sql = 'SELECT id_data FROM cplt_dpns_data WHERE id_demande = ?' ;
        $stmt = $db->query( $sql, $iIdDemande ) ;
        while( $idDepense = $stmt->fetchColumn( 0 ) ) {
            $aIdDepenses[] = $idDepense ;
        }

        return $aIdDepenses;
    }
}