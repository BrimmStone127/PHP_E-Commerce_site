<?php
include "LIB_project1.php";

//Call the html starter tags and navigation
echo getTopHTML();

//html specific for index.php
echo "<hr>";
echo "<h2>On Sale Now!</h2>";


//get items that are on sale 
//Parameters (SQL condition for select, identifier for return, table from database)
echo getItems('RegularPrice > 0',1,'Soup');

//html specific for index.php
echo "<hr>";
echo "<h1>Catalog</h1>";

//Check for the hidden value post to change the page. Also sanitizes and validates the field. 
if (isset($_POST["page"])) {
	$page = $_POST["page"];
	if($page != "" && !(numeric($page))){
  		echo "Error.";	  		 
  	}
  	else{
  		$page = $page + 5;//Will show a new five items in the catalog
		unset($_POST["page"]);
  	}
}
if (isset($_POST["back"])) {
	$page = $_POST["back"];
	if($page != "" && !(numeric($page))){
  		echo "Error.";	  		 
  	}
  	else{
  		$page = $page - 5;//will show the previous five pages in the catalog
		if($page < 0){
			$page = 0;
		}
		unset($_POST["back"]);
	}
}

if (!isset($page)){
	$page = 0;
}
$pageNum = $page/5+1;

//Get the catalog items
echo getItems('RegularPrice IS NULL LIMIT 5 OFFSET '.$page,2,'Soup');
//get the form for the page bu
echo "
	</br >
	</br >
	<form name='form' method='post' action=''>
	<input type='hidden' name='back' value={$page}>
	<input type='submit' name='button1' value='Previous Page'>
	</form>
	<p>Page: $pageNum</p>
	<form name='form2' method='post' action=''>
	<input type='hidden' name='page' value={$page}>
	<input type='submit' name='button' value='Next Page'>
	</form>";
