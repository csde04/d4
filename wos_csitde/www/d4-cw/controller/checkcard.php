	<?
	// ######################################### should this be replaced with a nice JS popup box? yes most likely #########
	
	// this code is executed only when submit is pressed
	if( $mode == "confirmscan")
	{
		// this is where we check the DB to confirm that the card is valid and insert a log into the access table
		//echo "<div class=div_entercardform>";
		
		$cardNumVal = $_REQUEST['cardnum'];
		$venueVal = $_REQUEST['venue'];
		$aTypeVal = $_REQUEST['accesstype'];
		
		//echo "<h3>Card " .$cardNumVal. " Scanned:</h3>";
		
		// get the card from the DB and make sure it exists
		$card = MyActiveRecord::FindById('card', $cardNumVal);
		if( $card == false)
		{
		//	echo "Card Does not exist.";
			?>
			<script type="text/javascript">
				alert( "Card does not exist.");
			</script>
			<?
		}
		else
		{
			// check if card is of type valid
			//if( $card->status_id == 
				// check that the card and venue are associated
				$card_venue = MyActiveRecord::FindBySql('card_venue', 'SELECT * FROM card_venue WHERE card_id = ' .$cardNumVal. ' AND venue_id = ' .$venueVal);
				if( $card_venue == false)
				{
				//	echo "Card is not valid in this venue";
					?>
					<script type="text/javascript">
						alert( "Card is not valid at this venue.");
					</script>
					<?
				}
				else
				{
					
				}
		}
		
		// check that the card and venue are associated
		
		//echo "</div>";
	}
	?>