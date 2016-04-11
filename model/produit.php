<?php

require_once(dirname(__FILE__) . '/../connection.php');

// require_once('./connection.php');
require('categorie_produit.php');
require('sport.php');
require('marque.php');
require('stock.php');

function addProduct($db, $dataArray)
{
	// echo "id Categorie : ".$dataArray['categorie']."<br>";
	$req = $db->prepare('insert into produit(libelle, description, prix, photo, idTaille, idCategorie, idSport, idMarque)
							values(:libelle, :description, :prix, :photo, :idTaille, :idCategorie, :idSport, :idMarque)');
	$req->bindValue(':libelle', $dataArray['libelle']);
	$req->bindValue(':description', $dataArray['description']);
	$req->bindValue(':prix', $dataArray['prix']);
	$req->bindValue(':photo', $dataArray['photo']);
	$req->bindValue(':idTaille', $dataArray['taille']);
	$req->bindValue(':idCategorie', $dataArray['categorie']);
	$req->bindValue(':idSport', $dataArray['sport']);
	$req->bindValue(':idMarque', $dataArray['marque']);
	$req->execute();

	$req=$db->prepare('select * from produit where libelle=:libelle and description=:description and prix=:prix and photo=:photo and idTaille=:idTaille and idCategorie=:idCategorie and idSport=:idSport and idMarque=:idMarque');
	$req->bindValue(':libelle', $dataArray['libelle']);
	$req->bindValue(':description', $dataArray['description']);
	$req->bindValue(':prix', $dataArray['prix']);
	$req->bindValue(':photo', $dataArray['photo']);
	$req->bindValue(':idTaille', $dataArray['taille']);
	$req->bindValue(':idCategorie', $dataArray['categorie']);
	$req->bindValue(':idSport', $dataArray['sport']);
	$req->bindValue(':idMarque', $dataArray['marque']);
	$req->execute();
	$res=$req->rowCount();

	return $res==1 ? true : false;
}

function addNewSingleProduct($db, $dataArray/*, $sizeArray*/)
{

	$sizeId = $dataArray['categorie'] == 5 ? 7 : 3;

	$req = $db->prepare('insert into produit(libelle, description, prix, photo, idCategorie, idSport, idMarque, idTaille)
							values(:libelle, :description, :prix, :photo, :idCategorie, :idSport, :idMarque, :idTaille)');
	$req->bindValue(':libelle', $dataArray['libelle']);
	$req->bindValue(':description', $dataArray['description']);
	$req->bindValue(':prix', $dataArray['prix']);
	$req->bindValue(':photo', $dataArray['photo']);
	$req->bindValue(':idCategorie', $dataArray['categorie']);
	$req->bindValue(':idSport', $dataArray['sport']);
	$req->bindValue(':idMarque', $dataArray['marque']);
	$req->bindValue(':idTaille', $sizeId);
	$req->execute();

	$req=$db->prepare('select idProduit from produit order by idProduit desc limit 1');
	$req->execute();
	$res = $req->fetch(PDO::FETCH_NUM);
	$idProduit=$res[0];

	//TODO : get idProduit
	// foreach taille 
	// si taille != ""
		// insert into stock values (idProduit, idTaille, quantite)

	// foreach ($sizeArray as $size => $qty)
	// {
	// 	if($qty!="")
	// 	{
	// 		$insertReq = $db->prepare('insert into stock values(:idProduit, :idTaille,:quantite)');
	// 		$insertReq->bindValue(":idProduit",$idProduit);
	// 		$insertReq->bindValue(":idTaille",$size);
	// 		$insertReq->bindValue(":quantite",$qty);
	// 		$insertReq->execute();
	// 	}
	// }

	return count(getProductById($db,$idProduit));// && count(getStockByProductId($db,$idProduit));
}

function deleteProductTable($db)
{
	$req = $db->prepare('delete from produit');
	$req->execute();
}

function getProductsBySport($db, $sport)
{
	$req = $db->prepare('select * from produit where idSport = :idSport');
	$req->bindValue(':idSport', $sport);
	$req->execute();
	$res = $req->fetchAll();
    return $res;
}

function addRowInProductTable($db,$res)
{
	$brand=getMarqueById($db,$res['idMarque']);
		$toReturn='<tr id="row'.$res['idProduit'].'" class="productLine secured">
		<td><a class="moreInfo btn btn-default btn-sm" style="display:none" href="index.php?page=view/createProduct&id='.$res['idProduit'].'"><i class="fa fa-ellipsis-h"></i></a></td>
		<td><input id="ref" disabled class="form-control" type="text" value="REF'.$res['idProduit'].'"</td>
		<td><input id="brand" disabled class="form-control" type="text" value="'.$brand.'"</td>
		<td><input id="label" disabled class="form-control" type="text" value="'.$res['libelle'].'"</td>
		<td><textarea class="form-control" name="description" id="description" rows=1 disabled style="font-size:13px;">'.$res['description'].'</textarea></td>
		<td><input id="price" disabled class="form-control" type="text" value="'.$res['prix'].'"</td>
		<td><button class="editButton btn btn-default btn-sm"><i class="fa fa-pencil"></i></button></td>
		<td><button class="deleteButton btn btn-default btn-sm"><i class="fa fa-close"></i></button></td>
		<td class="checkboxContainer" style="text-align:center;padding-top:10px;"><input class="deleteCheckbox" type="checkbox" value="delete'.$res['idProduit'].'"></td>
		</tr>';

	return $toReturn;
}

//query must content bindValues as : "x=:param0, y=:param1"
//if where clause isn't use set where to "" and bindValuesArray to null or array()
//bindValuesArray contains values which will be set as values in the query
//commonParam=true if the array contain a single value which has to be bound to all param
function displayProducts($db,$whereArray,$bindValuesArray,$sameValueForAll)
{
	//possibility : $operation=["like" | "=" | "!="]
	$query="select * from produit natural join marque ";

	if(!is_null($whereArray) && count($whereArray)!=0)
	{
		$query.="where ";
		$cptWhere=count($whereArray);

		for ($i=0; $i < $cptWhere; $i++) 
			if($i+1 == $cptWhere)//last
				$query.=$whereArray[$i]." like ? ";		
			else
				$query.=$whereArray[$i]." like ? or ";
	}

	$req=$db->prepare($query." order by idProduit");

	$paramNumber=substr_count($query,"?");

	if(!is_null($bindValuesArray) && count($bindValuesArray)!=0)//bindValues
	{
		if($sameValueForAll)//all param bound to the single value
			for ($i=0; $i < $paramNumber; $i++) 
				$req->bindValue($i+1,$bindValuesArray[0].'%');
		else
			for ($i=0; $i < $paramNumber; $i++) 
				$req->bindValue($i+1,$bindValuesArray[$i].'%');
	}

	$req->execute();
	// echo $req->rowCount();
	$body='';
	while($res=$req->fetch(PDO::FETCH_ASSOC))
		$body.= addRowInProductTable($db,$res);

	$lastRow='<tr>
		<td></td>
		<td><button id="downloadButton" class="btn btn-primary btn-sm"><a href="controller/download.php"><i class="fa fa-download"></i></a></button></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td><div class="multipleDeletion" style="float: right;">
				<button class="btn btn-sm" id="multipleDeletionButton" style="background-color: #A90000"><i class="fa fa-close" style="color:white;"></i></button>
			</div></td>
	</tr>';

	$tableContent=$body.$lastRow;

	return $tableContent;
}

function getProductById($db,$id)
{
	$req = $db->prepare('select * from produit where idProduit = :id');
	$req->bindValue(':id', $id);
	$req->execute();
	$res = $req->fetchAll();
    return $res;
}

function updateProduct($db,$id,$libelle,$description,$prix)
{
	$req = $db->prepare('update produit set libelle=:libelle, description=:description, prix=:prix where idProduit=:id');
	$req->bindValue(':id', $id);
	$req->bindValue(':libelle', $libelle);
	$req->bindValue(':description', $description);
	$req->bindValue(':prix', $prix);
	$req->execute();

	$req = $db->prepare('select * from produit where libelle=:libelle and description=:description and prix=:prix and idProduit=:id');
	$req->bindValue(':id', $id);
	$req->bindValue(':libelle', $libelle);
	$req->bindValue(':description', $description);
	$req->bindValue(':prix', $prix);
	$req->execute();
	$res=$req->rowCount();

	return $res==1 ? true : false;
}

function deleteProduct($db,$id)
{
	$req=$db->prepare("delete from produit where idProduit=:id");
	$req->bindValue(":id",$id);
	$req->execute();

	$req=$db->prepare("select * from produit where idProduit=:id");
	$req->bindValue(":id",$id);
	$req->execute();
	$res=$req->rowCount();

	return $res==0 ? true : false;
}

function isProduitEmpty($db)
{
	$req=$db->prepare("select * from produit");
	$req->execute();
	$res=$req->rowCount();

	return $res==0 ? true : false;
}


// function used in all the sport pages
// @param $images an array containing the images for the carousel
/*function displayCarousel($images)
{
	echo "
<div class=col-md-12>
                    <div class=row carousel-holder>
                        <div class=col-md-12>
                            <div id=carousel-example-generic class=carousel slide data-ride=carousel>
                                <ol class=carousel-indicators>
                                    <li data-target=#carousel-example-generic data-slide-to=0 class=active></li>
                                    <li data-target=#carousel-example-generic data-slide-to=1></li>
                                    <li data-target=#carousel-example-generic data-slide-to=2></li>
                                </ol>
                                <div class=carousel-inner>
                                    <div class=item active>
                                        <img class=slide-image src=http://placehold.it/800x300 alt=>
                                    </div>
                                    <div class=item>
                                        <img class=slide-image src=http://placehold.it/800x300 alt=>
                                    </div>
                                    <div class=item>
                                        <img class=slide-image src=http://placehold.it/800x300 alt=>
                                    </div>
                                </div>
                                <a class=left carousel-control href=#carousel-example-generic data-slide=prev>
                                    <span class=glyphicon glyphicon-chevron-left></span>
                                </a>
                                <a class=right carousel-control href=#carousel-example-generic data-slide=next>
                                    <span class=glyphicon glyphicon-chevron-right></span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class=row>";
}*/