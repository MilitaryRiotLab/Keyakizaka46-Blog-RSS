<?php
if (php_sapi_name() != "cli") {
	echo 'Only for cronjob';
	exit;
}

require('inc/simple_html_dom.php'); // Using PHP Simple HTML DOM Parser from https://sourceforge.net/projects/simplehtmldom/ under MIT License 
require('config.inc.php');
date_default_timezone_set('UTC');

$dom = file_get_html($HTM_INPUT);

$array = array();

$i = 0;
$out_time = '';
$loop = '';
foreach($dom->find('div.box-bottom li') as $node) // Time / Data
{
		$out_time .= trim( strip_tags($dom->find('div.box-bottom li',$i) ), " ");
		$i ++;
}

$time = str_replace('  ', '', explode( '個別ページ', $out_time ) );


for( $i = 0; $i <= 19; $i++ )
{
	$array[$i] = array();
	
	$link = 'http://www.keyakizaka46.com'.$dom->find('div.box-ttl h3 a',$i)->href;
	
	$array[$i]['title'] = htmlspecialchars( trim( strip_tags($dom->find('h3', $i) ,''), ' ' ),  ENT_XML1, 'UTF-8');
	$array[$i]['author'] = htmlspecialchars( trim( strip_tags($dom->find('article p.name', $i) ,'<a>'), ' ' ),  ENT_XML1, 'UTF-8');
	$array[$i]['link'] = urlencode( $link );
	
	$array[$i]['time'] = date( 'r', strtotime( $time[$i] ) );
	$content = strip_tags($dom->find('div.box-article',$i) ,'<img><br><br /><div><b>');
	$content = str_replace('<div>','',$content);
	$content = str_replace('</div>','<br>',$content);
	$content = str_replace('<div class="box-article">','<br>',$content);
	$content = str_replace('<div id="AppleMailSignature">','<br>',$content);
	
	$content = str_replace('<img src="','<img src="http://www.keyakizaka46.com',$content);
	$array[$i]['content'] = htmlspecialchars( $content,  ENT_XML1, 'UTF-8');
}

$date_now = date(DATE_RSS);

$pre_loop="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<rss xmlns:atom=\"http://www.w3.org/2005/Atom\" version=\"2.0\">
  <channel>
    <atom:link href=\"{$RSS_URI}\" rel=\"self\" type=\"application/rss+xml\"/>
    <title>欅坂46 公式ブログ</title>
    <link>http%3A%2F%2Fwww.keyakizaka46.com%2Fmob%2Fnews%2FdiarKiji.php%3Fsite%3Dk46o%26ima%3D0000%26cd%3Dmember</link>
    <description>Raw feed from 欅坂46 公式ブログ,Github: https://github.com/MilitaryRiotLab/Keyakizaka46-Blog-RSS</description>
    <lastBuildDate>$date_now</lastBuildDate>
    <ttl>15</ttl>
";

for( $i = 0; $i <= 19; $i++ )
{
	$loop .= "
	<item>
	<title>{$array[$i]['title']} | {$array[$i]['author']}</title>
	<link>{$array[$i]['link']}</link>
	<description>{$array[$i]['content']}</description>
	<pubDate>{$array[$i]['time']}</pubDate>
	<guid>{$array[$i]['link']}</guid>
	</item>
	";
}

$post_loop=<<<EOF
	</channel>
</rss>
EOF;

$output = $pre_loop.$loop.$post_loop;


$xmlfile = fopen($XML_OUTPUT, "w") or die("Unable to open file!");
fwrite($xmlfile, $output);
fclose($xmlfile);
$output_gzip = gzencode($output,9);
$xmlfile_gzip = fopen($XML_OUTPUT.'.gz', "w") or die("Unable to open gzip file!");
fwrite($xmlfile_gzip, $output_gzip);
fclose($xmlfile_gzip);
echo 'Jobs done';


/*
foreach($dom->find('h3') as $node) // Title
{
	$out .= strip_tags($dom->find('h3', $i) ,'<a>').'<br>';
	$i ++;
	
}
*/

/*
foreach($dom->find('article p.name') as $node) // Author
{
	$out .= strip_tags($dom->find('article p.name', $i) ,'<a>').'<br>';
	$i ++;
	
}
*/

/*
foreach($dom->find('div.box-article') as $node) // Content
{
    $out .= strip_tags($dom->find('div.box-article',$i) ,'<img><br><br /><div><b>')."==========";
	$out = str_replace('</div>','<br>',$out);
	//$out = str_replace('</b>','<br>',$out);
	$out = str_replace('<div>','',$out);
	$out = str_replace('<div id="AppleMailSignature">','',$out);
	//$out = str_replace('<b>','',$out);
	$i ++;
}
*/
/*
$i = 0;
foreach($dom->find('div.box-bottom li') as $node) // Time / Data
{
		$out_time .= trim( strip_tags($dom->find('div.box-bottom li',$i) ), " ");
		$i ++;
}

$time = str_replace('  ', '', explode( '個別ページ', $out_time ) );

echo '<pre>';
var_dump($array);
echo '</pre>';

echo $i.'<br>=============<br>';
echo $out;
*/