<?php

require "source/class.printjob.php";

class reportCodeBase extends printJob
{
	protected $db; 
	function BuildStructure() { $this->db = &$GLOBALS['utilDB']; }

	function specialClear()
	{
		$this->_events = array();
		$this->_columns = array();
 		$this->_columnCount = 0;
	}

}

class reportProcedures extends reportCodeBase
{
	protected $procList;
	
	function BuildStructure() { reportCodeBase::BuildStructure(); }
	
	function proceduresTOC()
	{
		$this->_lineFont = 'source/fonts/Helvetica.afm';
		$this->_lineFontSize = $this->fontSize;
		$this->DarkRowColor(0xff, 0xff, 0xff);
		$this->DefaultRightPad = 3;
		$this->addTextColumn('caption', 'caption', 'right');
		$this->addTextColumn('data', 'data');
		
		// Get all the code while building the TOC...
		$ptr = 0;
		$this->procList = array();
		$this->db->query("show procedure status where db='{$this->dbName}'");
		while ($this->db->fetchRow()) $this->procList[$ptr++]['name'] = $this->db->row[1];

		$ptr = 0;
		$max = count($this->procList);
		for($i=0; $i<$max; $i++)
		{
			$this->db->query("show create procedure {$this->dbName}.{$this->procList[$i]['name']}");
			$this->db->fetchRow();
			$buff = $this->db->row[2];
			$ptr = strpos($buff, 'PROCEDURE');
			$buff = substr($buff, $ptr, strlen($buff));
			$buff = str_replace('`', '', $buff);
			$this->procList[$i]['code'] = $buff;
			preg_match('/PROCEDURE [^(]+(.*)[\s]+BEGIN/i', $buff, $parts);
			
			$this->NewRow();
			$this->ColumnData('caption', $this->procList[$i]['name']);
			$this->ColumnData('data', $parts[1]);
		}
	}
	
	function proceduresDump()
	{
		foreach($this->procList as $script)
		{
			$this->specialClear();
			$this->installCodeTemplate();
			$this->ReportTitle = "Procedures: {$this->dbName}.{$script['name']}";
			$this->ReportSubTitle = '';
			$this->NewReportPage();
			$this->printCode($script['code']);
			$this->Commit(false, true, true);
		}
	}
	
	function execute()
	{
		$this->ReportTitle = "Procedures: {$this->dbName}";
		$this->ReportSubTitle = '';
		$this->ExceptionRow('', 1);
		$this->SpacerRow(10);		
		$this->proceduresTOC(true);
		$this->Commit(false, true, true);
		$this->specialClear();
		$this->proceduresDump();
   		$this->EndReport(true, false);
	}

}

class reportFunctions extends reportProcedures
{
	protected $funcList;

	function BuildStructure() { reportCodeBase::BuildStructure(); }
	
	function functionsTOC()
	{
		$this->_lineFont = 'source/fonts/Helvetica.afm';
		$this->_lineFontSize = $this->fontSize;
		$this->DefaultRightPad = 2;
		$this->DarkRowColor(0xe0, 0xe0, 0xe0);
		
		$this->addTextColumn('return');
		$this->addSpacerColumn('dummy', 5);
		$this->addTextColumn('fname', '', 'right');
		$this->addTextColumn('params');
		
		$this->NextRowDark(false);
		
		// Get all the code while building the TOC...
		$ptr = 0;
		$this->funcList = array();
		$this->db->query("show function status where db='{$this->dbName}'");
		while ($this->db->fetchRow()) $this->funcList[$ptr++]['name'] = $this->db->row[1];

		$ptr = 0;
		$max = count($this->funcList);
		for($i=0; $i<$max; $i++)
		{
			$this->db->query("show create function {$this->dbName}.{$this->funcList[$i]['name']}");
			$this->db->fetchRow();
			$buff = $this->db->row[2];
			$ptr = strpos($buff, 'FUNCTION');
			$buff = substr($buff, $ptr, strlen($buff));
			$buff = str_replace('`', '', $buff);
			$this->funcList[$i]['code'] = $buff;

			preg_match('/FUNCTION [^(]+(.*)[\s]+BEGIN/ims', $buff, $parts);
			$params = str_replace(array(chr(13), chr(10)), '', $parts[1]);
			// Take the params apart a bit so that I can show them a bit differently:
			
			$params = preg_replace('/charset[\s]+[A-Z0-9]+[\s]+/ims', '', $params);
			$params = preg_replace('/deterministic[\s]*/ims', '', $params);
			
			// Gather the return value so I can place it to the left:
			preg_match('/returns[\s]+([A-Z0-9\(\)]+)/ims', $params, $returnParts);
			$params = preg_replace('/returns[\s]+[A-Z0-9()]+\s*/ims', '', $params);
			
			$this->NewRow();
			$this->ColumnData('return', "[ {$returnParts[1]} ]");
			$this->ColumnData('fname', $this->funcList[$i]['name']);
			$this->ColumnData('params', $params);
		}
	}
	
	function functionsDump()
	{
		foreach($this->funcList as $script)
		{
			$this->specialClear();
			$this->installCodeTemplate();
			$this->ReportTitle = "Functions: {$this->dbName}.{$script['name']}";
			$this->ReportSubTitle = '';
			$this->NewReportPage();
			$this->printCode($script['code']);
			$this->Commit(false, true, false);
		}		
	}
	
	function execute()
	{
		$this->ReportTitle = "Functions: {$this->dbName}";
		$this->ReportSubTitle = '';
		$this->ExceptionRow('', 1);
		$this->SpacerRow(10);
		$this->functionsTOC(true);
		$this->Commit(false, true, true);
		$this->specialClear();
		$this->functionsDump();
   		$this->EndReport(true, false);
	}

}

class reportTriggers extends reportFunctions
{
	protected $scriptsToPrint;
	
	function BuildStructure() { reportCodeBase::BuildStructure(); }
	
	function triggersTOC()
	{
		$this->_lineFont = 'source/fonts/Helvetica.afm';
		$this->_lineFontSize = $this->fontSize;
		$this->DarkRowColor(0xff, 0xff, 0xff);
		$this->DefaultRightPad = 10;
		$this->DefaultLeftPad = 10;
		$this->addTextColumn('tablename', '', 'right');
		$this->addTextColumn('bi', '', 'center');
		$this->addTextColumn('ai', '', 'center');
		$this->addTextColumn('bu', '', 'center');
		$this->addTextColumn('au', '', 'center');
		$this->addTextColumn('bd', '', 'center');
		$this->addTextColumn('ad', '', 'center');
		$this->addTextColumn('dummy');
		
		$this->VerticalLines = true;
		$this->ExceptionRow('', 1);
		$this->SpacerRow(10);
		$this->NextRowDark(false);
		
		$this->db->query("show tables from {$this->dbName}");
		while ($this->db->fetchRow())
		{
			$name = $this->db->row[0];
			$todo[$name]['name'] = $this->db->row[0];
			$todo[$name]['beforeinsert'] = '';
			$todo[$name]['afterinsert'] ='';
			$todo[$name]['beforeupdate'] = '';
			$todo[$name]['afterupdate'] = '';
			$todo[$name]['beforedelete'] = '';
			$todo[$name]['afterdelete'] = '';
		}
			
		
		$sPtr = 0;
		$this->scriptsToPrint = array();
		
		// Get the online triggers:
		$this->db->query("select event_object_table, action_timing, event_manipulation, action_statement from information_schema.triggers where trigger_schema='{$this->dbName}'");
		while ($this->db->fetchRow())
		{
			$table = $this->db->row[0];
			$timing = strtolower($this->db->row[1] . $this->db->row[2]);
			$todo[$table][$timing] = '<b>ON</b>';
			
			$this->scriptsToPrint[$sPtr]['name'] = "{$this->dbName}.$table.{$this->db->row[1]}.{$this->db->row[2]}";
			$this->scriptsToPrint[$sPtr]['script'] = $this->db->row[3];
			$sPtr++;
		}
		
		// Get the offline triggers:
		$this->db->query("select tablename, timing, script from {$GLOBALS['pmiDBPrefix']}triggers where dbname='{$this->dbName}'");
		while ($this->db->fetchRow())
		{
			$timing = str_replace('.', '', $this->db->row[1]);
			// Don't change an online table...
			if ($todo[$this->db->row[0]][$timing] <= ' ')
			{
				$todo[$this->db->row[0]][$timing] = 'off';
				$this->scriptsToPrint[$sPtr]['name'] = "{$this->dbName}.{$this->db->row[0]}.{$this->db->row[1]}";
				$this->scriptsToPrint[$sPtr]['script'] = $this->db->row[2];
				$sPtr++;
			}
		}

		$this->NewRow();
		$this->ColumnData('bi', '<b>Before</b>');
		$this->ColumnData('ai', '<b>After</b>');
		$this->ColumnData('bu', '<b>Before</b>');
		$this->ColumnData('au', '<b>After</b>');
		$this->ColumnData('bd', '<b>Before</b>');
		$this->ColumnData('ad', '<b>After</b>');
		$this->NewRow();
		$this->ColumnData('bi', '<b>Insert</b>');
		$this->ColumnData('ai', '<b>Insert</b>');
		$this->ColumnData('bu', '<b>Update</b>');
		$this->ColumnData('au', '<b>Update</b>');
		$this->ColumnData('bd', '<b>Delete</b>');
		$this->ColumnData('ad', '<b>Delete</b>');
		$this->LineRow();
		$this->SpacerRow(5);

		foreach($todo as $table)
		{
			$this->NewRow();
			$this->ColumnData('tablename', $table['name']);
			$this->ColumnData('bi', $table['beforeinsert']);
			$this->ColumnData('ai', $table['afterinsert']);
			$this->ColumnData('bu', $table['beforeupdate']);
			$this->ColumnData('au', $table['afterupdate']);
			$this->ColumnData('bd', $table['beforedelete']);
			$this->ColumnData('ad', $table['afterdelete']);
			$this->LineRow();
		}
		
	}
	
	function triggersDump()
	{
		$this->VerticalLines = false;
		foreach($this->scriptsToPrint as $script)
		{
			$this->specialClear();
			$this->ReportTitle = "Trigger: {$script['name']}";
			$this->ReportSubTitle = '';
			$this->NewReportPage();
			$this->installCodeTemplate();
			$this->printCode($script['script']);
			$this->Commit(false, true, false);
		}
		
	}
	
	function execute()
	{
		$this->ReportTitle = "Triggers: {$this->dbName}";
		$this->ReportSubTitle = '';
		$this->triggersTOC();
		$this->Commit(false, true, true);
		$this->triggersDump();
   		$this->EndReport(true, false);
	}

}

class reportViews extends reportTriggers
{
	protected $viewList;

	function BuildStructure() { reportCodeBase::BuildStructure(); }
	
	function viewsTOC()
	{
		$this->viewList = array();
		$this->_lineFont = 'source/fonts/Helvetica.afm';
		$this->_lineFontSize = 11;
		$this->DarkRowColor(0xff, 0xff, 0xff);
		$this->addTextColumn('viewname');
		$this->db->query("select table_name from information_schema.views where table_schema='{$this->dbName}' order by table_name");
		while ($this->db->fetchRow())
		{
			$this->viewList[] = $this->db->row[0];
			$this->NewRow();
			$this->ColumnData('viewname', $this->db->row[0]);
		}
	}
	
	function viewsDump()
	{
		$this->specialClear();

		$this->ReportTitle = "Views: {$this->dbName}";
		$this->NewReportPage();

		$this->_lineFont = 'source/fonts/Courier.afm';
		$this->_lineFontSize = 11;
		$this->DarkRowColor(0xff, 0xff, 0xff);
		$this->AddSpacerColumn('dummy', 15);
		$this->AddTextColumn('line');

		$search[] = 'ALGORITHM ';
		$replace[] = "\nALGORITHM ";
		
		$search[] = 'DEFINER=';
		$replace[] = "\nDEFINER=";
		
		$search[] = 'SQL ';
		$replace[]  = "\nSQL ";
		
		$search[] = 'VIEW ';
		$replace[] = "\nVIEW ";
		
		$search[] = 'FROM ';
		$replace[] = "\nFROM ";
		
		$search[] = '`';
		$replace[] = '';
		
		$search[] = ',';
		$replace[] = ",\n";

		foreach($this->viewList as $view)
		{
			$this->db->query("show create view {$this->dbName}.$view");
			$this->db->fetchRow();
			$buff = str_replace("\n", '', $this->db->row[1]);
			
			$buff = str_ireplace($search, $replace, $buff);
			$buff = preg_replace('/VIEW[\s]+([A-Z0-9\.]+)[\s]+AS\s/i', "VIEW $1\nAS ", $buff);
			
			$this->ExceptionRow("<b>$view</b>", 12);
			$lines = explode(chr(10), $buff);

			foreach($lines as $line)
			{
				$this->NewRow();
				$this->ColumnData('line', $line);
			}
			$this->SpacerRow(15);			
		}
		$this->Commit(false, true, false);
		
	}
	
	function execute()
	{
		$this->ReportTitle = "Views: {$this->dbName}";
		$this->ReportSubTitle = '';
		$this->viewsTOC();
		$this->Commit(false, true, true);
		$this->specialClear();
		$this->viewsDump();
   		$this->EndReport(true, false);
	}

}

class reportDBCode extends reportViews
{
	function BuildStructure() { reportCodeBase::BuildStructure(); }
	
	function execute()
	{
		$this->ReportTitle = "Procedures: {$this->dbName}";
		$this->ReportSubTitle = '';
		$this->ExceptionRow('', 1);
		$this->SpacerRow(10);
		$this->Commit(false, false, true);
		
		$this->specialClear();
		$this->ReportTitle = "Procedures: {$this->dbName}";
		$this->ReportSubTitle = '';
		$this->specialClear();
		$this->proceduresTOC();
		$this->Commit(false, false, false);
		
		$this->specialClear();
		$this->ReportTitle = "Functions: {$this->dbName}";
		$this->ReportSubTitle = '';
		$this->NewReportPage();		
		$this->ExceptionRow('', 1);
		$this->SpacerRow(10);
		$this->functionsTOC(true);
		$this->Commit(false, true, false);

		$this->specialClear();
		$this->ReportTitle = "Triggers: {$this->dbName}";
		$this->ReportSubTitle = '';
		$this->NewReportPage();		
		$this->ExceptionRow('', 1);
		$this->SpacerRow(10);
		$this->triggersTOC(true);
		$this->Commit(false, true, false);

		$this->VerticalLines = false;

		$this->specialClear();
		$this->ReportTitle = "Views: {$this->dbName}";
		$this->ReportSubTitle = '';
		$this->NewReportPage();
		$this->viewsTOC();
		$this->Commit(false, true, false);


		$this->specialClear();
		$this->proceduresDump();
		$this->specialClear();
		$this->functionsDump();
		$this->specialClear();
		$this->triggersDump();
		$this->specialClear();
		$this->viewsDump();
		
   		$this->EndReport(true, false);
	}

}

?>