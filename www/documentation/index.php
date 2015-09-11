<?php


$prefix = isset($_SERVER['HTTP_X_ORIGINAL_SERVER_NAME'])?'HTTP_X_ORIGINAL_':'';
$url = 'http://'.$_SERVER[$prefix.'SERVER_NAME']."/api/languages";

$languageLinks = "<a>No languages available (yet)</a>";
if(!filter_var($url, FILTER_VALIDATE_URL) === false) {
	$fileContents = file_get_contents($url);

	if($fileContents !== false) {
		$parsed = json_decode($fileContents);

		if($parsed !== false) {
			$languageLinks = "";
			foreach($parsed as $lang){
				$firstExtension = $lang->extensions[0];
				$languageLinks .= "<a target=\"_top\" href=\"$firstExtension\">$lang->name</a>";
			}
		}
	}	
}

/*
<a href="team-manual.pdf">Team manual</a>
<a href="cpp/c.html">C</a>
<a href="cpp/cpp.html">C++</a>
<a href="java">Java</a>
<a href="haskell">Haskell</a>
<a href="http://mono/">C#</a>
*/


?><!DOCTYPE html>
<html lang="en" xml:lang="en">
<head>
<meta charset="utf-8"/>
<title>Documentation</title>
<link rel="icon" href="../images/favicon.png" type="image/png" />
<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<nav><div id="menutop">
<?php echo $languageLinks; ?>
<a target="_top" href="/team">→Back to team interface</a>
<?php 
//if ( checkrole('jury') || checkrole('balloon') ) {
//	echo "<a target=\"_top\" href=\"../jury/\" accesskey=\"j\">→jury</a>\n";
//}
?>
</div>

<br /><br /><br /><br />
Use the links at the top to go to the team manual or the documentation of a language.

<br /><br /><br /><br />
The following commands are available:
<br><br>
<table border="1px">
<tbody><tr><td>Language</td><td>Command</td><td>Executes</td></tr>
<tr><td>C</td><td>mygcc</td><td>gcc -Wall -O2 -static -pipe -lm $1 -o $2</td></tr>
<tr><td>C&#43;&#43;</td><td>myg&#43;&#43;</td><td>g&#43;&#43; -Wall -O2 -static -pipe -std=c++11 $1 -o $2</td></tr>
<tr><td>C#</td><td>mydmcs</td><td>dmcs -o+ -out:"$2" "$1"</td></tr>
<tr><td>Java</td><td>myjavac</td><td>javac -d . $1</td></tr>
<tr><td>Haskell</td><td>myghc</td><td>ghc -Wall -Wwarn -O -static -optl-static -optl-pthread $1 -o $2</td></tr>
</tbody></table>
<br /><br /><br /><br />

</body>
</html>


