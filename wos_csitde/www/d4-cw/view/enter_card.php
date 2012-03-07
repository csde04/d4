	<p class="p1">Simulate Entering a Card via a Card Reader ¦ 
	<?echo "<a href='".$current_file_name."?here=help#".$here."'> Help </a></p>";?>
		
	<div class=div_entercardform>
		<h3>Enter a Card</h3>
		<?
			// code to show a form with a drop down box with all venues and a field to enter a card number.
		?>
		
		<form name="entercardform">
		
		<input type="hidden" name="here" value="entercard"/>
		<input type="hidden" name="mode" value="confirmscan"/>
		
		<table name="entercardtab" border=0>
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
							echo "<option value='".$venue->id."'>".$venue->id." - ".$venue->referred_as."</option>";
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
							echo "<option value='".$atype->id."'>".$atype->id." - ".$atype->referred_as."</option>";
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Enter"/></td>
		</tr>
		</table> 
		</form> 
	</div>
	
	<?
	// ######################################### This should be moved to controller ##############################
	
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
			$accessStat = 4;
		}
		else
		{
			// check if card is not of type valid
			if( $card->status_id == 3)
			{
				?>
					<script type="text/javascript">
						alert( "Card status is Cancelled.");
					</script>
					<?
					$accessStat = 2;
			}
			{
				// check that the card and venue are associated
				$cardVenue = MyActiveRecord::FindBySql('card_venue', 'SELECT * FROM card_venue WHERE card_id = ' .$cardNumVal. ' AND venue_id = ' .$venueVal);
				if( $cardVenue == false)
				{
				//	echo "Card is not valid in this venue";
					?>
					<script type="text/javascript">
						alert( "Card is not valid at this venue.");
					</script>
					<?
					$accessStat = 4;
				}
				else
				{
					// 
					$currTime = strtotime( date("Y-m-d"));
					$startTime = $card->get_timestamp('startdate');
					$endTime = $card->get_timestamp('expirydate');
					
					/*
					$currDate = date("Y-m-d");
					$startDate = $card->startdate;
					$endDate = $card->expirydate;
					
					echo $currDate. " - ".$currTime." ? " .$startDate. " - ".$startTime." || " .$endDate. " - " .$endTime;
					*/
					
					// check if card is NOT valid in this time frame ie Expired
					if( $currTime > $endTime || $currTime < $startTime)
					{
						//echo "Not in the correct Time Period";
						?>
						<script type="text/javascript">
							alert( "Card is Expired");
						</script>
						<?
						$accessStat = 3;
						
						// change the card status to expired.
						//MyActiveRecord::Update( 'card', $card->id, array('status_id'=>2));
						$card->status_id = 2;
					}
					else
					{
						// card is valid and should be set to valid in the DB
						?>
						<script type="text/javascript">
							alert( "Card is valid.");
						</script>
						<?
						$accessStat = 1;
						if( $card->status_id == 2)
						{
							// change the card status to valid.
							//MyActiveRecord::Update( 'card', $card->id, array('status_id'=>1));
							$card->status_id = 1;
						}
					}
				}
			}
		}
		
		$card->save();
		
		// Insert into access table a new log record
		$arrAccess =  array('card_id' => $cardNumVal, 
									'date' => date( 'Y-m-d', time()),
									'time' => date( 'H:i:s', time()),
									'accesstype_id' => $aTypeVal, 
									'refered_as' => '-', 
									'accessstatus_id' => $accessStat, 
									'venue_id' => $venueVal);
		
		//echo "array" . print_r( $arrAccess);
		
		$accessLog = MyActiveRecord::Create( 'access', $arrAccess);
		
		$accessLog->save();
	}
	?>