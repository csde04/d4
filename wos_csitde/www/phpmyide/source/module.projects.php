<?php

// Note that the $ptr variable was set in the calling file
// and I simply increment it here because I'm adding something.

$ptr++;
$tabs[$bar][$ptr]['index'] = $ptr;
$tabs[$bar][$ptr]['caption'] = 'Projects';
$tabs[$bar][$ptr]['div'] = <<<HTML
Projects Div<br><br>
Mouse X: <span id="showMouseX">0</span><br>
Mouse y: <span id="showMouseY">0</span><br>
Drag Delta: <span id="showDelta">0</span>
HTML;

?>