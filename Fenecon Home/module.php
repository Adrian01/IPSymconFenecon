<?

// Klassendefinition
class Fenecon extends IPSModule {

   


    // Überschreibt die interne IPS_Create($id) Funktion
    public function Create() {
        // Diese Zeile nicht löschen.
        parent::Create();


        //Prüfen der Variablen Profile und erstellen wenn diese nicht vorhanden sind
        
        if(!IPS_VariableProfileExists('fho.sys_state'))

        {
            IPS_CreateVariableProfile("fho.sys_state", 1);
            IPS_SetVariableProfileAssociation("fho.sys_state", 1, "System OK","",-1);
            IPS_SetVariableProfileAssociation("fho.sys_state", 2, "System Info","",-1);
        }

        if(!IPS_VariableProfileExists('fho.active_power'))

        {
            IPS_CreateVariableProfile("fho.active_power", 1);
            IPS_SetVariableProfileText("fho.active_power", ""," W"); 
        }

        if(!IPS_VariableProfileExists('fho.energy'))

        {
            IPS_CreateVariableProfile("fho.energy", 1);
            IPS_SetVariableProfileText("fho.energy", ""," Wh"); 
        }

        if(!IPS_VariableProfileExists('fho.energy_kWh'))

        {
            IPS_CreateVariableProfile("fho.energy_kWh", 1);
            IPS_SetVariableProfileText("fho.energy_kWh", ""," kWh"); 
        }


        if(!IPS_VariableProfileExists('fho.percent'))

        {
            IPS_CreateVariableProfile("fho.percent", 1);
            IPS_SetVariableProfileText("fho.percent", ""," %"); 
            IPS_SetVariableProfileValues("fho.percent", 0,100,1);
        }




        // Auslesen der form.jon Felder
        
        $this->RegisterPropertyBoolean("activModule", false);
        $this->RegisterPropertyBoolean("counter_kWh", false);
        $this->RegisterPropertyString("ip","ip-adresse");
        $this->RegisterPropertyString("username","Feld kann leer bleiben!");
        $this->RegisterPropertyString("password","passwort");
        $this->RegisterPropertyInteger("UpdateIntervall", 0);



    /****************************************************************************************************
     * Erstellen der Status Variablen
    ****************************************************************************************************/

        //Variablen Systemzustand
         $this->RegisterVariableInteger("State", "Zustand", "fho.sys_state");


        //Variablen Strombezug
        $this->RegisterVariableInteger("ConsumptionActivePower", "Verbrauch", "fho.active_power");
        $this->RegisterVariableInteger("ConsumptionActivePowerL1", "Verbrauch L1", "fho.active_power");
        $this->RegisterVariableInteger("ConsumptionActivePowerL2", "Verbrauch L2", "fho.active_power");
        $this->RegisterVariableInteger("ConsumptionActivePowerL3", "Verbrauch L3", "fho.active_power");
        $this->RegisterVariableInteger("ConsumptionActiveEnergy", "Verbrauchszähler gesamt", "fho.energy");


        //Variablen Netzeinspeisepunkt
        $this->RegisterVariableInteger("GridActivePower", "Wirkleistung Netz", "fho.active_power");
        $this->RegisterVariableInteger("GridActivePowerL1", "Wirkleistung Netz L1", "fho.active_power");
        $this->RegisterVariableInteger("GridActivePowerL2", "Wirkleistung Netz L2", "fho.active_power");
        $this->RegisterVariableInteger("GridActivePowerL3", "Wirkleistung Netz L3", "fho.active_power");
        $this->RegisterVariableInteger("GridSellActiveEnergy", "Netzeinspeisung", "fho.energy");
        $this->RegisterVariableInteger("GridBuyActiveEnergy", "Netzbezug gesamt", "fho.energy");
    



        //Variablen PV-Erzeuger
        $this->RegisterVariableInteger("ProductionActivePower", "Produktion aktuell", "fho.active_power");
        $this->RegisterVariableInteger("ProductionAcActivePower", "Produktion aktuell AC", "fho.active_power");
        $this->RegisterVariableInteger("ProductionDcActivePower", "Produktion aktuell DC", "fho.active_power");
        $this->RegisterVariableInteger("ProductionActiveEnergy", "Produktion gesamt", "fho.energy");


        //Variablen Speichersystem
        $this->RegisterVariableInteger("EssSoc", "Ladezustand Speicher", "fho.percent");
        $this->RegisterVariableInteger("EssActivePower", "Wirkleistung Speicher", "fho.active_power");     
        $this->RegisterVariableInteger("EssActiveChargeEnergy", "Speicherbeladung", "fho.active_power");
        $this->RegisterVariableInteger("EssActiveDischargeEnergy", "Speicherentladung", "fho.active_power");


        //TESTING
      
       
        

        // Erstellen des Updatetimers

        $this->RegisterTimer("Update", 0, 'FHO_update(' . $this->InstanceID . ');');




    }

    // Überschreibt die intere IPS_ApplyChanges($id) Funktion
    public function ApplyChanges() {
        // Diese Zeile nicht löschen
        parent::ApplyChanges();

        if(($this->ReadPropertyBoolean("counter_kWh")) == true)
            {

                $this->RegisterVariableInteger("GridBuyActiveEnergy_kWh", "Verbrauchszähler gesamt", "fho.energy_kWh");
            }


        $this->activateModule();




    }
    /**
    * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
    * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wiefolgt zur Verfügung gestellt:
    *
    * ABC_MeineErsteEigeneFunktion($id);
    *
    */


    private function fems_connect($value)

    {
        $ip = ($this->ReadPropertyString("ip"));
        $user = ($this->ReadPropertyString("username"));
        $pw = ($this->ReadPropertyString("password"));
    
        $raw_data = file_get_contents ("http://$user:$pw@$ip:80/rest/channel/_sum/$value");
        return $raw_data;

    }


    private function checkConnection()

    {
        $ip = ($this->ReadPropertyString("ip"));
        $user = ($this->ReadPropertyString("username"));
        $pw = ($this->ReadPropertyString("password"));
    
        $json_info = file_get_contents ("http://$user:$pw@$ip:80/rest/channel/_sum/State");
        
        if($json_info == true)
        {
    
            $this->SetStatus(102);
            $this->LogMessage("Verbindung zu FEMS erfolgreich hergestellt", KL_MESSAGE);
            return true;
        }
            elseif($json_info == false)
            {
                $this->SetStatus(200);
                $this->LogMessage("Verbindung zu FEMS konnte nicht hergestellt werden, bitte Zugangsdaten prüfen!", KL_ERROR);
                return false;
            }

    }



    //Funktion um das Modul bzw. die Abfrage des FEMS-Systems Ein- und Auszuschalten

    private function activateModule()

    {
        if(($this->ReadPropertyBoolean("activModule")) == true)

        {
            
            $this->SetTimerInterval("Update", $this->ReadPropertyInteger("UpdateIntervall") * 1000);
            $this->checkConnection();
     
           
        }

        elseif(($this->ReadPropertyBoolean("activModule")) == false)
        {

            $this->SetTimerInterval("Update", 0);
            $this->SetStatus(104);


        }

    }
    

    //Funtkion um die Werte des FEMS-Systems auszulesen

    public function update()

    {
    
        $connected = $this->checkConnection();
        $stats = array(
        
            "State",
            "ConsumptionActivePower",
            "ConsumptionActivePowerL1",
            "ConsumptionActivePowerL2",
            "ConsumptionActivePowerL3",
            "ConsumptionActiveEnergy",
            "GridActivePower",
            "GridActivePowerL1",
            "GridActivePowerL2",
            "GridActivePowerL3",
            "GridSellActiveEnergy",
            "GridBuyActiveEnergy",
            "ProductionActivePower",
            //"ProductionAcActivePower",
            //"ProductionDcActivePower",
            "ProductionActiveEnergy",
            "EssSoc",
            "EssActivePower",
            "EssActiveChargeEnergy",
            "EssActiveDischargeEnergy"
   
        );


        if($connected == true)

        {
            foreach ($stats as $key=>$value)

            {
                    $json = json_decode($this->fems_connect($value));
                    $this->SetValue($value,($json->value));
            }

            if(($this->ReadPropertyBoolean("counter_kWh")) == true)

            {
                $GridBuyActiveEnergy=IPS_GetObjectIDByIdent("GridBuyActiveEnergy", $this->InstanceID);
                $this->SetValue("GridBuyActiveEnergy_kWh", GetValue($GridBuyActiveEnergy) / 1000);
              
            }
    
    
            elseif($connected == false)

            {
                $this->LogMessage("Das ist ein Test!", KL_ERROR);
            }
    
        }

    }

}

            //$json_info = file_get_contents ("http://$user:$pw@$ip:80/rest/channel/_sum/$value");
            //$info = json_decode($json_info);
            //$this->SetValue($value,($info->value));

?>