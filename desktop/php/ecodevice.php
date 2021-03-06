<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'ecodevice');
?>

<div class="row row-overflow">
    <div class="col-lg-3">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un Ecodev}}</a>
                <?php
				$eqLogics = eqLogic::byType('ecodevice');
                foreach ($eqLogics as $eqLogic) {
                       echo '<li>';
						echo '<i class="fa fa-sitemap cursor eqLogicAction" data-action="hide" data-eqLogic_id="' . $eqLogic->getId() . '"></i>';
						echo '<a class="cursor li_eqLogic" style="display: inline;" data-eqLogic_id="' . $eqLogic->getId() . '" data-eqLogic_type="ecodevice">' . $eqLogic->getName() . '</a>';
						echo '<ul id="ul_eqLogic" class="nav nav-list bs-sidenav sub-nav-list" data-eqLogic_id="' . $eqLogic->getId() . '" style="display: none;">';
							echo '<li>';
								echo '<i class="fa fa-calculator cursor eqLogicAction" data-action="hide" data-eqLogic_id="compteur_' . $eqLogic->getId() . '"></i>';
								echo '<a class="cursor eqLogicAction" data-action="hide" style="display: inline;" data-eqLogic_id="compteur_' . $eqLogic->getId() . '" data-eqLogic_type="ecodevice">{{Compteur}}</a>';
								echo '<ul id="ul_eqLogic" class="nav nav-list bs-sidenav sub-nav-list" data-eqLogic_id="compteur_' . $eqLogic->getId() . '" style="display: none;">';
									foreach (eqLogic::byType('ecodevice_compteur') as $SubeqLogic) {
										if ( substr ($SubeqLogic->getLogicalId(), 0, strpos($SubeqLogic->getLogicalId(),"_")) == $eqLogic->getId() ) {
											echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $SubeqLogic->getId() . '" data-eqLogic_type="ecodevice_compteur"><a>' . $SubeqLogic->getListeName() . '</a></li>';
										}
									}
								echo '</ul>';
							echo '</li>';
							echo '<li>';
								echo '<i class="fa fa-bolt cursor eqLogicAction" data-action="hide" data-eqLogic_id="teleinfo_' . $eqLogic->getId() . '"></i>';
								echo '<a class="cursor eqLogicAction" data-action="hide" style="display: inline;" data-eqLogic_id="teleinfo_' . $eqLogic->getId() . '" data-eqLogic_type="ecodevice">{{Téléinfo}}</a>';
								echo '<ul id="ul_eqLogic" class="nav nav-list bs-sidenav sub-nav-list" data-eqLogic_id="teleinfo_' . $eqLogic->getId() . '" style="display: none;">';
									foreach (eqLogic::byType('ecodevice_teleinfo') as $SubeqLogic) {
										if ( substr ($SubeqLogic->getLogicalId(), 0, strpos($SubeqLogic->getLogicalId(),"_")) == $eqLogic->getId() ) {
											echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $SubeqLogic->getId() . '" data-eqLogic_type="ecodevice_teleinfo"><a>' . $SubeqLogic->getListeName() . '</a></li>';
										}
									}
								echo '</ul>';
							echo '</li>';
						echo '</ul>';
					echo '</li>';
				}
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-9 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
        <legend>{{Mes Ecodevices}}
        </legend>
        <?php
        if (count($eqLogics) == 0) {
            echo "<br/><br/><br/><center><span style='color:#767676;font-size:1.2em;font-weight: bold;'>{{Vous n'avez pas encore d'ecodevice, cliquez sur Ajouter un équipement pour commencer}}</span></center>";
        } else {
            ?>
            <div class="eqLogicThumbnailContainer">
                <?php
                foreach ($eqLogics as $eqLogic) {
                    echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
                    echo "<center>";
                    echo '<img src="plugins/ecodevice/doc/images/ecodevice.jpg" height="105" width="95" />';
                    echo "</center>";
                    echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
                    echo '</div>';
                }
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="col-lg-9 eqLogic ecodevice" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <form class="form-horizontal">
            <fieldset>
                <legend>
                    <i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}}
				   <i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i>
                </legend>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Nom de l'ecodevice}}</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'ecodevice}}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label" >{{Objet parent}}</label>
                    <div class="col-lg-3">
                        <select class="form-control eqLogicAttr" data-l1key="object_id">
                            <option value="">{{Aucun}}</option>
                            <?php
                            foreach (object::all() as $object) {
                                echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Catégorie}}</label>
                    <div class="col-lg-8">
                        <?php
                        foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                            echo '<label class="checkbox-inline">';
                            echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                            echo '</label>';
                        }
                        ?>

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label" >{{Activer}}</label>
                    <div class="col-md-1">
                        <input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>
                    </div>
					<a class="btn btn-default btn-sm tooltips" id="bt_configPush" title='{{Configurer push}}'><i class="fa fa-wrench"></i></a>
                    <label class="col-lg-2 control-label" >{{Visible}}</label>
                    <div class="col-lg-1">
                        <input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{IP de l'ecodevice}}</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ip"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Port de l'ecodevice}}</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="port"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Compte de l'ecodevice}}</label>
                    <div class="col-lg-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="username"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label">{{Password de l'ecodevice}}</label>
                    <div class="col-lg-3">
                        <input type="password" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="password"/>
                    </div>
                </div>
            </fieldset> 
        </form>

        <legend>{{Indicateurs}}</legend>
        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 230px;">{{Nom}}</th>
                    <th style="width: 110px;">{{Sous-Type}}</th>
                    <th>{{Valeur}}</th>
                    <th style="width: 100px;">{{Unité}}</th>
                    <th style="width: 200px;">{{Paramètres}}</th>
                    <th style="width: 100px;"></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
                    <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                </div>
            </fieldset>
        </form>

    </div>
	<?php include_file('desktop', 'ecodevice_compteur', 'php', 'ecodevice'); ?>
	<?php include_file('desktop', 'ecodevice_teleinfo', 'php', 'ecodevice'); ?>
</div>

<?php include_file('desktop', 'ecodevice', 'js', 'ecodevice'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
<script type="text/javascript">
if (getUrlVars('saveSuccessFull') == 1) {
    $('#div_alert').showAlert({message: '{{Sauvegarde effectuée avec succès}}<br>{{Utilisez sur l\icône suivant pour voir le détail de l\'élément <i class="fa fa-sitemap"></i>}}', level: 'success'});
}
</script>