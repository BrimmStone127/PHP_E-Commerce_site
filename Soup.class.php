<?php

	class Soup{
		private $SoupID;
		private $Name;
		private $Description;
		private $Price;
		private $RegularPrice;
		private $Quantity;
		private $Image;

		//Returns the price object
		public function selectTotal(){
			return "{$this->Price}";
		}
		//returns the item that is on sale
		public function itemsOnSale(){
			return "{$this->RegularPrice}";
		}
		//Will return the item on sale as well as its info a picture and a button to add it to the cart
		public function selectSale($check){
			if ($this->$check){
				return "<div id = item><img src={$this->Image} height='100' width='100'> <h2>{$this->Name}</h2> <p>{$this->Description}</p> <p>{$this->Price} regularly {$this->RegularPrice}</p> <p>Quantity: {$this->Quantity}
				<form name='form' method='post' action=''>
				<input type='hidden' name='id' value={$this->SoupID}>
				<input type='submit' name='button' value='Add to Cart'>
				</form></div>
				";
			}
		}
		//Will return the items in the catalog as well as its info a picture and a button to add it to the cart
		public function selectPrice($check){
			if (is_null($this->$check)){
				return "<div id = item><img src={$this->Image} height='100' width='100'><h2>{$this->Name}</h2> <p>{$this->Description}</p> <p>{$this->Price}</p> <p>Quantity: {$this->Quantity}</p>
				<form name='form' method='post' action=''>
				<input type='hidden' name='id' value={$this->SoupID}>
				<input type='submit' name='button' value='Add to Cart'>
				</form>
				</div>";
			}
		}
		//returns the options for the drop down menu in the admin.php
		public function getItems(){
			return "<option value= '{$this->SoupID}'>{$this->Name}</option>";
		}
		//returns an array of info that can be used to populate the form in admin.php
		public function getEdit(){
			return array($this->SoupID, $this->Name, $this->Description, $this->Price, $this->Quantity, $this->RegularPrice, $this->Image);
		}

}