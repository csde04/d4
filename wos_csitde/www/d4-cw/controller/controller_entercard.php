<?
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
					$currTime = time();
					$startTime = $card->get_timestamp('startdate');
					$endTime = $card->get_timestamp('expirydate');

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
		/*$sqlout = MyActiveRecord::Query( 'UPDATE access SET 
												card_id='.$cardNumVal.', 
												time=FROM_UNIXTIME('.$time.'), 
												accesstype_id='.$aTypeVal.',
												accessstatus_id='.$accessStat.',
												venue_id='.$venueVal);
		
		echo $sqlout;
		*/
		$arrAccess =  array('card_id' => $cardNumVal, 
									//'time' => "FROM_UNIXTIME('".$currTime."')", 
									'time' => date( 'Y-m-d H:i:s', $currTime),
									'accesstype_id' => $aTypeVal, 
									'refered_as' => '-', 
									'accessstatus_id' => $accessStat, 
									'venue_id' => $venueVal);
		
		//echo "array" . print_r( $arrAccess);
		
		$accessLog = MyActiveRecord::Create( 'access', $arrAccess);
		
		$accessLog->save();
	}
	?>