<?php
	require_once "DB.class.php";
	include ("validations.php");

	//These if statements are for catching various posts throughout the site 
	//If there has been a post from the hidden field in the forms ->  run update function.
	if (isset($_POST["id"])) {
		$id = $_POST["id"];
    	$db = new DB();
		update($id);
    	unset($_POST["id"]);
    }
    //If the clear cart button has been pressed this will clear the quantity in the Cart database. 
	if (isset($_POST["clear"])) {
		$db = new DB();
		$db->clearCart();
    	$db = new DB();
    	unset($_POST["clear"]);
	}
	//Resets everything if the reset button is pressed 
	if (isset($_POST["reset"])) {
		unset($_POST);
	}
	//check if the submit button has been pressed
    if (isset($_POST["submit"])){
    	checkForm();
    }

	
	//Runs a select function out of the database gets a specific html return based off of the id parameter. Returns functioning html. $command = where clause of sql command, $id = helps specify the return, $table = table in the database to update
	function getItems($command, $id, $table){
		$db = new DB();
		$bigString = "";
		$data = $db->getAllObjects($table,'*',$command);
		if ($id = 1){
			foreach($data as $soup){
				$bigString = $bigString . "{$soup->selectSale('RegularPrice')}";
			}
		}
		if ($id = 2){
			foreach($data as $soup){
				$bigString = $bigString . "{$soup->selectPrice('RegularPrice')}";
			}
		}
		
		return $bigString;
	}

	//function returns the top portion of the html with the label and the links that occur globally
	function getTopHTML() {
		$bigString = 
		"<!DOCTYPE html>
		<html>
		<head>
			<link rel='stylesheet' href='style.css'>
		</head>
		<body>
			<ul>
	 			<li><a href='index.php'>Home</a></li>
	  			<li><a href='cart.php'>Cart</a></li>
	  			<li><a href='admin.php'>Admin</a></li>
			</ul>
			<h1>Stu's Stews</h1>";
		return $bigString;
	}

	function addTotals(){
		$data = array();
		$db = new DB();
		$bigNum = 0;
		$data = $db->getAllObjects('Cart', 'Sum(Price * Quantity) AS Price', 'Quantity IS NOT NULL');
		foreach($data as $soup){
			$bigNum = "{$soup->selectTotal()}";
		}
		return $bigNum;
	}

	//Run the database function for updating THIS FUNCTION IS FOR WORKING WITH INT FIELDS
	//Parameters $table - table where update occurs, $field - field to update, $num - number being added to the field, $id - Where clause identifier, $value - where clause identifier - 
	//UPDATE $table SET $field = $field + $num WHERE $id = $value
	function update($id){
		$db = new DB();
		$db->increment($id);
		$db->decrement($id);
	}


	//This function returns the form used in the admin page. 
	function getForm(){
		//If the dropdown menu has not been selected 
		if(!isset($_POST["edit"])){
			$FormId = " ";
    		$FormName = " "; 
    		$FormDesc = " "; 
    		$FormPrice = " "; 
    		$FormQuantity = " "; 
    		$FormReg = " ";
    		$FormImage = " ";
		}
		//If the dropdown menu has been selected 
		if (isset($_POST["edit"])) {
			$id = $_POST["edit"];
			unset($_POST["edit"]);
			$str = 'SoupID = '.$id;
	    	$db = new DB();
	    	$data = $db->getAllObjects('Soup','*',$str);
	    	$array = array();
	    	foreach($data as $soup){
				$array = $soup->getEdit();
			}//take all the values from the selected drop down menu and assign them variables and load them in the form 
			$FormId = $array[0];
	    	$FormName = $array[1]; 
	    	$FormDesc = $array[2]; 
	    	$FormPrice = $array[3]; 
	    	$FormReg = $array[5];
	    	$FormQuantity = $array[4]; 
	    	$FormImage = $array[6];
		}
		//populate the field drop down menu
		$db = new DB();
		$data = $db->getAllObjects('Soup','Name, SoupID','SoupID > 0');
		$options = "<option selected='selected'>--SELECT ITEM TO UPDATE--</option>";
		foreach($data as $item){
			//call the Soup.class.method to get each option 
			$options = $options . "{$item->getItems()}";
		}
		//This is the form for admin.php 
		$bigString = 
		'<div id="form1"><form method = "post" action = "">

			<label for="state" >Change an existing items price / regular price: </label>
			<select name="edit" onchange="this.form.submit();">
				'.$options.'
			</select>

		</form>
		<form id="sample" action="" method="post">
			<fieldset>
				<legend>Item Editor</legend>

				<label for="id" class="required">ID: </label>
					<input class="required" name="ID" type="text" size="5" value="'.$FormId.'" readonly/></br >

				<label for="name" class="required">Name: </label>
					<input class="required" name="name" type="text" size="50" value="'.$FormName.'" /></br >

				<label for="description" class="required">Description: </label></br>
					 <textarea id="description" name="description" rows="5" cols="60" wrap="soft">'.$FormDesc.'</textarea></br >

				<label for="price">Price: </label>
					<input name="price" type="text" size="10" value="'.$FormPrice.'" /></br >

				<label for="quantity">Quantity: </label>
					<input name="quantity" type="text" size="10" value="'.$FormQuantity.'" /></br >
				
				<p>*Items RegularPrice refers to an items price before going on sale.</p>
				<label for="regularprice">Items Regular Price: </label>
					<input name="regular" type="text" size="10" value="'.$FormReg.'" /></br >

				<label for="image">Image: </label>
					<input name="image" type="text" size="10" value="'.$FormImage.'" /></br >

				<label for="password">Admin Password: </label>
					<input type="password" class="form-control" size="10" name="password" value="" /></br >

		    </fieldset>
		    <input name="submit" type="submit" value="Save" />
		    <input name="reset" type="submit" value="Reset" /></br >
		</form></div>';
		return $bigString;
	}

    //makes sure that the recieved data from the form is valid and sanitized 
    function checkForm(){
    	//Init error variables
    	$pswd = "password";//current admin page password is password
		$errorMsg = false;//when true this error message will be displayed to the users
		$errorText = "<strong>ERRORS:</strong><br />";//This will hold the error message
		//These values are the values taken by post from the admin form. They are trimmed to make sure there are no unecessary characters
		$soupID = $_POST['ID'];
    	$name = $_POST['name'] ? trim($_POST['name']) : '';
    	$desc = $_POST['description'] ? trim($_POST['description']) : '';
    	$price = $_POST['price'] ? trim($_POST['price']) : '';
    	$quantity = $_POST['quantity'] ? trim($_POST['quantity']) : '';
    	$regularPrice = $_POST['regular'] ? trim($_POST['regular']) : '';
    	$image = $_POST['image'] ? trim($_POST['image']) : '';
    	$password = $_POST['password'] ? trim($_POST['password']) : '';
    	//VALIDATION
    	//This validates the name value in the form 
    	if($name == "" || !alphabeticSpace($name) || strlen($name) > 30 || $name == " ") {
    		$errorText = $errorText.'You must enter a valid name.<br />';
    		$error = true;
  		}
  		//This validates the description section and makes sure that there are no injections
  		if ($desc !="" && (sqlMetaChars($desc) || sqlInjection($desc) || sqlInjectionUnion($desc) ||
	  	sqlInjectionSelect($desc) || sqlInjectionInsert($desc) || sqlInjectionDelete($desc) ||
	  	sqlInjectionUpdate($desc) || sqlInjectionDrop($desc) || crossSiteScripting($desc) ||
	  	crossSiteScriptingImg($desc))) {
	    	$errorText = $errorText.'You entered a invalid description.<br />';
	    	$errorMsg = true;  		 
  		}
  		//This validates the price section and makes sure it is just numbers
  		if($price != "" && !(numeric($price))){
  			$errorText = $errorText.'You entered a invalid price.<br />';
	    	$errorMsg = true;  		 
  		}
  		//This validates the quantity section making sure it is just numbers
  		if($quantity != "" && !(numeric($quantity))){
  			$errorText = $errorText.'You entered a invalid quantity.<br />';
	    	$errorMsg = true;  		 
  		}
  		//This validates the regularprice section making sure it is just numbers
  		if($regularPrice != "" && !(numeric($regularPrice))){
  			$errorText = $errorText.'You entered a invalid regularPrice.<br />';
	    	$errorMsg = true;  		 
  		}

  		// if($image != "" && !(alphabeticNumericPunct($image))){
  		// 	$errorText = $errorText.'You entered a invalid image.<br />';
	   //  	$errorMsg = true;  		 
  		// }

  		//This checks the password and makes sure it is just alphanumeric with punctuation and that it is the correct password 
  		if($password != "" && !(alphabeticNumericPunct($password)) || $password != $pswd){
  			$errorText = $errorText.'Invalid Password.<br />';
  			$errorMsg = true;
  		}
  		//unset all post values
   		unset($_POST);
   		//Get the number of items that are currently on sale.
   		$salesnum = getNumberOfSales();
   		//Check and make sure there are no more then five items on sale
   		if($salesnum >= 5 && $regularPrice > 0){
   			$errorText = $errorText.'There can only be a maximum of 5 items on sale.';
   			$errorMsg = true;
   		}
   		//display the error messages
   		if($errorMsg){
   			echo $errorText;
   		}
   		//If there are no errors and the item has not been selected from the drop down then add it to the table
   		else if($soupID > 0){
   			updateItem($soupID, $price, $regularPrice);
   			echo $name . " has been updated!";
   		}
   		//If the item has a already defined ID then update it in the table 
   		else{
   			insertNewItem($name, $desc, $price, $quantity, $regularPrice, $image);
   		}
    }//end of validateForm

    //get the total cost from the cart 
	function getNumberOfSales(){
		$db = new DB();
		$bigString = "";
		$data = $db->getAllObjects('Soup','COUNT(RegularPrice) AS RegularPrice','SoupID > 0');
		foreach($data as $soup){
			$bigString = "{$soup->itemsOnSale()}";
		}	
		return $bigString;
	}

	//Update the table with new data from the form 
	function updateItem($soupID, $price, $regularPrice){
		$id2 = $soupID;
		$db = new DB();

		$table = "Soup";
	  	$db->update($table, $price, $id2, 1);
	  	$db->update($table, $regularPrice, $id2, 2);

	  	$table = "Cart";
	  	$db->update($table, $price, $id2, 1);
	  	$db->update($table, $regularPrice, $id2, 2);
	}

	//Insert new data into the form 
	function insertNewItem($name, $desc, $price, $quantity, $regularPrice, $image){
		$table = "Soup";
		$values = "('".$name."', '".$desc."', '".$price."', '".$quantity."', '".$regularPrice."', '".$image."')";
	   	$db = new DB();
	   	$db->insert($table, $values);
	  	$table = 'Cart';
	   	$db->insert($table, $values);
	  	echo "Inserted into Database.";
	}