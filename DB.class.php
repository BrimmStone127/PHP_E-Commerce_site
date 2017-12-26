<?php
	require_once "Soup.class.php";
	class DB{
		private $dbh;

		function __construct(){
			try{
				$this->dbh = new PDO("mysql:host={$_SERVER['DB_SERVER']};dbname={$_SERVER['DB']}", $_SERVER['DB_USER'],$_SERVER['DB_PASSWORD']);
				$this->dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			}catch (PDOException $e){
				echo $e->getMessage();
				die();
			}
		}
		
		/*
		This function performs the majority of all database queries. This handles all but one query for selecting and displaying data. It uses a mapped class: Soup.class.php. 
		PARAMETERS: $table - table that is being updated.
		$select - values to select
		$id - Where in the table to select it from 
		*/
		function getAllObjects($table, $select, $id){
			try{
				$data = array();
				$stmt = $this->dbh->prepare("SELECT $select FROM $table WHERE $id;");
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS,"Soup");
				while($stew = $stmt->fetch()){
					$data[] = $stew;
				}
				return $data;
			}catch(PDOException $e){
				echo $e->getMessage();
				die();
			}
		}

		//Selects the sales total for the cart 
		function salesCount($table, $select, $id){
			try{
				$data = array() ;
				$stmt = $this->dbh->prepare("SELECT $select FROM $table WHERE $id;");
				$stmt->execute();
				while($stew = $stmt->fetch()){
					$data = $stew;
				}
				return $data;
			}catch(PDOException $e){
				echo $e->getMessage();
				die();
			}
		}

		//clears the cart table using an update
		function clearCart(){
			try{
				$stmt = $this->dbh->prepare("UPDATE Cart SET Quantity = 0;");
				$stmt->bindParam(":id",$id,PDO::PARAM_STR);
				$stmt->execute(array("id"=>$id));
			}catch(PDOException $e){
				echo $e->getMessage();
				die();
			}
		}

		//Insert into the DB
		function insert($table, $vals){
			try{
				$stmt = $this->dbh->prepare("INSERT INTO $table (Name, Description, Price, RegularPrice, Quantity, Image) VALUES $vals;");
				$stmt->execute();
			}catch(PDOException $e){
				echo $e->getMessage();
				die();
			}
		}

		//Update the DB
		function update($table, $vals, $id, $type){
			if($type = 1){
				try{
					$stmt = $this->dbh->prepare("UPDATE $table SET Price = $vals WHERE SoupID = $id;");
					$stmt->execute();
				}catch(PDOException $e){
					echo $e->getMessage();
					die();
				}
			}
			if($type = 2){
				try{
					$stmt = $this->dbh->prepare("UPDATE $table SET RegularPrice = $vals WHERE SoupID = $id;");
					$stmt->execute();
				}catch(PDOException $e){
					echo $e->getMessage();
					die();
				}
			}
			
		}
		//Thes two functions are for handling the adding of items to the cart and lowering the quantity in stock
		//increment the quantity of an item in the cart table 
		function increment($id){
			try{
				$stmt = $this->dbh->prepare("UPDATE Cart SET Quantity = Quantity + 1 WHERE SoupID = :id;");
				$stmt->bindParam(":id",$id,PDO::PARAM_INT);
				$stmt->execute(array("id"=>$id));
			}catch(PDOException $e){
				echo $e->getMessage();
				die();
			}
		}

		//decrease the quantity of an item in the soup table once it has been added to the cart 
		function decrement($id){
			try{
				$stmt = $this->dbh->prepare("UPDATE Soup SET Quantity = Quantity - 1 WHERE SoupID = :id;");
				$stmt->bindParam(":id",$id,PDO::PARAM_INT);
				$stmt->execute(array("id"=>$id));
			}catch(PDOException $e){
				echo $e->getMessage();
				die();
			}
		}

	}//end of DB