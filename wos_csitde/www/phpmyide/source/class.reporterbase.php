<?php
require_once("$classPath/class.ezpdf.php");
//require_once("$classPath/class.debuglog.php");

class eds_ezpdf extends Cezpdf {
	
	var $_HELVETICA;
	var $_TIMES;
	var $_COURIER;
	
	function eds_ezpdf($size='LETTER', $layout='portrait') {
		parent::Cezpdf($size, $layout);
        	$this->_HELVETICA = "{$GLOBALS['fontPath']}/Helvetica.afm";
        	if (!file_exists($this->_HELVETICA)) die('class.reporterbase.php: Missing font file {$this->_HELVETICA}');
        	$this->_TIMES = "{$GLOBALS['fontPath']}/Times-Roman.afm";
        	$this->_COURIER = "{$GLOBALS['fontPath']}/Courier.afm";
        	if (!file_exists($this->_COURIER)) die('class.reporterbase.php: Missing font file: {$this->_COURIER}');
	}

	function hexSetColor($inStr)
	{
		$inStr = strtoupper($inStr);
		preg_match('/([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})/', $inStr, $parts);
		$red = hexdec($parts[1]) / 255;
		$green = hexdec($parts[2]) / 255;
		$blue = hexdec($parts[3]) / 255;
		$this->setColor($red, $green, $blue);
	}

	function hexSetStrokeColor($inStr)
	{
		$inStr = strtoupper($inStr);
		preg_match('/([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})/', $inStr, $parts);
		$red = hexdec($parts[1]) / 255;
		$green = hexdec($parts[2]) / 255;
		$blue = hexdec($parts[3]) / 255;
		$this->setStrokeColor($red, $green, $blue);
	}

	function _ColorIntToFloat($inVal)
	{
		if ( ($inVal < 0) or ($inVal > 255) ) die("_ColorIntToFloat requires a value between 0 and 255");
		return $inVal / 255;
	}	
	
	function DBDateToUTC($inVal)
	{
        $year = substr($inVal, 0, 4);
        $month = substr($inVal, 5, 2);
        $day = substr($inVal, 8, 2);
        return gmmktime(12, 0, 0, $month, $day, $year);
	}
	
	function NVPairsToArray($inStr)
	{
		// This function assumes a CRLF delimed list of Name=Value pairs.
		$lines = explode(chr(10), trim($inStr));		
		foreach($lines as $line) {
			if ($line <= ' ') { continue; }
			$pair = explode('=', $line);			
			$outArr[$pair[0]] = $pair[1];
		}
		return $outArr;
	}
}

class reporterbase extends eds_ezpdf {

	var $_lineFont;
	var $_lineFontSize;
	var $_lc_Red;
	var $_lc_Green;
	var $_lc_Blue;
	var $_startingUp;
	var $_pageNum;
	var $_marginTop;
	var $_marginRight;
	var $_marginBottom;
	var $_marginLeft;
	var $_oMarginLeft;
	var $_oMarginRight;
	var $_belowHeaderY;
	var $_events = array();
	var $_columns = array();
	var $_columnCount;
	var $_rowCount;
	var $_currY;
	var $_currRowHeight;
	var $_lr_Red;
	var $_lr_Green;
	var $_lr_Blue;
	var $_dr_Red;
	var $_dr_Green;
	var $_dr_Blue;
	var $_headLineY;
	var $_footLineY;
	var $_outputFile;
	var $_startedNewPage;
	var $_multiColumnReport;
	var $_multiColumns = array();
	var $_multiColumnPtr;
	var $_multiColumnCount;
	var $_outputMode;

	var $_sectionPtr;
	var $_sectionY;
	var $_sectionOn;
	
	var $ReportTitle; 
	var $ReportSubTitle;
	var $SpacerHeight;
	var $DefaultLeftPad;
	var $DefaultRightPad;
	var $ReportHasHeaderRows;
	var $NextRowDark;
	var $VerticalLines;
	var $HeaderFontSize;
	var $PDFString;
	var $LastResult;
	var $magicTop;
	var $magicRight;
	
	// Constructor
    function reporterbase($size='LETTER', $layout='portrait') {
    		parent::eds_ezpdf($size, $layout);

		switch(strtolower($layout))
		{
			case 'landscape':
				$this->magicTop = 612;
				$this->magicRight = 792;
				break;
				
			case 'portrait':
			default:
				$this->magicTop = 792;
				$this->magicRight = 612;
				break;
		}
		
		$this->ClearAll();
	}
	
	function ClearAll()
	{
		$this->_events = array();
		$this->_columns = array();
		$this->_multiColumns = array();

   		$this->ReportTitle = 'Report Title';
   		$this->ReportSubTitle = 'SubTitle';
   		$this->LineFont('helvetica', 9);
   		$this->LightRowColor(255, 255, 255);
   		$this->DarkRowColor(224, 224, 224);
   		$this->LineFontColor(0, 0, 0);
   		$this->SetMargins(30, 30, 30, 30);
 		$this->SpacerHeight = 2;
 		$this->DefaultLeftPad = 0;
 		$this->DefaultRightPad = 15;
 		$this->ReportHasHeaderRows = false;
 		$this->VerticalLines = false;
 		$this->LineWeight = 0.3;
 		$this->HeaderFontHeight = 14;
 		$this->PDFString = '';
 		$this->LastResult = '';
 		
 		$this->_columnCount = 0;
 		$this->_rowCount = -1;
 		$this->_outputFile = '';
 		$this->_multiColumnReport = false;
 		$this->_outputMode = 0; // stream is the default
		
 		$this->BuildStructure();
 	}
 	
 	function AddBulletColumn($name, $radius=2, $just='center', $lPadSize=5, $rPadSize=5) {
 		$c = $this->_columnCount;
 		$this->_columns[$c]['name'] = $name; // Used to reference the column
 		$this->_columns[$c]['caption'] = ''; // For use with "Header Rows"
 		$this->_columns[$c]['type'] = 'bullet'; // Column type
 		$this->_columns[$c]['radius'] = $radius;
 		$this->_columns[$c]['width'] = $lPadSize + $rPadSize + ($radius * 2);
 		$this->_columns[$c]['lPadSize'] = $lPadSize; // Left side padding
 		$this->_columns[$c]['rPadSize'] = $rPadSize; // Right side padding
 		$this->_columns[$c]['justification'] = $just; // Column justification
 		$this->_columns[$c]['lPos'] = 0; // Eventual column left margin
 		$this->_columns[$c]['rPos'] = 0; // Eventual column right margin
 		$this->_columns[$name] = $c; // This is a hash value for me to find the column later
 		$this->_columnCount++;		
 	}
 	
	function AddCheckboxColumn($name, $width=18, $size=9, $just='center') {
 		$c = $this->_columnCount;
 		$this->_columns[$c]['name'] = $name; // Used to reference the column
 		$this->_columns[$c]['type'] = 'checkbox'; // Column type
		$this->_columns[$c]['width'] = $width; // Column width
 		$this->_columns[$c]['size'] = $size; // size of checkbox
 		$this->_columns[$c]['justification'] = $just; // Column justification
 		$this->_columns[$c]['lPos'] = 0; // Eventual column left margin
 		$this->_columns[$c]['rPos'] = 0; // Eventual column right margin
		$this->_columns[$c]['default'] = '1'; // This makes sure one of these always shows up...
 		$this->_columns[$name] = $c; // This is a hash value for me to find the column later
 		$this->_columnCount++;		
	}
	
 	function AddNumericColumn($name, $caption='__', $format=0, $decimals=0, $just='right', $lPadSize=-32767, $rPadSize=-32767) {
 		if ($caption == '__') { $caption = $name; }
 		if ($rPadSize == -32767) { $rPadSize = $this->DefaultRightPad; }
 		if ($lPadSize == -32767) { $lPadSize = $this->DefaultLeftPad; }
 		$c = $this->_columnCount;
 		$this->_columns[$c]['name'] = $name; // Used to reference the column
 		$this->_columns[$c]['caption'] = $caption; // For use with "Header Rows"
 		$this->_columns[$c]['type'] = 'numeric'; // Column type
 		$this->_columns[$c]['format'] = $format;
 		$this->_columns[$c]['decimals'] = $decimals;
 		$this->_columns[$c]['width'] = 0; // Column width - (calc as I add data)
 		$this->_columns[$c]['lPadSize'] = $lPadSize; // Left side padding
 		$this->_columns[$c]['rPadSize'] = $rPadSize; // Right side padding
 		$this->_columns[$c]['justification'] = $just; // Column justification
 		$this->_columns[$c]['lPos'] = 0; // Eventual column left margin
 		$this->_columns[$c]['rPos'] = 0; // Eventual column right margin
 		$this->_columns[$name] = $c; // This is a hash value for me to find the column later
 		$this->_columnCount++;		
 	}
 	
 	function AddSpacerColumn($name, $width) {
 		$c = $this->_columnCount;
 		$this->_columns[$c]['name'] = $name; // Used to reference the column
 		$this->_columns[$c]['type'] = 'spacer'; // Column type
 		$this->_columns[$c]['width'] = $width; // Column width - (calc as I add data)
 		$this->_columns[$c]['lPos'] = 0; // Eventual column left margin
 		$this->_columns[$c]['rPos'] = 0; // Eventual column right margin
		$this->_columns[$c]['default'] = '1'; // This makes sure one of these always shows up...
 		$this->_columns[$name] = $c; // This is a hash value for me to find the column later
 		$this->_columnCount++;		
 	} 		
 	
 	function AddTextColumn($name, $caption='__', $just='left', $lPadSize=-32767, $rPadSize=-32767) {
 		if ($caption == '__') { $caption = $name; }
 		if ($rPadSize == -32767) { $rPadSize = $this->DefaultRightPad; }
 		if ($lPadSize == -32767) { $lPadSize = $this->DefaultLeftPad; }
 		$c = $this->_columnCount;
 		$this->_columns[$c]['name'] = $name; // Used to reference the column
 		$this->_columns[$c]['caption'] = $caption; // For use with "Header Rows"
 		$this->_columns[$c]['type'] = 'text'; // Column type
 		$this->_columns[$c]['width'] = 0; // Column width - (calc as I add data)
 		$this->_columns[$c]['lPadSize'] = $lPadSize; // Left side padding
 		$this->_columns[$c]['rPadSize'] = $rPadSize; // Right side padding
 		$this->_columns[$c]['justification'] = $just; // Column justification
 		$this->_columns[$c]['lPos'] = 0; // Eventual column left margin
 		$this->_columns[$c]['rPos'] = 0; // Eventual column right margin
 		$this->_columns[$name] = $c; // This is a hash value for me to find the column later
 		$this->_columnCount++;
 	}
 	
 	function AddTextColumnFixed($name, $width, $caption='__', $just='left', $lPadSize=-32767, $rPadSize=-32767) {
 		if ($caption == '__') { $caption = $name; }
 		if ($rPadSize == -32767) { $rPadSize = $this->DefaultRightPad; }
 		if ($lPadSize == -32767) { $lPadSize = $this->DefaultLeftPad; }
 		$c = $this->_columnCount;
 		$this->_columns[$c]['name'] = $name; // Used to reference the column
 		$this->_columns[$c]['caption'] = $caption; // For use with "Header Rows"
 		$this->_columns[$c]['type'] = 'textf'; // Column type
 		$this->_columns[$c]['width'] = $width; // Column width - (calc as I add data)
 		$this->_columns[$c]['lPadSize'] = $lPadSize; // Left side padding
 		$this->_columns[$c]['rPadSize'] = $rPadSize; // Right side padding
 		$this->_columns[$c]['justification'] = $just; // Column justification
 		$this->_columns[$c]['lPos'] = 0; // Eventual column left margin
 		$this->_columns[$c]['rPos'] = 0; // Eventual column right margin
 		$this->_columns[$name] = $c; // This is a hash value for me to find the column later
 		$this->_columnCount++;
 	}
 	
 	function afterFooter() {
    		if ($this->VerticalLines) {    			
    			for ($i=0; $i<$this->_columnCount; $i++) {
    				$lPos = $this->_columns[$i]['lPos'];
    				$lPos += $this->_marginLeft;
    				$this->setLineStyle(0.3);
    				$this->setColor(0, 0, 0);
    				$this->line($lPos, $this->_headLineY, $lPos, $this->_footLineY);			
    			}	
    			$this->line($this->_marginRight, $this->_headLineY, $this->_marginRight, $this->_footLineY);
    		}
    		
    		if ($this->_multiColumnReport) {
    			$this->setLineStyle(0.3);
    			$this->setColor(0, 0, 0);
    			for ($i=0; $i<=$this->_multiColumnPtr; $i++) {
    				if ($this->_multiColumns[$i]['vertDiv'] > -1) {
    					$x = $this->_multiColumns[$i]['vertDiv'];
	    				$this->line($x, $this->_belowHeaderY, $x, $this->_currY);			
    				}					
    			}	
    		}
 	}
		
	function afterHeader() {
		$this->_startedNewPage = true;
		$this->_belowHeaderY = $this->_currY;
	}
	
	function BoundingBox() {
		$this->rectangle($this->_marginLeft, $this->_marginBottom, ($this->_marginRight - $this->_marginLeft), ($this->_marginTop - $this->_marginBottom));
	}
    
    function BuildStructure() {
    		die('ReporterBase.BuildStructure: You must override this function');
    }
    
    function ColumnData($colName, $colValue) 
	{
    	$cPtr = $this->_columns[$colName];
		$type = $this->_columns[$cPtr]['type'];
   		$r = $this->_rowCount;
		if ($this->_events[$r]['__method'] == 'header') { $type = 'text'; }
    		
    	switch($type) {
   			case 'bullet' :
   				$this->_events[$r][$colName] = $colValue;
    				
   			case 'numeric' :
   				$format = $this->_columns[$cPtr]['format'];
   				$decimals = $this->_columns[$cPtr]['decimals'];
   				$thisLPad = $this->_columns[$cPtr]['lPadSize'];
   				$thisRPad = $this->_columns[$cPtr]['rPadSize'];
   				switch ($format) 
				{
   					case 0 :
    					$numStr = number_format($colValue, $decimals, '.', '');
   						break;
   					case 1 :
   						$numStr = number_format($colValue, $decimals, '.', '');
   						break;
   					case 2 :
   						$numStr = number_format($colValue, $decimals, '.', ',');
   						break;
   					case 3 :
   						if ($colValue < 0) {
   							$numStr = '($' . number_format(abs($colValue), 2, '.', ',') . ')';
   						} else {
   							$numStr = '$' . number_format($colValue, 2, '.', ',');
   						}
   						break;
   					case 4 :
   						$colValue *= 100;
   						$numStr = number_format($colValue, $decimals, '.', '') . '%';
   						break;
   				}
	    		$this->_events[$r][$colName] = $numStr;
				$thisW = $this->getTextWidth($this->_lineFontSize, $numStr) + $thisLPad + $thisRPad;
				if ($thisW > $this->_columns[$cPtr]['width']) { $this->_columns[$cPtr]['width'] = $thisW; }
   				break;
    				
   			case 'text' :
   				// NN2Recalculate the width of the field for these types...
   				$thisLPad = $this->_columns[$cPtr]['lPadSize'];
   				$thisRPad = $this->_columns[$cPtr]['rPadSize'];
				$thisW = $this->getTextWidth($this->_lineFontSize, $colValue) + $thisLPad + $thisRPad;
				if ($thisW > $this->_columns[$cPtr]['width']) { $this->_columns[$cPtr]['width'] = $thisW; }
	    		$this->_events[$r][$colName] = $colValue;
    			break;
    				
    		case 'textf' :
	    		$this->_events[$r][$colName] = $colValue;
    			break;
    	}
    }

    	function ColumnDataEx($colName, $colData, $just="default", $bold=false, $italic=false, $size=0)
    	{
		$this->ColumnData($colName, $colData);
                $r = $this->_rowCount;
		$tempVal = $this->_events[$r][$colName]; 
		if ($bold) { $tempVal = "<b>$tempVal</b>"; }
		if ($italic) { $tempVal = "<i>$tempVal</i>"; }
		$this->_events[$r][$colName] = $tempVal;

		if ($just <> 'default') { $this->_events[$r][$colName . '__ovr_just'] = $just; }
		if ($size > 0) { $this->_events[$r][$colName . '__ovr_size'] = $size; }

    	}
    
    function Commit($sendOutput=true, $doFooter=true, $startReport=true) {

    		// this is where the actual PDF gets written...
    		if ($startReport) $this->StartReport();
    		$this->StructureColumns();
    		$darkRow = true;
    		$rowColorOverride = false;
    		$fontColorOverride = false;
    		$fontRed = 0;
    		$fontBreen = 0;
    		$fontBlue = 0;
    		$rowRed = 0;
    		$rowGreen = 0;
    		$rowBlue = 0;
    		$lastRow = false;
    		$this->_sectionOn = false;
    		
$debug = &$_SERVER['debug'];

    		for ($i=0; $i<=$this->_rowCount; $i++) {
    			$lastRow = ($i == $this->_rowCount);
    			
    			$method = $this->_events[$i]['__method'];
    			$header = false;
    			
    			switch ($method) {
    				case 'block':
    					$value = $this->_events[$i]['__text'];
    					$fSize = $this->_events[$i]['__size'];
    					$just = $this->_events[$i]['__just'];
    					$fHeight = $this->getFontHeight($fSize);
    					$fRed = $this->_events[$i]['__fc_Red'];
    					$fGreen = $this->_events[$i]['__fc_Green'];
    					$fBlue = $this->_events[$i]['__fc_Blue'];
    					$bRed = $this->_events[$i]['__rc_Red'];
    					$bGreen = $this->_events[$i]['__rc_Green'];
    					$bBlue = $this->_events[$i]['__rc_Blue'];
                        $bold = $this->_events[$i]['__mod_bold'];
                        $italic = $this->_events[$i]['__mod_italic'];
						$indent = $this->_events[$i]['__mod_indent'];
						
						// The block will actually be a small array where the <br>s are...
						$value = str_replace(array(0=>chr(10), 1=>chr(13)), ' ', $value);
						while (strpos($value, '  ')) { $value = str_replace('  ', ' ', $value); }
						$value = str_replace('<BR>', '<br>', $value);
						$paragraphs = explode('<br>', $value);
					    				
		    			$this->setColor($fRed, $fGreen, $fBlue);
		    			$x = $this->_marginLeft;
		    			$wid = $this->_marginRight - $this->_marginLeft;
						foreach($paragraphs as $para)
						{
							$para = trim($para);
							if ($para <= ' ') { $para = ' '; }
							$para = str_replace('&nbsp;', ' ', $para);
							while ($para)
							{
								// First, make sure that a new line won't push me over the page...
		    					if ($this->_currY <= ($this->_marginBottom + ($fHeight + 5))) 
		    					{ 

									if ($this->_sectionOn)
									{
										// Uh oh... we are trying to feed, but we are in the middle of a block.
										// Unwind the current transaction, reset the internal pointers to where we
										// where when the transaction started then let the feed go on...
										$this->transaction('rewind');
										$i = $this->_sectionPtr;
										$this->_currY = $this->sectionY;
									}
    
		    						$this->NewReportPage(); 
		    					}
	    						
	    						// Now calc where <this> line is supposed to Y...
								$y = $this->_currY - $fHeight;
							
								// Drop the line on the page...
				    			$this->setColor($fRed, $fGreen, $fBlue);
				    			$this->setStrokeColor($fRed, $fGreen, $fBlue);
								$para = $this->addTextWrap($x, $y, $wid, $fSize, $para, $just);
							
								// Move the Y and feed if necessary...
		    					$this->_currY -= $fHeight;
	    						if ($this->_currY <= ($this->_marginBottom + 30)) 
	    						{ 
	    						
									if ($this->_sectionOn)
									{
										// Uh oh... we are trying to feed, but we are in the middle of a block.
										// Unwind the current transaction, reset the internal pointers to where we
										// where when the transaction started then let the feed go on...
										$this->transaction('rewind');
										$i = $this->_sectionPtr;
										$this->_currY = $this->sectionY;
									}

	    							$this->NewReportPage(); 
	    						}
	    						$this->_startedNewPage = false;
	    					}
						}

    					break;
    					
    				case 'cnewpage' :
    					$fromBottom = $this->_events[$i]['__border'];
    					if (($this->_currY - $this->_marginBottom) <= $fromBottom) { $this->NewReportPage(); }
    					break; 
    				    
    				case 'colorfont' :
    					$fontRed = $this->_events[$i]['__fc_red'];
    					$fontGreen = $this->_events[$i]['__fc_green'];
    					$fontBlue = $this->_events[$i]['__fc_blue'];
    					$fontColorOverride = true;    					    					
    					break;
    					
    				case 'colorrow' :
    					$rowRed = $this->_events[$i]['__rc_red'];
    					$rowGreen = $this->_events[$i]['__rc_green'];
    					$rowBlue = $this->_events[$i]['__rc_blue'];
    					$rowColorOverride = true;    					    					
    					break;
    					
    				case 'event':
    					$funcName = $this->_events[$i]['__func'];
    					for ($ii=0; $ii<10; $ii++)
    						$p[$ii] = $this->_events[$i]['__parms'][$ii];
    					$this->$funcName($p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9]);
    					if ($this->_currY <= $this->_marginBottom) 
    					{ 

							if ($this->_sectionOn)
							{
								// Uh oh... the event has pushed us below the lower margin... need to take another shot at it...
								// Unwind the current transaction, reset the internal pointers to where we
								// where when the transaction started then let the feed go on...
								$this->transaction('rewind');
								$i = $this->_sectionPtr;
								$this->_currY = $this->sectionY;
							}

    						$this->NewReportPage(); 
    					}
    					
    				case 'exception' :
    					$value = $this->_events[$i]['__text'];
    					$fSize = $this->_events[$i]['__size'];
    					$just = $this->_events[$i]['__just'];
    					$fHeight = $this->getFontHeight($fSize);
    					$fRed = $this->_events[$i]['__fc_Red'];
    					$fGreen = $this->_events[$i]['__fc_Green'];
    					$fBlue = $this->_events[$i]['__fc_Blue'];
    					$bRed = $this->_events[$i]['__rc_Red'];
    					$bGreen = $this->_events[$i]['__rc_Green'];
    					$bBlue = $this->_events[$i]['__rc_Blue'];
                        $bold = $this->_events[$i]['__mod_bold'];
                        $italic = $this->_events[$i]['__mod_italic'];
						$indent = $this->_events[$i]['__mod_indent'];
					    				
    					if ($this->_currY <= ($this->_marginBottom + ($fHeight + 5))) 
    					{ 

							if ($this->_sectionOn)
							{
								// Uh oh... we are trying to feed, but we are in the middle of a block.
								// Unwind the current transaction, reset the internal pointers to where we
								// where when the transaction started then let the feed go on...
								$this->transaction('rewind');
								$i = $this->_sectionPtr;
								$this->_currY = $this->sectionY;
							}

    						$this->NewReportPage(); 
    					}

			    		$this->setColor($bRed, $bGreen, $bBlue);
    					$this->filledRectangle($this->_marginLeft, $this->_currY - 1, $this->_marginRight - $this->_marginLeft, -($fHeight + 1));

		    			$this->setColor($fRed, $fGreen, $fBlue);

						if ($italic) { $value = '<i>' . $value . '</i>'; }
						if ($bold) { $value = '<b>' . $value . '</b>'; }

						$y = $this->_currY - $fHeight; 
						switch ($just) 
						{
    						case 'right' :
    							$x = $this->_marginRight - $this->getTextWidth($fSize, $value); 
								$x -= $indent;
    							break;
    						case 'center' :
    							$x = ($this->_marginRight - $this->_marginLeft) / 2;
    							$tWidth = $this->getTextWidth($fSize, $value) / 2;
    							$x -= $tWidth;
    							break;
    						case 'left' :
    						default:
    							$x = $this->_marginLeft;
								$x += $indent;
    							break;
	   				}
    					$this->addText($x, $y, $fSize, $value);

    					$this->_currY -= ($fHeight + $this->SpacerHeight);
    					if ($this->_currY <= ($this->_marginBottom + 30)) 
    					{ 

							if ($this->_sectionOn)
							{
								// Uh oh... we are trying to feed, but we are in the middle of a block.
								// Unwind the current transaction, reset the internal pointers to where we
								// where when the transaction started then let the feed go on...
								$this->transaction('rewind');
								$i = $this->_sectionPtr;
								$this->_currY = $this->sectionY;
							}

    						$this->NewReportPage(); 
    					}
    					$this->_startedNewPage = false;

    					break;
    					
    				case 'image':
    					$file = $this->_events[$i]['__file'];
    					$fHeight = $this->_events[$i]['__height'];
    					$fWidth = $this->_events[$i]['__width'];
    					$just = $this->_events[$i]['__just'];
    					switch ($just)
    					{
    						case 'left':
    							$x = $this->_marginLeft;
    							break;
    						case 'right':
    							break;
    						case 'center':
    							break;
    					}
    					$this->addJpegFromFile($file, $x, $this->_currY - $fHeight, $fWidth, $fHeight);
    					$this->_currY -= $fHeight;
    					if ($this->_currY <= ($this->_marginBottom + 30)) 
    					{ 

							if ($this->_sectionOn)
							{
								// Uh oh... we are trying to feed, but we are in the middle of a block.
								// Unwind the current transaction, reset the internal pointers to where we
								// where when the transaction started then let the feed go on...
								$this->transaction('rewind');
								$i = $this->_sectionPtr;
								$this->_currY = $this->sectionY;
							}

    						$this->NewReportPage(); 
    					}
		
    					break;
    									
    				case 'line' :
    					$thickness = $this->_events[$i]['__line_thickness'];
    					$red = $this->_ColorIntToFloat($this->_events[$i]['__line_red']);
    					$green = $this->_ColorIntToFloat($this->_events[$i]['__line_green']);
    					$blue = $this->_ColorIntToFloat($this->_events[$i]['__line_blue']);
    					$this->setColor($red, $green, $blue); 
    					$this->setLineStyle($thickness);
    					$this->line($this->_marginLeft, $this->_currY - 1, $this->_marginRight, $this->_currY - 1);
    					$this->_currY -= ($thickness + 2);
    					break;
    					
    				case 'nextdark' :
    					$darkRow = $this->_events[$i]['__darkRow'];
    					break;
    					
    				case 'header' :
    					$header = true;
    					
    				case 'normal' :
		    			$c = $this->_columnCount;
		    			$this->selectFont($this->_lineFont);

						// New: See if there are any column specific size overrides and adjust accordingly...
    					$fSize = $this->_lineFontSize;
						$newSize = $fSize;
                        for ($j=0; $j<$c; $j++) {
							$name = $this->_columns[$j]['name'];
							if($this->_events[$i][$name . '__ovr_size'] > $newSize) { $newSize = $this->_events[$i][$name . '__ovr_size']; }
						}
    					$fHeight = $this->getFontHeight($newSize);

    					$this->_currRowHeight = $fHeight; // Memos might change this...
    					$bold = $this->_events[$i]['__mod_bold'];
    					$italic = $this->_events[$i]['__mod_italic'];
    					
    					if ($this->_currY <= ($this->_marginBottom + ($fHeight * 2) + 15)) 
    					{ 

							if ($this->_sectionOn)
							{
								// Uh oh... we are trying to feed, but we are in the middle of a block.
								// Unwind the current transaction, reset the internal pointers to where we
								// where when the transaction started then let the feed go on...
								$this->transaction('rewind');
								$i = $this->_sectionPtr;
								$this->_currY = $this->sectionY;
							}

    						$this->NewReportPage(); 
    					}
    							
    					if ($rowColorOverride) {    						
	    					$this->setColor($rowRed, $rowGreen, $rowBlue);
    						$rowColorOverride = false;
							$finalRed = $rowRed;
							$finalGreen = $rowGreen;
							$finalBlue = $rowBlue;
    					} else {
		    				if ($darkRow) { 
	    						$this->setColor($this->_dr_Red, $this->_dr_Green, $this->_dr_Blue);
    							$darkRow = false; 
								$finalRed = $this->_dr_Red;
								$finalGreen = $this->_dr_Green;
								$finalBlue = $this->_dr_Blue;
    						} else { 
			    				$this->setColor($this->_lr_Red, $this->_lr_Green, $this->_lr_Blue);
								$finalRed = $this->_lr_Red;
								$finalGreen = $this->_lr_Green;
								$finalBlue = $this->_lr_Blue;
    							$darkRow = true; 
    						}
    						$this->NextRowDark = $darkRow;
    					}
    					$this->filledRectangle($this->_marginLeft, $this->_currY - 1, $this->_marginRight - $this->_marginLeft, -($this->_currRowHeight + 1));

						if ($fontColorOverride) {
			    				$this->setColor($fontRed, $fontGreen, $fontBlue);
								$finalFontRed = $fontRed;
								$finalFontGreen = $fontGreen;
								$finalFontBlue = $fontBlue;
		    					$fontColorOverride = false;
						} else {    			
			    				$this->setColor($this->_lc_Red, $this->_lc_Green, $this->_lc_Blue);
								$finalFontRed = $this->_lc_Red;
								$finalFontGreen = $this->_lc_Green;
								$finalFontBlue = $this->_lc_Blue;
						}
		    			
    					for ($j=0; $j<$c; $j++) 
						{
    						$name = $this->_columns[$j]['name'];
    						$value = $this->_events[$i][$name];
							$force = $this->_columns[$j]['default'];
    						$oValue = $value;
    						if ( (!$value) and ($value <> '0') and (!$force)){ continue; }
    						if ($italic) { $value = '<i>' . $value . '</i>'; }    						
    						if ($bold) { $value = '<b>' . $value . '</b>'; }    						
    						$lPos = $this->_columns[$j]['lPos'];
    						$lPad = $this->_columns[$j]['lPadSize'];
    						$rPos = $this->_columns[$j]['rPos'];
    						$rPad = $this->_columns[$j]['rPadSize'];
    						$just = $this->_columns[$j]['justification'];	
							$type = $this->_columns[$j]['type'];
						
							// New column specific overrides...
							if ($this->_events[$i][$name . '__ovr_just']) { $just = $this->_events[$i][$name . '__ovr_just']; }
							$cellSize = $fSize;
							if ($this->_events[$i][$name . '__ovr_size']) { $cellSize = $this->_events[$i][$name . '__ovr_size']; }
							switch ($type) 
							{
								case 'memo' :
									break;
								
								case 'bullet' :
									$center = (($rPos - $lPos) / 2) + $lPos;
									$middle = ($this->_currY) - ($fHeight / 2) - 2;
									$radius = $this->_columns[$j]['radius'];
									$this->setLineStyle(0.5);
									$center += $this->_marginLeft;
									$this->ellipse($center, $middle, $radius);
									break;
								
								case 'checkbox':
									$size = $this->_columns[$j]['size'];
	   								$x = $lPos + $this->_marginLeft;
									$this->setLineStyle(0.5);
									$y = $this->_currY - $fHeight;
									$this->filledrectangle($x, $y, $size, $size);
									$y += 1;
									$x -= 1;
									$this->filledrectangle($x, $y, $size, $size);
									$y -= 1;
									$x += 1;
			    					$this->setColor(255, 255, 255);
									$this->filledrectangle($x, $y + 2, $size - 2, $size - 2);
			    					$this->setColor($finalFontRed, $finalFontGreen, $finalFontBlue);
									break;
								
								case 'text' :
								case 'textf' :
								case 'numeric' :
									$y = $this->_currY - $fHeight;
	    							switch ($just) 
									{
    									case 'left' :
    										$x = $lPos + $lPad;
    										break;
    									case 'right' :
    										$x = $rPos - ($this->getTextWidth($cellSize, $value) + $rPad); 
    										break;
	    								case 'center' :
    										$x = (($rPos - $lPos) / 2) + $lPos;
    										$tWidth = $this->getTextWidth($cellSize, $oValue) / 2;
    										$x -= $tWidth;
    										break;
	   								}
	   								$x += $this->_marginLeft;
    								$this->addText($x, $y, $cellSize, $value);
	   								break;
    						}
    					}
    					$this->_currY -= ($this->_currRowHeight + $this->SpacerHeight);
    					if ( ($this->_currY <= ($this->_marginBottom + 30)) and (!$lastRow) ) 
    					{ 

							if ($this->_sectionOn)
							{
								// Uh oh... we are trying to feed, but we are in the middle of a block.
								// Unwind the current transaction, reset the internal pointers to where we
								// where when the transaction started then let the feed go on...
								$this->transaction('rewind');
								$i = $this->_sectionPtr;
								$this->_currY = $this->sectionY;
							}

    						$this->NewReportPage(); 
    					}
    					$this->_startedNewPage = false;
    					break;
						
					case 'pagefeed':
						$this->NewReportPage(true);
						break;
						
					case 'sectionStart':
						$this->_sectionPtr = $i;
						$this->_sectionY = $this->_currY;
						$this->_sectionOn = true;
						$this->transaction('start');
						break;
						
					case 'sectionComplete':
						$this->_sectionOn = false;
						$this->transaction('commit');
						break;
    				
    				case 'spacer' :
    					// I only do a spacer if it is NOT the first line of the page...
    					if (!$this->_startedNewPage) {
    						$height = $this->_events[$i]['__spacer_height'];
    						$this->_currY -= $height; 
    						if ($this->_currY <= ($this->_marginBottom + 30)) 
    						{ 

							if ($this->_sectionOn)
							{
								// Uh oh... we are trying to feed, but we are in the middle of a block.
								// Unwind the current transaction, reset the internal pointers to where we
								// where when the transaction started then let the feed go on...
								$this->transaction('rewind');
								$i = $this->_sectionPtr;
								$this->_currY = $this->sectionY;
							}

    							$this->NewReportPage(); 
    						}
    					}
    					break;
						
    			}
    			
    		}
    		$this->EndReport($sendOutput, $doFooter);
    }
    
    function ConditionalNewPage($fromBottom) {
    		// This function kicks off a new page if we are less than $fromBottom from the bottom
    		$this->_rowCount++; 
    		$r = $this->_rowCount;
    		$this->_events[$r] = array();
    		$this->_events[$r]['__method'] = 'cnewpage';
    		$this->_events[$r]['__border'] = $fromBottom;
    }
    
    function DarkRowColor($red, $green, $blue) {
    		$this->_dr_Red = $this->_ColorIntToFloat($red);
    		$this->_dr_Green = $this->_ColorIntToFloat($green);
    		$this->_dr_Blue = $this->_ColorIntToFloat($blue);
    }
    
    function EndReport($sendOutput, $footer=true) 
	{
		if ($footer)
		{
	   		$this->onFooter();
   			$this->afterFooter();
   		}
   		
		$this->LastResult = 'OK';
		
		if (!$sendOutput) { return true; }
		
    	switch ($this->_outputMode) 
		{
    		case 0 :
    			$this->ezStream();
    			break;
    				
    		case 1 :
    			$buff = $this->ezOutput();
	   			$f = fopen($this->_outputFile, 'w');
				fwrite($f, $buff);
				fclose($f);
//				print 'OK-File';
				break;
				
    		case 2 :
    			$this->PDFString = $this->ezOutput();
 //   			print 'OK-Self';
    	}
    }
    
    function EventPop($functionName, $p0='', $p1='', $p2='', $p3='', $p4='', $p5='', $p6='', $p7='', $p8='', $p9='')
    {
		$this->_rowCount++;
		$r = $this->_rowCount;
		$this->_events[$r] = array();
		$this->_events[$r]['__method'] = 'event';
		$this->_events[$r]['__func'] = $functionName;
		if ($p0) { $this->_events[$r]['__parms'][0] = $p0; }
		if ($p1) { $this->_events[$r]['__parms'][1] = $p1; }
		if ($p2) { $this->_events[$r]['__parms'][2] = $p2; }
		if ($p3) { $this->_events[$r]['__parms'][3] = $p3; }
		if ($p4) { $this->_events[$r]['__parms'][4] = $p4; }
		if ($p5) { $this->_events[$r]['__parms'][5] = $p5; }
		if ($p6) { $this->_events[$r]['__parms'][6] = $p6; }
		if ($p7) { $this->_events[$r]['__parms'][7] = $p7; }
		if ($p8) { $this->_events[$r]['__parms'][8] = $p8; }
		if ($p9) { $this->_events[$r]['__parms'][9] = $p9; }
    }
    
    function ExceptionRow($text, $size, $just='left', $fRed=0, $fGreen=0, $fBlue=0, $bRed=255, $bGreen=255, $bBlue=255) 
    {
    		$this->_rowCount++; 
    		$r = $this->_rowCount;
    		$this->_events[$r] = array();
    		$this->_events[$r]['__method'] = 'exception';
    		$this->_events[$r]['__text'] = $text;
    		$this->_events[$r]['__size'] = $size;
    		$this->_events[$r]['__just'] = $just;
                $this->_events[$r]['__mod_bold'] = false;
                $this->_events[$r]['__mod_italic'] = false;
                $this->_events[$r]['__mod_indent'] = 0;
    		$this->_events[$r]['__fc_Red'] = $this->_ColorIntToFloat($fRed);
    		$this->_events[$r]['__fc_Green'] = $this->_ColorIntToFloat($fGreen);
    		$this->_events[$r]['__fc_Blue'] = $this->_ColorIntToFloat($fBlue);
    		$this->_events[$r]['__rc_Red'] = $this->_ColorIntToFloat($bRed);
    		$this->_events[$r]['__rc_Green'] = $this->_ColorIntToFloat($bGreen);
    		$this->_events[$r]['__rc_Blue'] = $this->_ColorIntToFloat($bBlue);
    }
    
    function Execute() {
		die('ReporterBase.Execute: You must override this function');
    }
    
    function HeaderRow() {
    		// Kind of a super-row in that this actually places several events on the stack...
    		$this->SpacerRow(10);
    		$this->NextRowDark(false);
    		$this->NewRow(true);
    		$this->_events[$this->_rowCount]['__method'] = 'header';
    		for ($i=0; $i<$this->_columnCount; $i++) {
    			$this->ColumnData($this->_columns[$i]['name'], $this->_columns[$i]['caption']);
    		}
    		$this->LineRow();
    		$this->NextRowDark(true);
    }
    
    function ImageRow($fileName, $height, $width, $just='left')
    {
    		$this->_rowCount++; 
    		$r = $this->_rowCount;
    		$this->_events[$r] = array();
    		$this->_events[$r]['__method'] = 'image';
    		$this->_events[$r]['__file'] = $fileName;
    		$this->_events[$r]['__height'] = $height;
    		$this->_events[$r]['__width'] = $width;
    		$this->_events[$r]['__just'] = $just;
    }
    
    function IndentedExceptionRow($text, $indent, $size, $bold=true, $italic=false, $just='left', $fRed=0, $fGreen=0, $fBlue=0, $bRed=255, $bGreen=255, $bBlue=255) {
                $this->_rowCount++;
                $r = $this->_rowCount;
                $this->_events[$r] = array();
                $this->_events[$r]['__method'] = 'exception';
                $this->_events[$r]['__text'] = $text;
                $this->_events[$r]['__size'] = $size;
                $this->_events[$r]['__just'] = $just;
                $this->_events[$r]['__mod_bold'] = $bold;
                $this->_events[$r]['__mod_italic'] = $italic;
		$this->_events[$r]['__mod_indent'] = $indent;
                $this->_events[$r]['__fc_Red'] = $this->_ColorIntToFloat($fRed);
                $this->_events[$r]['__fc_Green'] = $this->_ColorIntToFloat($fGreen);
                $this->_events[$r]['__fc_Blue'] = $this->_ColorIntToFloat($fBlue);
                $this->_events[$r]['__rc_Red'] = $this->_ColorIntToFloat($bRed);
                $this->_events[$r]['__rc_Green'] = $this->_ColorIntToFloat($bGreen);
                $this->_events[$r]['__rc_Blue'] = $this->_ColorIntToFloat($bBlue);
    }
   
    function LightRowColor($red, $green, $blue) {
    		$this->_lr_Red = $this->_ColorIntToFloat($red);
    		$this->_lr_Green = $this->_ColorIntToFloat($green);
    		$this->_lr_Blue = $this->_ColorIntToFloat($blue);
    }
    
    function LineFont($fontName, $fontSize) {
    		$this->_lineFont = strtolower($fontName);
    		$this->_lineFontSize = $fontSize;
    		switch ($this->_lineFont) {
    			case 'helvetica' :
    				$this->selectFont($this->_HELVETICA);
    				break;
    			case 'courier' :
    				$this->selectFont($this->_COURIER);
    				break;
    			case 'times' :
    				$this->selectFont($this->_TIMES);
    				break;
    		}
    }
    
    function LineFontColor($red, $green, $blue) {
    		$this->_lc_Red = $this->_ColorIntToFloat($red);
    		$this->_lc_Green = $this->_ColorIntToFloat($green);
    		$this->_lc_Blue = $this->_ColorIntToFloat($blue);
    }
    
    function LineRow($thickness=0.3, $red=0, $green=0, $blue=0) {
    		$this->_rowCount++; 
    		$r = $this->_rowCount;
    		$this->_events[$r] = array();
    		$this->_events[$r]['__method'] = 'line';
    		$this->_events[$r]['__line_thickness'] = $thickness;
    		$this->_events[$r]['__line_red'] = $red;
    		$this->_events[$r]['__line_green'] = $green;
    		$this->_events[$r]['__line_blue'] = $blue;
    }
    
    function MultiColumnReport($numCols, $colSpace, $vertDiv) {
    		// This changes things a bit, but hopefully not too much to understand:
    		// I am just going to create array entries in $_multiColumns that define new 
    		// margins for each of the columns - so that when NewPage is called, I will reset the
    		// left and right margins for the current array entry, unless I have exceeded the 
    		// number of columns per page, in which case the pointer goes to zero and a 
    		// for-reals new page is requested...
    		if ($numCols < 1) { die('You must have a report with at least 1 column'); }
    		if ($numCols > 1) {
    			$grossArea = ($this->_marginRight - $this->_marginLeft) - (($numCols - 1) * $colSpace);
    			$netCol = $grossArea / $numCols;
    			$currLeft = $this->_oMarginLeft;
    			$halfSpace = $colSpace / 2;
    			
    			for ($i=0; $i<$numCols; $i++) {
    				$thisRight = $currLeft + $netCol;
    				$this->_multiColumns[$i]['lMargin'] = $currLeft;
    				$this->_multiColumns[$i]['rMargin'] = $thisRight;
    				if ($vertDiv) { $x = $currLeft + $netCol + $halfSpace; }
    				else { $x = -1; }
    				$this->_multiColumns[$i]['vertDiv'] = $x;
    				$currLeft += ($netCol + $colSpace);
    			}    			
    			$this->_multiColumnReport = true;
    		}
    		$this->_multiColumnPtr = -1;
    		$this->_multiColumnCount = $numCols;
    }
    
    function NextFontColor($red, $green, $blue) {
    	 	$this->_rowCount++; 
    		$r = $this->_rowCount;
    		$this->_events[$r] = array();
    		$this->_events[$r]['__method'] = 'colorfont';
    		$this->_events[$r]['__fc_red'] = $this->_ColorIntToFloat($red);
    		$this->_events[$r]['__fc_green'] = $this->_ColorIntToFloat($green);
    		$this->_events[$r]['__fc_blue'] = $this->_ColorIntToFloat($blue);
    	    }
    
    function NextRowColor($red, $green, $blue) {
    	 	$this->_rowCount++; 
    		$r = $this->_rowCount;
    		$this->_events[$r] = array();
    		$this->_events[$r]['__method'] = 'colorrow';
    		$this->_events[$r]['__rc_red'] = $this->_ColorIntToFloat($red);
    		$this->_events[$r]['__rc_green'] = $this->_ColorIntToFloat($green);
    		$this->_events[$r]['__rc_blue'] = $this->_ColorIntToFloat($blue);    	
    }
    
    function NextRowDark($isDark=true) {
    		$this->_rowCount++; 
    		$r = $this->_rowCount;
    		$this->_events[$r] = array();
    		$this->_events[$r]['__method'] = 'nextdark';
    		$this->_events[$r]['__darkRow'] = $isDark;
    }    		
    
    function NewReportPage($forceMultiPageFeed=false) 
    {
		if ($this->_multiColumnReport) {
			if ($this->_startingUp) {
				$this->onHeader();
				$this->afterHeader();
			}			
			$this->_multiColumnPtr++;
			if (($this->_multiColumnPtr >= $this->_multiColumnCount) || ($forceMultiPageFeed)){
	    			$this->onFooter();
   				$this->afterFooter();
    				$this->newPage();
	    			$this->_pageNum++;
	    			$this->onHeader();
    				$this->afterHeader();
				$this->_multiColumnPtr = 0;
			}
			$m = $this->_multiColumnPtr;
			$this->_marginLeft = $this->_multiColumns[$m]['lMargin'];
			$this->_marginRight = $this->_multiColumns[$m]['rMargin'];
			$this->_currY = $this->_belowHeaderY; // Reset the top pointer (not doing a header)
			
			$this->_startingUp = false;
		} else {
    			if (! $this->_startingUp) {
	    			$this->onFooter();
    				$this->afterFooter();
    				$this->newPage();
	    			$this->_pageNum++;
    			}
	    		$this->onHeader();
    			$this->afterHeader();
    			$this->_startingUp = false;
		}    		    		
    }
    
    function NewRow($bold=false, $italic=false) {
    		// Create a new blank instance of each column...
    		$this->_rowCount++; 
    		$r = $this->_rowCount;
    		$this->_events[$r] = array();
    		$this->_events[$r]['__method'] = 'normal';
    		$this->_events[$r]['__mod_bold'] = $bold;
    		$this->_events[$r]['__mod_italic'] = $italic;
    		for ($i=0; $i<$this->_columnCount; $i++) {
			$name = $this->_columns[$i]['name'];
//print $name . chr(10);
//			$this->_events[$r][$name] = $this->_columns[$i]['default'];
    		}
    }
    
    function onFooter() 
    {
   		// This is where footers are build - if you override this and want the normal footers,
   		// you must explicitly call this function from the child class.
		$this->setColor(0,0,0);
		$this->setStrokeColor(0,0,0);
		$this->selectFont($this->_HELVETICA);
		$today = date('l F jS, g:ia');
		$page = 'Page ' . $this->_pageNum; 
		$y = $this->_marginBottom;
		$this->addText($this->_oMarginLeft, $y, 9, $today);
		$x = $this->_oMarginRight - $this->getTextWidth(9, $page); 
		$this->addText($x, $y, 9, $page);
		$y += $this->getFontHeight(9);
		$this->setLineStyle($this->LineWeight);
		$this->line($this->_oMarginLeft, $y, $this->_oMarginRight, $y);
		$this->_footLineY = $y;				
    }
    
    function onHeader() 
    {
		$this->setColor(0,0,0);
		$this->setStrokeColor(0,0,0);
		$this->selectFont($this->_HELVETICA);
		$y = $this->_marginTop - ($this->getFontHeight(14) / 2);
		$this->addText($this->_oMarginLeft, $y, 14, $this->ReportTitle);
		if ($this->ReportSubTitle > ' ') {
			$y -= $this->getFontHeight(11);
			$this->addText($this->_oMarginLeft, $y, 11, $this->ReportSubTitle);
		}
		$this->setLineStyle($this->LineWeight);
		$y -= 4;
		$this->line($this->_oMarginLeft, $y, $this->_oMarginRight, $y);
		$this->_headLineY = $y;
		$y -= 5;
		$this->_currY = $y;
		
    }
    
    function OutputToFile($fileName) {
    		$this->_outputFile = $fileName;
    		$this->_outputMode = 1; // file type output
    }
    
    function OutputToSelf() {
    		$this->_outputMode = 2;
    }
    
	function PageFeed() {
    		$this->_rowCount++; 
    		$r = $this->_rowCount;
    		$this->_events[$r] = array();
    		$this->_events[$r]['__method'] = 'pagefeed';
//    		$this->_events[$r]['__border'] = $fromBottom;
    }
    
    function ResetPageNum() {
    		$this->_pageNum = 1;
    }
    
	function SectionStart()
	{
		// This adds a "don't page break during this block" marker so that
		// I can do the internal transaction stuff and keep the block cohesive
		$this->_rowCount++;
		$r = $this->_rowCount;
		$this->_events[$r] = array();
		$this->_events[$r]['__method'] = 'sectionStart';
	}
	
	function SectionComplete()
	{
		// This tells me that the block is complete and I can commit the 
		// current transaction and move on...
		$this->_rowCount++;
		$r = $this->_rowCount;
		$this->_events[$r] = array();
		$this->_events[$r]['__method'] = 'sectionComplete';
	}
		 			
    function SetMargins($top, $right, $bottom, $left) {
		$this->_marginBottom = $bottom;
		$this->_marginLeft = $left;
		$this->_marginTop = $this->magicTop - $top;
		$this->_marginRight = $this->magicRight - $right;
		$this->_oMarginLeft = $this->_marginLeft;
		$this->_oMarginRight = $this->_marginRight;
    }    		
    
    function SpacerRow($height) {
    		$this->_rowCount++; 
    		$r = $this->_rowCount;
    		$this->_events[$r] = array();
    		$this->_events[$r]['__method'] = 'spacer';
    		$this->_events[$r]['__spacer_height'] = $height;
    }

	function StartReport() {
		$this->LastResult = 'Report Started';
		$this->_startingUp = true;
		$this->ResetPageNum();
		$this->NewReportPage();
	}
	
    function StructureColumns() {
    		//$currLeft = $this->_marginLeft;
    		$currLeft = 0;
    		for ($i=0; $i<$this->_columnCount; $i++) {
    			$this->_columns[$i]['lPos'] = $currLeft;
    			$this->_columns[$i]['rPos'] = $currLeft + $this->_columns[$i]['width'];
    			$currLeft += $this->_columns[$i]['width'];	
    		}
	}
	
    function TextBlock($text, $size, $just='left', $fRed=0, $fGreen=0, $fBlue=0, $bRed=255, $bGreen=255, $bBlue=255) 
    {
		$this->_rowCount++; 
		$r = $this->_rowCount;
		$this->_events[$r] = array();
    	$this->_events[$r]['__method'] = 'block';
    	$this->_events[$r]['__text'] = $text;
    	$this->_events[$r]['__size'] = $size;
    	$this->_events[$r]['__just'] = $just;
		$this->_events[$r]['__mod_bold'] = false;
		$this->_events[$r]['__mod_italic'] = false;
		$this->_events[$r]['__mod_indent'] = 0;
		$this->_events[$r]['__fc_Red'] = $this->_ColorIntToFloat($fRed);
    	$this->_events[$r]['__fc_Green'] = $this->_ColorIntToFloat($fGreen);
    	$this->_events[$r]['__fc_Blue'] = $this->_ColorIntToFloat($fBlue);
    	$this->_events[$r]['__rc_Red'] = $this->_ColorIntToFloat($bRed);
    	$this->_events[$r]['__rc_Green'] = $this->_ColorIntToFloat($bGreen);
    	$this->_events[$r]['__rc_Blue'] = $this->_ColorIntToFloat($bBlue);
    }   	
    
    function ValidateRequestor() {
    		return true;
    }
    
    function ValidateRequest() {
    		return true;
    }
    
}
?>
