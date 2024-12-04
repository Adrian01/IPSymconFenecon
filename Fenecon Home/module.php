<?

class Fenecon extends IPSModule {

    private $baseURL;

    public function Create() 
    {
        parent::Create();

    /****************************************************************************************************
     * Erstellen der Variablen Profile
    ****************************************************************************************************/
        
        if(!IPS_VariableProfileExists('FH.SystemState'))

        {
            IPS_CreateVariableProfile("FH.SystemState", 1);
            IPS_SetVariableProfileAssociation("FH.SystemState", 0, "System OK","", 0x7ebf5e);
            IPS_SetVariableProfileAssociation("FH.SystemState", 1, "System Info","",0x538be0);
            IPS_SetVariableProfileAssociation("FH.SystemState", 2, "System Warnung","",0xe8da3f);
            IPS_SetVariableProfileAssociation("FH.SystemState", 3, "System Fehler","", 0xe05c53);
        }

        if(!IPS_VariableProfileExists('FH.GridMode'))

        {
            IPS_CreateVariableProfile("FH.GridMode", 1);
            IPS_SetVariableProfileAssociation("FH.GridMode", 0, "undefiniert","", -1);
            IPS_SetVariableProfileAssociation("FH.GridMode", 1, "Netzbetrieb","", -1);
            IPS_SetVariableProfileAssociation("FH.GridMode", 2, "Notstrom","", -1);
        }

        if(!IPS_VariableProfileExists('FH.CS_State'))

        {
            IPS_CreateVariableProfile("FH.CS_State", 1);
            IPS_SetVariableProfileAssociation("FH.CS_State", 0, "Ladevorgang Startet","", -1);
            IPS_SetVariableProfileAssociation("FH.CS_State", 1, "Nicht bereit zum Laden","", -1);
            IPS_SetVariableProfileAssociation("FH.CS_State", 2, "Kabel ist nicht angeschlossen","", -1);
            IPS_SetVariableProfileAssociation("FH.CS_State", 3, "Ladevorgang aktiv","", -1);
            IPS_SetVariableProfileAssociation("FH.CS_State", 4, "Fehler","", -1);
            IPS_SetVariableProfileAssociation("FH.CS_State", 5, "Ladevorgang abgelehnt","", -1);
            IPS_SetVariableProfileAssociation("FH.CS_State", 6, "Ladelimit erreicht","", -1);
            IPS_SetVariableProfileAssociation("FH.CS_State", 7, "Laden beendet","", -1);

        }

        if(!IPS_VariableProfileExists('FH.ActivPower'))

        {
            IPS_CreateVariableProfile("FH.ActivPower", 1);
            IPS_SetVariableProfileText("FH.ActivPower", ""," W"); 
        }

        if(!IPS_VariableProfileExists('FH.Energy'))

        {
            IPS_CreateVariableProfile("FH.Energy", 2);
            IPS_SetVariableProfileText("FH.Energy", ""," kWh");
            IPS_SetVariableProfileDigits("FH.Energy", 1); 
        }


        if(!IPS_VariableProfileExists('FH.Percent'))

        {
            IPS_CreateVariableProfile("FH.Percent", 1);
            IPS_SetVariableProfileText("FH.Percent", ""," %"); 
            IPS_SetVariableProfileValues("FH.Percent", 0,100,1);
        }

        
        if(!IPS_VariableProfileExists('FH.Capacity'))

        {
            IPS_CreateVariableProfile("FH.Capacity", 2);
            IPS_SetVariableProfileText("FH.Capacity", ""," kWh");
            IPS_SetVariableProfileDigits("FH.Capacity", 1);
        }

    /****************************************************************************************************
     * Erstellen der Moduleigenschaften
    ****************************************************************************************************/

        $this->RegisterPropertyBoolean("activModule", false);
        $this->RegisterPropertyBoolean("counter_kWh", false);
        $this->RegisterPropertyString("ip","");
        $this->RegisterPropertyString("username","");
        $this->RegisterPropertyString("password","");
        $this->RegisterPropertyInteger("UpdateIntervall", 10);
        $this->RegisterPropertyBoolean("activChargingStation", false);

    /****************************************************************************************************
     * Erstellen der Status Variablen
    ****************************************************************************************************/

        //Variablen Systemzustand
         $this->RegisterVariableInteger("State", "Systemzustand", "FH.SystemState", 1);
         $this->RegisterVariableInteger("GridMode", "Netzmodus", "FH.GridMode", 2);

        //Variablen Strombezug
        $this->RegisterVariableInteger("ConsumptionActivePower", "Momentanverbrauch", "FH.ActivPower", 4);
        $this->RegisterVariableInteger("ConsumptionActivePowerL1", "Momentanverbrauch L1", "FH.ActivPower", 5);
        $this->RegisterVariableInteger("ConsumptionActivePowerL2", "Momentanverbrauch L2", "FH.ActivPower", 6);
        $this->RegisterVariableInteger("ConsumptionActivePowerL3", "Momentanverbrauch L3", "FH.ActivPower", 7);
        $this->RegisterVariableFloat("ConsumptionActiveEnergy", "Gesamtverbrauch", "FH.Energy", 3);

        //Variablen Netzeinspeisepunkt
        $this->RegisterVariableInteger("GridActivePower", "Momentanleistung Netz", "FH.ActivPower", 10);
        $this->RegisterVariableInteger("GridActivePowerL1", "Momentanleistung Netz L1", "FH.ActivPower", 11);
        $this->RegisterVariableInteger("GridActivePowerL2", "Momentanleistung Netz L2", "FH.ActivPower", 12);
        $this->RegisterVariableInteger("GridActivePowerL3", "Momentanleistung Netz L3", "FH.ActivPower", 13);
        $this->RegisterVariableFloat("GridSellActiveEnergy", "Gesamteinspeisung Netz", "FH.Energy", 9);
        $this->RegisterVariableFloat("GridBuyActiveEnergy", "Gesamtbezug Netz", "FH.Energy", 8);

        //Variablen PV-Erzeuger
        $this->RegisterVariableInteger("ProductionActivePower", "PV-Produktion aktuell", "FH.ActivPower", 14);
        $this->RegisterVariableFloat("ProductionActiveEnergy", "PV-Produktion gesamt", "FH.Energy", 15);

        //Variablen Speichersystem
        $this->RegisterVariableInteger("EssSoc", "Ladezustand Speicher", "FH.Percent", 17);
        $this->RegisterVariableInteger("DcDischargePower", "Wirkleistung Speicher", "FH.ActivPower", 16);     
        $this->RegisterVariableFloat("EssDcChargeEnergy", "Beladung Speicher gesamt", "FH.Energy", 18);     
        $this->RegisterVariableFloat("EssDcDischargeEnergy", "Entladung Speicher gesamt", "FH.Energy", 19);     
        $this->RegisterVariableFloat("Capacity", "Kapazität Speichersystem", "FH.Capacity", 20); 
 

    /****************************************************************************************************
     * Erstellen der Timerfunktionen
    ****************************************************************************************************/

        $this->RegisterTimer("Update_State", 0, 'FHO_update(' . $this->InstanceID . ');');

    }



    private function createEVCS_Variables()
    {
        if(($this->ReadPropertyBoolean("activChargingStation")) == true)
        {
            $this->RegisterVariableInteger("Status", "Zustand Ladestation", "FH.CS_State", 21);
            $this->RegisterVariableInteger("ChargePower", "Ladeleistung", "FH.ActivPower", 22);
            $this->RegisterVariableFloat("EnergySession", "Enerige Ladevorgang", "FH.Energy", 23);
            $this->RegisterVariableFloat("ActiveConsumptionEnergy", "Gesamtverbrauch Ladestation", "FH.Energy", 24);
        }
    }
   

    
    public function ApplyChanges() 
    {
        parent::ApplyChanges();

        $connectionState = $this->checkConnection();
        $this->createEVCS_Variables();

        if($connectionState == true)

        {
            $this->activateModule();
        }

    }


    public function Destroy() 
    {
        parent::Destroy();

    }

    /****************************************************************************************************
     * Funktion zum updaten der Datenpunkte
    ****************************************************************************************************/

    public function update()

    {
        $this->getStorageValues();
        $this->getSumValues();

        if(($this->ReadPropertyBoolean("activChargingStation")) == true)
        {
            $this->getChargingStationValues();
        }

    }
    
    /****************************************************************************************************
     * Funktionen zum abrufen der verschiedenen Datenpunkte
    ****************************************************************************************************/

    //Datenpunkte verschiedener Systemkomponenten abrufen wie Energymeter, Wechselrichter, Speicher
    private function getSumValues()

    {
        $channel = "_sum/";
        $state = array(
        
            //Energymeter bzw. Netzteinspeisung
            "GridActivePower",
            "GridActivePowerL1",
            "GridActivePowerL2",
            "GridActivePowerL3",
            "GridSellActiveEnergy",
            "GridBuyActiveEnergy",

            //Erzeugung PV-Anlage
            "ConsumptionActiveEnergy",
            "ConsumptionActivePower",
            "ConsumptionActivePowerL1",
            "ConsumptionActivePowerL2",
            "ConsumptionActivePowerL3",
            "ProductionActiveEnergy",
            "ProductionActivePower",

            //Variablen Stromspeicher
            "EssSoc",
            "EssDcDischargeEnergy",
            "EssDcChargeEnergy",

            //Allegemeine Statusinformationen
            "State",
            "GridMode"

        );

        foreach ($state as $key => $value)
        {
            $response = $this->fems_connect($channel, $value);
            $json = json_decode($response, true);
            
            switch ($json['unit'])

            {

                case 'Wh_Σ':
                    $this->SetValue($value, $json['value'] / 1000);
                    break;

                case 'Wh':
                    $this->SetValue($value, $json['value'] / 1000);
                    break;

                default: 
                    $this->SetValue($value, $json['value']);
                    break; 
            } 
        }
    }

    //Datenpunkte der Ladestation abrufen
    private function getChargingStationValues()
    {
        $channel = "evcs0/";
        $state = array(
        
            "Status",
            "ChargePower",
            "EnergySession",
            "ActiveConsumptionEnergy"
        );

        foreach ($state as $key => $value)

        {
            $response = $this->fems_connect($channel, $value);
            $json = json_decode($response, true);

            switch ($json['unit'])

            {

                case 'Wh_Σ':
                    $this->SetValue($value, $json['value'] / 1000);
                    break;

                case 'Wh':
                    $this->SetValue($value, $json['value'] / 1000);
                    break;

                default: 
                    $this->SetValue($value, $json['value']);
                    break; 
            } 
        }      
    }


    //Datenpunkte des Sepichersystems abrufen
    private function getStorageValues()
    {
        $channel = "ess0/";
        $state = array(

            "DcDischargePower",
            "Capacity"

        );

        foreach ($state as $key => $value)

        {
            $response = $this->fems_connect($channel, $value);
            $json = json_decode($response, true);

            switch ($json['unit'])

            {

                case 'Wh_Σ':
                    $this->SetValue($value, $json['value'] / 1000);
                    break;

                case 'Wh':
                    $this->SetValue($value, $json['value'] / 1000);
                    break;

                default: 
                    $this->SetValue($value, $json['value']);
                    break; 
            } 
        }      
    }

    /****************************************************************************************************
     * Modul akvivieren und Verbindung prüfen
    ****************************************************************************************************/

    //Modul aktivieren/deaktivieren
    private function activateModule()

    {
        if(($this->ReadPropertyBoolean("activModule")) == true)

        {
            $this->SetTimerInterval("Update_State", $this->ReadPropertyInteger("UpdateIntervall") * 1000);
            $this->checkConnection();
        }

        elseif(($this->ReadPropertyBoolean("activModule")) == false)

        {
            $this->SetTimerInterval("Update_State", 0);
            $this->SetStatus(104);
        }
    }
        
    //prüfen ob Verbindung zu FEMS möglich ist
    private function checkConnection()

    {
        $this->get_baseURL();
        $url = $this->baseURL . "/_sum/State";
    
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    
        $raw_data = curl_exec($curl);
        curl_close($curl);
    
        $json = json_decode($raw_data, true);

    
        if(isset($json['value']) && $json['value'] !== null) 

        {
            $this->LogMessage("Verbindung zu FEMS erfolgreich hergestellt!", KL_NOTIFY);
            $this->SetStatus(102);
            return true;
        }
    

        elseif (isset($json['error']) && $json['error']['code'] == 1003) 
        
        {
            $this->LogMessage("Anmeldung fehlgeschlagen, Zugangsdaten prüfen!", KL_WARNING);
            $this->SetStatus(200);
            return false; 
        } 

        else
    
        {
            $this->LogMessage("Verbindung zu FEMS konnte nicht hergestellt werden, bitte IP-Adresse oder Netzwerkverbindung prüfen!", KL_ERROR);
            $this->SetStatus(200);
            return false;
        }

    /*


        public function checkConnection()

    {
        $this->get_baseURL();
        $url = $this->baseURL . "State";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    
        $raw_data = curl_exec($curl);

        $json = json_decode($raw_data, true);
    

        if (isset($json->error) && $json->error->code == 1003)

        {
            $this->LogMessage("Anmeldung fehlgeschlagen, Zugangsdaten prüfen", KL_WARNING);
        }


        if($raw_data == true)

        switch ($raw_data)

        {
            case ($raw_data['code'] == 1003):
            $this->LogMessage("Anmeldung fehlgeschlagen, Zugangsdaten prüfen", KL_WARNING);
            break;

            case $raw_data == false:
                $this->LogMessage("Verbindung zu FEMS konnte nicht hergestellt werden, bitte IP-Adresse oder Netzwerkverbindung prüfen!", KL_ERROR);
                $this->SetStatus(200);
                return false;
                break;
        }

        {
            $this->LogMessage("Verbindung zu FEMS erfolgreich hergestellt!", KL_NOTIFY);
            $this->SetStatus(102);
            return true;
        }
            
        elseif($raw_data == false)
    
        {
            $this->LogMessage("Verbindung zu FEMS konnte nicht hergestellt werden, bitte IP-Adresse oder Netzwerkverbindung prüfen!", KL_ERROR);
            $this->SetStatus(200);
            return false;
        }

        
        if($raw_data['code'] == 1003)

        {
            $this->LogMessage("Anmeldung fehlgeschlagen, Zugangsdaten prüfen", KL_WARNING);
        }
*/





    }

    /****************************************************************************************************
     * Zusammensetzen der URL und Verbingdsaufbau zum Datenabruf
    ****************************************************************************************************/

        //Funktion zum Datenabruf
        private function fems_connect($channel, $value)

        {
            $this->get_baseURL();
            $url = $this->baseURL . $channel . $value;
    
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 6);
    
            $raw_data = curl_exec($curl);
            curl_close($curl);
    
            if ($raw_data === false) 

            {
                $this->LogMessage("Verbindung zu FEMS konnte nicht hergestellt werden, bitte IP-Adresse oder Netzwerkverbindung prüfen!", KL_ERROR);
                return false;
            } 
            
            else 
            
            {
                return $raw_data;
            }


        }

        //zusammensetzen der Basis URL
        private function get_baseURL ()

        {
            $ip = $this->ReadPropertyString("ip");
            $user = $this->ReadPropertyString("username");
            $pw = $this->ReadPropertyString("password");
            
            $this->baseURL = "http://$user:$pw@$ip:80/rest/channel/";
        }
    }

?>