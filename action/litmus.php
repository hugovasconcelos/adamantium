<?php

function litmusEmailTest($issueKey, $pathPeca, $nameHtml, $urlClient) {

	$issueKey = 'email-2008';
	$pathPeca = '../../pecas/teste/email-2008/';
	$nameHtml = 'index.html';
	$urlClient = 'http://projetos.ogilvy.com.br/emailmkt/pecas/teste';

	$urlPeca = $urlClient . "/" . $issueKey;
    $litmusAddressEmail = "/emails.xml";

    $text = createXml($issueKey, $pathPeca, $nameHtml, $urlClient);

    litmusCurl($litmusAddressEmail, $text);
    unset($xmlEmailTest);
}



function litmusCurl($litmusAddressComplemented, $data){


	 $litmusUrl = "https://subscriptions-recife%40ogilvy.com:D%236zlbb%24A%25IF@ogilvy29.litmus.com" . $litmusAddressComplemented;

	$s = curl_init(); 
	curl_setopt($s,CURLOPT_URL, $litmusUrl); 
	curl_setopt($s,CURLOPT_POST,true);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($s,CURLOPT_HTTPHEADER,array('Accept: application/xml', 'Content-Type:application/xml'));
	curl_setopt($s,CURLOPT_POSTFIELDS, $data);

     $result = curl_exec($s);
	 var_dump($result);
     die();
     
}


function createXml ($issueKey, $pathPeca, $nameHtml, $urlClient) {
	
	$pathHtml = $pathPeca . $nameHtml;

	$arquivo = fopen($pathHtml,'r');
	$string = file_get_contents($pathHtml);
	
	unlink($pathXml);
	unset($text);

	$text = '<?xml version="1.0"?><test_set><save_defaults>true</save_defaults><use_defaults>true</use_defaults><email_source><body><![CDATA[' . $string . ']]></body><subject>' . $issueKey . '</subject></email_source></test_set>';
	$text = str_replace('<img src="images/', '<img src="http://projetos.ogilvy.com.br/emailmkt/pecas/teste/email-2008/images/', $text);
	
	return $text;

}