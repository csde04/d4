	<p class="p1">Simulate Entering a Card via a Card Reader</p>
	
	<?
	// this code is executed only when submit is pressed
	if( $mode == "confirmscan")
	{
		// this is where we check the DB to confirm that the card is valid and insert a log into the access table
		echo "<div class=div_entercardform>";
		
		$cardNumVal = $_REQUEST['cardnum'];
		$venueVal = $_REQUEST['venue'];
		$aTypeVal = $_REQUEST['accesstype'];
		
		echo "<p>Card " .$cardNumVal. " Scanned:</p>";
		
		// get the card from the DB and make sure it exists
		$card = MyActiveRecord::FindById('card', $cardNumVal);
		if( $card == false)
		{
			echo "Card Does not exist.";
		}
		else
		{
			// check if card is of type valid
			if( $card->status_id
				// check that the card and venue are associated
				$card_venue = MyActiveRecord::FindBySql('card_venue', 'SELECT * FROM card_venue WHERE card_id = ' .$cardNumVal. ' AND venue_id = ' .$venueVal);
				if( $card_venue == false)
				{
					echo "Card is not valid in this venue";
				}
				else
				{
					
				}
		}
		
		// check that the card and venue are associated
		
		echo "</div>";
	}
	?>
	
	<div class=div_entercardform>
		<p>Please enter a card number and select a venue:</p>
		<?
			// code to show a form with a drop down box with all venues and a field to enter a card number.
		?>
		
		<form name="entercardform">
		
		<input type="hidden" name="here" value="entercard"/>
		<input type="hidden" name="mode" value="confirmscan"/>
		
		<table name="entercardtab" border="0">
		<tr>
			<td>Card Number:</td>
			<td><input type="text" name="cardnum" /></td>
		</tr>
		<tr>
			<td>Venue:</td>
			<td>
			
				<select name="venue">
					<?
						// Grab all venues from DB and list in select box.
						$venues = MyActiveRecord::FindBySql('Venue', 'SELECT * FROM venue WHERE id > -1 ORDER BY id');
						foreach ($venues as $venue)
						{
							?><option value="<?echo $venue->id;?>"><?echo $venue->id;?> - <?echo $venue->referred_as;?></option><?
						}
					?>
				</select>
			
			</td>
		</tr>
		<tr>
			<td>Access Type:</td>
			<td>
				<select name="accesstype">
					<?
						// Grab all accesstypes from DB and list in select box.
						$atypes = MyActiveRecord::FindBySql('Accesstype', 'SELECT * FROM accesstype WHERE id > -1 ORDER BY id');
						foreach ($atypes as $atype)
						{
							?><option value="<?echo $atype->id;?>"><?echo $atype->id;?> - <?echo $atype->referred_as;?></option><?
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Scan" /></td>
		</tr>
		</table> 
		</form> 
	</div>