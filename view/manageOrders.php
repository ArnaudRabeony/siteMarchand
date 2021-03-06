<?php 


require_once(dirname(__FILE__) . '/../model/functions.php');
require_once(dirname(__FILE__) . '/../model/commande.php');
require_once(dirname(__FILE__) . '/../model/commande_etat.php');
require_once(dirname(__FILE__) . '/../model/ligne_commande.php');

if(pageRestriction(array("admin")))
{

$toHightlight=0;

	if(verifGet(array("main")))
		$toHightlight=$_GET['main'];

$emptyTable=isCommandeEmpty($db);

$visibleEmptyContainer= $emptyTable ? 'style="display:block;"' : 'style="display:none;"';
$visibleContainer= $emptyTable ? 'style="display:none;"' : 'style="display:block;"';
 ?>

<div class="displayContainer">
	<p>
		<h3><small>Cette page vous permet de gérer et visualiser les commandes des clients</small></h3>
		 Cliquer sur l'étape courante d'une commande permettra d'approuver le statut de la commande en question.
		 Les changements effectués seront visibles sur la page "Mes commandes" des clients concernés.
		 <hr>
	</p>
	<div id="ordersContainer" <?php echo "data-main=$toHightlight" ?>>
		<div id="notEmptyOrderTable" class="table-responsive" <?php echo $visibleContainer ?>>
			<?php echo displayOrders($db) ?>
		</div>
		<div id="emptyOrderTableContainer" class="shadow450" <?php echo $visibleEmptyContainer ?>>
			<div id="emptyOrderTable">
				<p>
					<i class="material-icons">business_center</i>
					<h3>Aucune commande présente</h3>
					<h5>Aucune commande n'a été passée, livrée ou n'est terminée</h5>
				</p>
			</div>
		</div>
		<br>
		<hr>
	</div>
</div>

<script src="js/jquery.js"></script>
<script>
	
	$(function()
	{
		var toHightlight = $("#ordersContainer").attr("data-main");
		
		if(toHightlight!=0)
		{
			$('.orderContainer').each(function()
			{
				if($(this).attr("data-orderid")==toHightlight)
				{
					$(this).css("background-color","#EEE");
				}
			});
		}
	})

</script>
<?php } ?>