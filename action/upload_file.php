<?php
require_once('./zip.php');
require_once('./jira.php');
session_start();
$url_redirect= str_replace('action','',dirname($_SERVER['PHP_SELF']));

// Pasta onde o arquivo vai ser salvo
$_UP['pasta'] = '../../pecas/' ;

// Tamanho máximo do arquivo (em Bytes)
$_UP['tamanho'] = 1024 * 1024 * 2; // 2Mb

// Array com as extensões permitidas
$_UP['extensoes'] = array('jpg', 'png', 'html');

// Renomeia o arquivo? (Se true, o arquivo será salvo como .jpg e um nome único)
$_UP['renomeia'] = false;

$directory_cliente = "";
$directory_task = "";
$directory_final = "";
$directory_final_images = ""; 
// Array com os tipos de erros de upload do PHP
$_UP['erros'][0] = 'Não houve erro';
$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
$_UP['erros'][4] = 'Não foi feito o upload do arquivo';

// Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
foreach ($_FILES['arquivo']['erro'] as $erro) {
	if ($erro != 0) {
		die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$erro]);
		exit; // Para a execução do script
	}
}

if ($_POST['cliente'] == "") {
	echo "Necessário informar um cliente";
	exit;
}elseif ($_POST['task'] == "") {
	$return[] = "Necessário informar um número de task";
	$_SESSION['retorno'] = $return;
	header("Location:$url_redirect");
	exit;
}else {
	$directory_cliente = strtolower($_UP['pasta'] . $_POST['cliente'] . '/');
	$directory_task = strtolower($directory_cliente . $_POST['task'] . '/');
}

if ($_FILES['arquivohtml']['name'] == "") {
    $return[] = "Selecione o html para upload;";
	$_SESSION['retorno'] = $return;
	header("Location:$url_redirect");
	exit;

}

foreach ($_FILES['arquivo']['name'] as $key=> $type) {
	if($_FILES['arquivo']['name'][$key] == "") {
	    $return[] = "Selecione a(s) imagens para upload;";
		$_SESSION['retorno'] = $return;
		header("Location:$url_redirect");
		exit;
	}
}


if (is_dir($directory_task)) {
	mkdir ($directory_task . 'images/', 0775);
	$directory_final = $directory_task;
	$directory_final_images = $directory_task . 'images/';
}elseif (is_dir($directory_cliente)) {
	mkdir ($directory_task, 0775);
	mkdir ($directory_task . 'images/', 0775);
	$directory_final = $directory_task;
	$directory_final_images = $directory_task . 'images/';
}else{
	mkdir ($directory_cliente);
	mkdir ($directory_task , 0775);
    mkdir ($directory_task . 'images/', 0775);
	$directory_final = $directory_task;
	$directory_final_images = $directory_task . 'images/';
}

// Faz a verificação da extensão do arquivo

if ($_FILES['arquivohtml']['type'] = "text/html") {
        move_uploaded_file($_FILES['arquivohtml']['tmp_name'], $directory_final . $_FILES['arquivohtml']['name']);
}

foreach ($_FILES['arquivo']['name'] as $key=> $type) {

if ($_FILES['arquivo']['type'] = "image/jpeg" || $_FILES['arquivo']['type'] = "image/jpg" || $_FILES['arquivo']['type'] = "image/png") { 
	move_uploaded_file($_FILES['arquivo']['tmp_name'][$key], $directory_final_images . $type);
}else{
// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
echo "Não foi possível enviar o arquivo, tente novamente";
}
}

unlink($directory_final . strtolower($_POST['task']) . ".zip");

Zip($directory_final,$directory_final . strtolower($_POST['task']) . ".zip", true);


if ($_POST['comentar'] == "on") {
	$issueKey = strtoupper($_POST['task']);
	$username = strtolower($_POST['login']);
	$password = strtolower($_POST['senha']);
	$myComment = "";
	if ($_POST['comentario'] != "") {
			$myComment = strtolower($_POST['comentario']) . chr(10) . chr(13) . "http://projetos.ogilvy.com.br/emailmkt/pecas/" . strtolower($_POST['cliente']) . '/' .strtolower( $_POST['task']) . '/' .strtolower($_FILES['arquivohtml']['name']) . chr(10) . "http://projetos.ogilvy.com.br/emailmkt/pecas/" . strtolower($_POST['cliente']) . '/' . strtolower($_POST['task']) . '/' . strtolower($_POST['task']) . ".zip";

		} else{
			$myComment = "http://projetos.ogilvy.com.br/emailmkt/pecas/" . strtolower($_POST['cliente']) . '/' .strtolower( $_POST['task']) . '/' .strtolower( $_FILES['arquivohtml']['name']) . chr(10) . "http://projetos.ogilvy.com.br/emailmkt/pecas/" . strtolower($_POST['cliente']) . '/' . strtolower($_POST['task']) . '/' . strtolower($_POST['task']) . ".zip";

		}

	jiraComment($issueKey, $username, $password, $myComment);
	jiraTranitions($issueKey, $username, $password, "41"); // 41 é o ID do estado "[DEV] QA"
	jiraTranitions($issueKey, $username, $password, "5"); // 41 é o ID do estado "Awaiting QA" "10019"
	jiraTranitions($issueKey, $username, $password, "10019"); // 41 é o ID do estado "[DEV] Awaiting QA"
}

//necessario um checkbox para validar se deve rodar a suite de teste
//será preciso um for para o caso de mais de um html (caso IBM)

if ($_POST['litmus'] == "on") {
	litmusEmailTest(strtolower($_POST['task']), $directory_final, $_FILES['arquivohtml']['name'], "http://projetos.ogilvy.com.br/emailmkt/pecas/" . strtolower($_POST['cliente']));
}

$filesReturn[] =  "http://projetos.ogilvy.com.br/emailmkt/pecas/" . strtolower($_POST['cliente']) . '/' .strtolower( $_POST['task']) . '/' .strtolower( $_FILES['arquivohtml']['name'])	;
$filesReturn[] = "<br>";
$filesReturn[] = "http://projetos.ogilvy.com.br/emailmkt/pecas/" . strtolower($_POST['cliente']) . '/' . strtolower($_POST['task']) . '/' . strtolower($_POST['task']) . ".zip";
$_SESSION['retorno'] = $filesReturn; 

header("Location:$url_redirect");
?>



