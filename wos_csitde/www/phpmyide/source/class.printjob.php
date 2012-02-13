<?php

require "source/class.reporterbase.php";

class printJob extends reporterbase
{
	public $dbName;
	public $scriptName; 
	public $lineNumbers;
	public $headerTitle;
	public $footerDate;
	public $pageNumbers;
	public $fontName;
	public $fontSize;
	
	function printJob()
	{
		parent::reporterbase();

		$this->lineNumbers = true;
		$this->headerTitle = true;
		$this->footerDate = true;
		$this->pageNumbers = true;
		$this->fontSize = 9;
	}
	
	function BuildStructure() { }
	
	function execute($buffer)
	{
		$this->installCodeTemplate();

		if ($this->headerTitle)
			$this->ReportTitle = "{$this->dbName}.{$this->scriptName}";
		$this->ReportSubTitle = '';

		$this->printCode($buffer);
		$this->Commit();
	}
	
	function installCodeTemplate()
	{
		// This is a kludge specifically for phpMyIDE and should NOT be added to the class normally...
		$this->_lineFont = 'source/fonts/Courier.afm';
		$this->_lineFontSize = $this->fontSize;
		$this->DarkRowColor(0xff, 0xff, 0xff);
                
		if ($this->lineNumbers)
			$this->AddTextColumn('linenum', 'LineNum', 'right');
		
		$this->AddTextColumn('line', 'Line', 'left');
	
	}
	
	function printCode($buffer)
	{
		$search[] = "\t";
		$search[] = "\'";
		$replace[] = '    ';
		$replace[] = "'";
	
		$buffer = str_replace($search, $replace, $buffer);
		$lines = explode(chr(10), $buffer);
		$max = count($lines);
		for($i=0; $i<$max; $i++)
		{
			$this->NewRow();
			if ($this->lineNumbers) $this->ColumnData('linenum', "$i:");
			$this->ColumnData('line', $lines[$i]);
		}	
	}

    function onFooter() 
    {
		if ($this->footerDate or $this->pageNumbers)
		{
			$this->selectFont($this->_HELVETICA);
			$today = date('l F jS, g:ia');
			$page = 'Page ' . $this->_pageNum; 
			$y = $this->_marginBottom;
			if ($this->footerDate) 
				$this->addText($this->_oMarginLeft, $y, 9, $today);
				
			$x = $this->_oMarginRight - $this->getTextWidth(9, $page); 
			if ($this->pageNumbers) 
				$this->addText($x, $y, 9, $page);
				
			$y += $this->getFontHeight(9);
			$this->setLineStyle($this->LineWeight);
			$this->line($this->_oMarginLeft, $y, $this->_oMarginRight, $y);
			$this->_footLineY = $y;
		}
    }

    function onHeader() 
    {
		if ($this->headerTitle)
		{
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
		} else {
			$y = $this->_marginTop - ($this->getFontHeight(14) / 2);
			$this->_currY = $y;
		}
    }
}

?>