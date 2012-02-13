<?php

require "source/class.printjob.php";

class dbeTableReport extends printJob
{
	protected $db;

	public $tableName;
	public $dbName;
	
	function BuildStructure()
	{
		$this->db = &$GLOBALS['utilDB'];
		$this->DefaultRightPad = 20;
	}

	function installTOC($title='Blank Title', $subTitle='')
	{
		$this->_lineFont = 'source/fonts/Courier.afm';
		$this->_lineFontSize = $this->fontSize;
		$this->DarkRowColor(0xff, 0xff, 0xff);
                
		$this->addTextColumn('caption', 'caption', 'right');
		$this->addTextColumn('data', 'data');
		$this->ReportTitle = $title;
		$this->ReportSubTitle = $subTitle;
	}
	
	function dumpFieldData($startingUp=true)
	{
		$this->db->query("describe {$this->dbName}.{$this->tableName}");
		while ($this->db->fetchArray())
		{
			$this->NewRow();
			$this->ColumnData('fieldname', $this->db->row['Field']);
			$this->ColumnData('kind', $this->db->row['Type']);
			$this->ColumnData('nulls', ucfirst(strtolower($this->db->row['Null'])));
			$this->ColumnData('default', $this->db->row['Default']);
			$this->ColumnData('extra', $this->db->row['Extra']);
		}
		$this->Commit(false, false, $startingUp);
	}
	
	function dumpIndexData($startingUp=false)
	{
		$ptr = 0;
		$currIndex = '';
		$darkRow = true;
		$this->db->query("show index from {$this->dbName}.{$this->tableName}");
		while ($this->db->fetchArray())
		{
			
			if ($this->db->row['Key_name'] <> $currIndex)
			{
				$darkRow = !$darkRow;
				
				if ($ptr++ > 0) 
				{
					$this->NextRowDark($darkRow);	
					$this->SpacerRow(8);
					$this->NextRowDark($darkRow);	
					$this->NewRow();
				} else {
					$this->NextRowDark($darkRow);	
					$this->NewRow();
				}

				
				if ($this->db->row['Key_name'] == 'PRIMARY') $theType = 'PKey';
				else {
					switch($db->row['Index_type'])
					{
						case 'FULLTEXT':
							$theType = 'FullText';
							break;
							
						default:
							$theType = 'Index';
							break;
							
					}
				}
		
				$thisUnique = ($this->db->row['Non_unique'] == '0') ? 'Yes' : 'No';
				
				$this->ColumnData('indexname', $this->db->row['Key_name']);
				$this->ColumnData('type', $theType);
				$this->ColumnData('unique', $thisUnique);
				$this->ColumnData('cardinality', $this->db->row['Cardinality']);
				$this->ColumnData('fields', $this->db->row['Column_name']);

				$currIndex = $this->db->row['Key_name'];
				
			} else {
				$this->NextRowDark($darkRow);	
				$this->NewRow();
				$this->ColumnData('fields', $this->db->row['Column_name']);
			}
		}

		$this->Commit(false, true, $startingUp);
	}

	function dumpTableData($startingUp=true)
	{
		$this->installTableShape();
		$this->dumpFieldData($startingUp);
		$this->installIndexShape();
		$this->dumpIndexData(false);
	}
	
	function execute()
	{
		$theTable = "{$this->dbName}.{$this->tableName}";
		$rows = $this->db->singleAnswer("select count('x') from $theTable");
		$this->ReportTitle = "$theTable - ($rows Rows)";
		$this->ReportSubTitle = '';

		$this->lineNumbers = false;
		$this->headerTitle = true;
		$this->footerDate = true;
		$this->pageNumbers = true;
		$this->fontSize = 9;
		
		$this->dumpTableData(true);
   		$this->EndReport(true, false);		
   		
	}
	
	function installIndexShape()
	{
		$this->specialClear();

		$this->_lineFont = 'source/fonts/Courier.afm';
		$this->_lineFontSize = 9;
		$this->SpacerHeight = 0;

		$this->AddTextColumnFixed('indexname', 150);
		$this->AddTextColumn('type');
		$this->AddTextColumn('unique', '', 'center');
		$this->AddTextColumn('cardinality');
		$this->AddTextColumn('fields');

		$this->NextRowDark(false);
		$this->ExceptionRow('', 1);
   		$this->SpacerRow(10);
		$this->NewRow();
		$this->ColumnData('indexname', '<b>Index Name</b>');
		$this->ColumnData('type', '<b>Type</b>');
		$this->ColumnData('unique', '<b>Unique</b>');
		$this->ColumnData('cardinality', '<b>Cardinality</b>');
		$this->ColumnData('fields', '<b>Fields</b>');
		$this->LineRow();
	}
	
	function installTableShape()
	{
		$this->specialClear();

		$this->_lineFont = 'source/fonts/Courier.afm';
		$this->_lineFontSize = 9;

		$this->AddTextColumnFixed('fieldname', 150);
		$this->AddTextColumn('kind');
		$this->AddTextColumn('nulls', '', 'center');
		$this->AddTextColumn('default');
		$this->AddTextColumn('extra');

		$this->NextRowDark(false);
		$this->ExceptionRow('', 1);
   		$this->SpacerRow(10);
		$this->NewRow();
		$this->ColumnData('fieldname', '<b>Field Name</b>');
		$this->ColumnData('kind', '<b>Kind</b>');
		$this->ColumnData('nulls', '<b>Nulls</b>');
		$this->ColumnData('default', '<b>Default</b>');
		$this->ColumnData('extra', '<b>Extra</b>');
		$this->LineRow();
		$this->NextRowDark(false);
	}
	
	function specialClear()
	{
		$this->_events = array();
		$this->_columns = array();
 		$this->_columnCount = 0;
	}
	
}

class dbeDatabaseReport extends dbeTableReport
{
	function BuildStructure()
	{
		parent::BuildStructure();
	}

	function execute()
	{
		$this->ReportTitle = "Schema Report: {$this->dbName}";
		$this->ReportSubTitle = '';
		$this->installTOC();
		$this->ExceptionRow('', 1);
		$this->SpacerRow(10);
		$this->NewRow();
		$this->ColumnData('tablename', '<b>Table Name</b>');
		$this->ColumnData('engine', '<b>Engine</b>');
		$this->ColumnData('rows', '<b>Rows</b>');
		$this->ColumnData('autoinc', '<b>AutoInc</b>');
		$this->LineRow();
		$this->SpacerRow(5);
		
		$this->db->query("show table status from {$this->dbName}");
		while ($this->db->fetchArray())
		{
			$todo[] = $this->db->row['Name'];
			
			$this->NewRow();
			$this->ColumnData('tablename', $this->db->row['Name']);
			$this->ColumnData('engine', $this->db->row['Engine']);
			$this->ColumnData('rows', $this->db->row['Rows']);
			$this->ColumnData('autoinc', $this->db->row['Auto_increment']);
		}
		$this->Commit(false, true, true);
			
		$startingUp = false;
		foreach($todo as $table)
		{
			$this->tableName = $table;
			$theTable = "{$this->dbName}.$table";
			$rows = $this->db->singleAnswer("select count('x') from $theTable");
			$this->lineNumbers = false;
			$this->headerTitle = true;
			$this->footerDate = true;
			$this->pageNumbers = true;
			$this->fontSize = 9;
		
			$this->ReportTitle = "Schema Report: $theTable";
			$this->ReportSubTitle = '';
			if (!$startingUp) $this->NewReportPage();
			
			$this->dumpTableData($startingUp);
			$startingUp = false;
		}
	   	
	   	$this->EndReport(true, false);
	}
	
	function installTOC()
	{
		$this->specialClear();
		$this->AddTextColumnFixed('tablename', 200);
		$this->AddTextColumn('engine');
		$this->AddTextColumn('rows', '', 'right');
		$this->AddTextColumn('autoinc', '', 'right');
		$this->_lineFont = 'source/fonts/Courier.afm';
		$this->_lineFontSize = 9;
		$this->DarkRowColor(0xff, 0xff, 0xff);
		
		
	}
	
}

?>