<?php
include "LIB_project1.php";
//Get the top html that includes the title and the nav
echo getTopHTML();
//Get the items that have been added to the cart 
echo getItems('Quantity > 0',1,'Cart');
//Add a clear button clears the cart
echo "<form name='form' method='post' action=''>
	  	<input type='hidden' name='clear' value='clear'>
	  	<input type='submit' name='button' value='Clear Cart'>
      </form>";
//Get the total price for the cart 
echo "Total: ";
echo addTotals();

