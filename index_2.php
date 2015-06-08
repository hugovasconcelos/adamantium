<?php
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$htmlCode = $_POST['html_mkt'];

//tratando o html para as formatação padrão do email marketing

//1° Passo -> remover os comentários existentes no código
$patternComments = '/<!--.*?-->/';
$htmlCode = preg_replace($patternComments, '', $htmlCode);

//2° Passo -> Capturar o bgcolor contido na tag body
$patternCaptureBgColor = '/body\sbgcolor="#(?P<color>\d|\w{3,6})"/';
preg_match($patternCaptureBgColor, $htmlCode, $matches);

if(isset($matches['color'])){
	$bgColor = $matches['color'];
	//3° Passo -> Remover bgcolor da tag body
	$htmlCode = str_replace($matches[0], 'body ', $htmlCode);
}

//4° Passo -> adicionar bgcolor na tag table
$patternFirstTable = '/<table(.*?)>/';
preg_match($patternFirstTable, $htmlCode, $matchesTable);


if($matchesTable){
	//5° Passo -> remove o id da tabela
	$matchesTable[1] = preg_replace('/id="[^"]*"/', '', $matchesTable[1]);
	
	//continuacao do 4° Passo
	$definicaoTable = '<table align="center" bgcolor="#' . $bgColor . '"' . $matchesTable[1] . '>';

	
	//6° Passo -> tamanho da tabela receberá o tamanho da tabela-1
	$patternTableHeight = '/height="(?P<tableHeight>\d*)"/';
	preg_match($patternTableHeight, $definicaoTable, $matchesTableHaight);
	if(isset($matchesTableHaight['tableHeight'])){
		$tableHeight = (int)$matchesTableHaight['tableHeight'] - 1;
		
		$definicaoTable = preg_replace($patternTableHeight, 'height="' . $tableHeight . '"', $definicaoTable);
	}
	
	//modifica o html inserindo as novas informações contidas na tag table
	$htmlCode = str_replace($matchesTable[0], $definicaoTable, $htmlCode);
}

//7° Passo -> Remover os colspan das tags td
$patternRemoveColspan = '/td\scolspan="[\d]*"/';
$htmlCode = preg_replace($patternRemoveColspan, 'td', $htmlCode);

//8° Passo -> Remove o último bloco de tr, esta deve contar imagens com o src="images/spacer.gif"
$patternRemoveLastTr = '/<tr>(\s*<td>\s*<img src="images\/spacer\.gif"\swidth="\d*"\sheight="\d*"\salt="">\s*<\/td>\s*)*<\/tr>/';
$htmlCode = preg_replace($patternRemoveLastTr, '',$htmlCode);


//9° Passo -> Toda TR que tem mais de uma TD dentro precisa ser alterada, deixa-se apenas uma td e cria uma nova tabela dentro dessa td
$patternModifyMultiplesTds = '/<tr>(\s*<td>\s*<img\ssrc="[^"]*"\swidth="\d*"\sheight="\d*"\salt="">\s*<\/td>\s*){2,}<\/tr>/';
preg_match_all($patternModifyMultiplesTds, $htmlCode, $matchesTables);

if($matchesTables){
	foreach ($matchesTables[0] as $infoTableTR){
		$newTDTable = '<tr><td><table border="0" cellpadding="0" cellspacing="0">'.$infoTableTR.'</table></td></tr>';
		
		$htmlCode = str_replace($infoTableTR, $newTDTable, $htmlCode);
	}
}


//10° Passo -> transfere as informações de tamanho das imagens presentes na tag img para a tag td, mandando a informação de tamanho também na imagem
$patternImgLengthToTd = '<td>\s*<img\s*src="(.*?)"\swidth="(\d*)"\sheight="(\d*)"\salt="">\s*<\/td>';
$htmlCode = preg_replace('/<td>\s*<img\s*src="(.*?)"\swidth="(\d*)"\sheight="(\d*)"\salt="">\s*<\/td>/', '<td width="$2" height="$3"><img src="$1" width="$2" height="$3" alt="" border="0" style="padding:0px; margin:0px; display:block"></td>', $htmlCode);


echo json_encode($htmlCode);
die;