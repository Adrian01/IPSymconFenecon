[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
![Version](https://img.shields.io/badge/Symcon%20Version-7.0%20%3E-blue.svg)
[![Donate](https://img.shields.io/badge/Donate-Paypal-009cde.svg)](https://www.paypal.me/adrianschmidt1121)
# IP-Symcon Fenecon Home

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetungen)
3. [Unterstützte Gerätetypen](#3-unterstützte-gerätevarianten)
4. [Installation](#4-installation)
5. [Funktionsreferenz](#5-funktionsreferenz)
6. [Statusvariablen](#6-statusvariablen)
7. [Anhang](#7-anhang)
   1. [GUIDs der Module](#guids-der-module)
   2. [Spenden](#spenden)


## 1. Funktionsumfang

Das Modul dient zum auslesen aller relevanten Datenpunkte aus dem Stromspeichersystem Fenecon Home.
Derzeit wird nur der lesende Zugriff unterstützt, für den Schreibzugriff wird ein Kostenpflichtiges Addon im FEMS-System benötigt, der Schreibzugriff beschränkt sich außerdem auf ein paar wenige Datenpunkte.

Aktuelle Features:

- Auslesen allgemeiner Datenpunkte zur PV-Produktion
- Auslesen allgemeiner Datenpunkte zum Energieverbauch
- Auslesen allgemeiner Datenpunkte zur Netzeinspeisung/Netzbezug
- Auslesen allgemeiner Datenpunkte einer angeschlossenen Wallbox


## 2. Voraussetzungen

- IP-Symcon 7.0
- Fenecon Home Stromspeichersystem


## 4. Installation

### 4.1 Laden des Moduls

Modul im Module Control hinzufügen: https://github.com/Adrian01/IPSymconFenecon


### 4.2 Eingabe der Anmeldedaten und Modul aktivieren

![image](docs/login.png)

Über die Checkbox "Schnittstelle aktivieren" kann die komplette Funktion des Moduls aktiviert und deaktiviert werden. 

Im Feld "FEMS IP-Adresse" ist die interne IP-Adresse des FEMS-Systems einzutragen.
Das Feld "Benutzername" kann derzeit leer bleiben, aktuell wird in FEMS nicht zwischen verschiedenen Usern unterschieden. 
Das Feld "Passwort" muss für den Lesezugriff standardmäßig mit dem Passwort "user" befüllt werden.

Wenn eine Ladestation über das FEMS-System angebunden wurde, können die Statusinformationen dieser ebenfalls über das Modul abgerufen werden, hierfür ist der Schalter "E-Mobility Ladestation vorhanden" zu aktivieren.


## 5. Funktionsreferenz

 _**Datenpunkte aktualisieren**_
```php
FH_update()
```

## 6. Statusvariablen

|         Variable                 |   Typ   |                                  Beschreibung                                           |
|:--------------------------------:|:-------:|:---------------------------------------------------------------------------------------:|
|      Systemzustand               | Integer | gibt den aktuellen Zustand des Gesamtsystems aus                                        |
|      Netzmodus                   | Integer | gibt an ob sich das System im Netzbetrieb oder Notstrombetrieb befindet                 |
|      Gesamtverbrauch             | Integer | Zählervariable des Gesamtstromverbrauchs in kWh                                         |
|      Momentanverbrauch           | Integer | Aktueller Stromverbrauch (mögliche PV-Produktion nicht berücksichtig)                   |
|      Momentanverbrauch L1        | Integer | Aktueller Stromverbrauch auf der Phase L1 (mögliche PV-Produktion nicht berücksichtig)  |
|      Momentanverbrauch L2        | Integer | Aktueller Stromverbrauch auf der Phase L2 (mögliche PV-Produktion nicht berücksichtig)  |
|      Momentanverbrauch L3        | Integer | Aktueller Stromverbrauch auf der Phase L3 (mögliche PV-Produktion nicht berücksichtig)                 |
|      Gesamtbezug Netz            | Integer | Zählervariable des gesamten Stroms der über das Netz bezogen/zugekauft wurde                           |
|      Gesamteinspeisung Netz      | Integer | Zählervariable des gesamten Stroms der in das Netz eingespeist wurde (Überschusseinspeisung)           |
|      Momentanleistung Netz       | Integer | Aktueller Bezug oder Einspeisung am Netzanschlusspunkt, positiv = Bezug / negativ = Einspeisung        |
|      Momentanleistung Netz L1    | Integer | Aktueller Bezug oder Einspeisung am Netzanschlusspunkt auf der Phase L1, positiv = Bezug / negativ = Einspeisung        |
|      Momentanleistung Netz L2    | Integer | Aktueller Bezug oder Einspeisung am Netzanschlusspunkt auf der Phase L2, positiv = Bezug / negativ = Einspeisung        |
|      Momentanleistung Netz L3    | Integer | Aktueller Bezug oder Einspeisung am Netzanschlusspunkt auf der Phase L3, positiv = Bezug / negativ = Einspeisung        |
|      PV-Produktion aktuell       | Integer | Produktion der PV-Anlage                                                                                                |
|      PV-Produktion gesamt        | Integer | Gesamte Produktion der PV-Anlage seit Inbetriebnahme                                                                    |
|      Momentanleistung Speicher   | Integer | Aktuelle Be-/Entladung des Stromspeichers, positiv = Entladung / negativ = Beladung                                     |
|      Beladung Speicher gesamt    | Integer | Zählervariable der gesamten Energie die seit Inbetriebnahme in den Speicher geladen wurde                               |
|      Entladung Speicher gesamt   | Integer | Zählervariable der gesamten Energie die seit Inbetriebnahme aus dem Speicher entladen wurde                             |
|      Kapazität Speichersystem    | Float   | Nennkapazität des Speichersystems                                                                                       |
|      Zustand Ladestation         | Integer | gibt den aktuellen Betriebszustand der Ladestation aus                                                                  |
|      Ladeleistung                | Integer | aktuell eingestellte Ladeleistung der Ladestation                                                                       |
|      Energie Ladevorgang         | Integer | Zählervariable Verbrauch während des aktullen bzw. des vergangenen Ladevorgangs (wird bei neuem Vorgang zurückgesetzt   |
|      Gesamtverbrauch Ladestation | Integer | Zählervariable Gesamtverbrauch für alle Ladevorgänge seit Inbetriebnahme der Ladestation am FEMS-System                 |


## 7. Anhang

###  GUIDs der Module

|           Modul            |  Typ   |                  GUID                  |
|:--------------------------:|:------:|:--------------------------------------:|
|          Fenecon           | Device | {C4D7A2A4-789F-63CE-D5CC-DD0BD1671C0C} |



###  Spenden

Dieses Modul ist für die nicht kommzerielle Nutzung kostenlos, Schenkungen als Unterstützung für den Autor werden hier akzeptiert:    

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=H35258DZU36AW" target="_blank"><img src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" /></a>
