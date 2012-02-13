<?php

echo "<pre>";
error_reporting(E_ALL);

require 'config.php';

$my = new mysqli($connections[0]['host'], $connections[0]['user'], $connections[0]['password']);

$sql = <<<SQL
DROP TABLE IF EXISTS {$pmiDBPrefix}long_commands;
CREATE TABLE IF NOT EXISTS {$pmiDBPrefix}long_commands (
  datestamp date NOT NULL,
  id int(11) NOT NULL auto_increment,
  command text NOT NULL,
  PRIMARY KEY  (datestamp,id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
select "Built long_commands";

DROP TABLE IF EXISTS {$pmiDBPrefix}long_responses;
CREATE TABLE IF NOT EXISTS {$pmiDBPrefix}long_responses (
  command_datestamp date NOT NULL,
  command_id int(11) NOT NULL,
  id int(11) NOT NULL auto_increment,
  response text NOT NULL,
  PRIMARY KEY  (command_datestamp,command_id,id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
select "Built long_responses";

DROP TABLE IF EXISTS {$pmiDBPrefix}preferences;
CREATE TABLE IF NOT EXISTS {$pmiDBPrefix}preferences (
  name varchar(32) NOT NULL,
  value varchar(254) NOT NULL,
  PRIMARY KEY  (name)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
select "Built preferences";

INSERT INTO {$pmiDBPrefix}preferences (name, value) VALUES ('print_headertitle', '1'),
('print_pagenumbers', '1'),
('print_headerdate', '1'),
('print_linenumbers', '1'),
('print_fontsize', '11'),
('print_fontname', 'courier');
select "Installed preferences";

DROP TABLE IF EXISTS {$pmiDBPrefix}timemachine;
CREATE TABLE IF NOT EXISTS {$pmiDBPrefix}timemachine (
  descriptor varchar(254) NOT NULL,
  uid int(11) NOT NULL auto_increment,
  timestamp datetime NOT NULL,
  createsql text NOT NULL,
  PRIMARY KEY  (descriptor,uid)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
select "Built timemachine";

DROP TABLE IF EXISTS {$pmiDBPrefix}triggers;
CREATE TABLE IF NOT EXISTS {$pmiDBPrefix}triggers (
  dbname varchar(128) NOT NULL,
  tablename varchar(64) NOT NULL,
  timing varchar(32) NOT NULL,
  oneshot char(1) NOT NULL default '0',
  script text NOT NULL,
  PRIMARY KEY  (dbname,tablename,timing)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
select "Built triggers";
select "All Done";
SQL;

if($my->multi_query($sql))
{
	do 
	{
		if ($res = $my->store_result())
		{
			$row = $res->fetch_row();
			echo "{$row[0]}\n";
			$res->close();
		} else {
			if ($my->error) echo "Error (1): {$my->error}\n";
		}
	} while($my->next_result());
} else {
	if ($my->error) echo "Error (0): {$my->error}\n";
}
echo "</pre>";

?>
