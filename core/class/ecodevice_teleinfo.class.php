<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class ecodevice_teleinfo extends eqLogic {
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */
	public function getListeName()
	{
		return substr($this->getLogicalId(), strpos($this->getLogicalId(),"_")+2, 1)." - ".parent::getName();
	}

	public function postInsert()
	{
		$commandes = array("BASE" => array('Index (base)', 'numeric', 'kwatt-heure', 1),
		"HCHC" => array('Index (heures creuses)', 'numeric', 'kwatt-heure', 1),
		"HCHP" => array('Index (heures pleines)', 'numeric', 'kwatt-heure', 1),
		"BBRHCJB" => array('Index (heures creuses jours bleus Tempo)', 'numeric', 'kwatt-heure', 0),
		"BBRHPJB" => array('Index (heures pleines jours bleus Tempo)', 'numeric', 'kwatt-heure', 0),
		"BBRHCJW" => array('Index (heures creuses jours blancs Tempo)', 'numeric', 'kwatt-heure', 0),
		"BBRHPJW" => array('Index (heures pleines jours blancs Tempo)', 'numeric', 'kwatt-heure', 0),
		"BBRHCJR" => array('Index (heures creuses jours rouges Tempo)', 'numeric', 'kwatt-heure', 0),
		"BBRHPJR" => array('Index (heures pleines jours rouges Tempo)', 'numeric', 'kwatt-heure', 0),
		"EJPHN" => array('Index (normal EJP)', 'numeric', 'kwatt-heure', 0),
		"EJPHPM" => array('Index (pointe mobile EJP)', 'numeric', 'kwatt-heure', 0),
		"IINST" => array('Intensité instantanée', 'numeric', 'ampere', 1),
		"IINST1" => array('Intensité instantanée 1', 'numeric', 'ampere', 0),
		"IINST2" => array('Intensité instantanée 2', 'numeric', 'ampere', 0),
		"IINST3" => array('Intensité instantanée 3', 'numeric', 'ampere', 0),
		"PPAP" => array('Puissance Apparente', 'numeric', 'watt', 1),
		"OPTARIF" => array('Option tarif', 'string', '', 1),
		"DEMAIN" => array('Couleur demain', 'string', '', 0),
		"PTEC" => array('Tarif en cours', 'string', '', 1));
		foreach( $commandes as $label => $data)
		{
			$cmd = $this->getCmd(null, $label);
			if ( ! is_object($cmd) ) {
				$cmd = new ecodevice_teleinfoCmd();
				$cmd->setName($data[0]);
				$cmd->setEqLogic_id($this->getId());
				$cmd->setType('info');
				$cmd->setSubType($data[1]);
				$cmd->setLogicalId($label);
				$cmd->setUnite($data[2]);
				$cmd->setIsVisible($data[3]);
				$cmd->setEventOnly(1);
				$cmd->save();
			}
		}
	}

	public function postUpdate() {
	}

    public static function event() {
        $cmd = ecodevice_teleinfoCmd::byId(init('id'));
        if (!is_object($cmd)) {
            throw new Exception('Commande ID virtuel inconnu : ' . init('id'));
        }
		$cmd->event(init('value'));
    }

	public function configPush($url) {
		$gceid = substr($this->getLogicalId(), strpos($this->getLogicalId(),"_")+2, 1);
		$url .= 'protect/settings/notif'.$gceid.'P.htm';
		for ($compteur = 0; $compteur < 6; $compteur++) {
			log::add('ecodevice','debug','Url '.$url);
			$data = array('num' => $compteur + ($gceid -1)*6,
					'act' => $compteur+3,
					'serv' => config::byKey('internalAddr'),
					'port' => 80,
					'url' => '/jeedom/core/api/jeeApi.php?api='.config::byKey('api').'&type=ecodevice&id='.substr($this->getLogicalId(), 0, strpos($this->getLogicalId(),"_")).'&message=data_change');
//					'url' => '/jeedom/core/api/jeeApi.php?api='.config::byKey('api').'&type=ecodevice_teleinfo&id='.$this->getId().'&message=data_change');
			
			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($data),
				),
			);
			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
		}
	}

    public function getLinkToConfiguration() {
        return 'index.php?v=d&p=ecodevice&m=ecodevice&id=' . $this->getId();
    }
    /*     * **********************Getteur Setteur*************************** */
}

class ecodevice_teleinfoCmd extends cmd 
{
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */

    /*     * **********************Getteur Setteur*************************** */
}
?>
