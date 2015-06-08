<?php

function jiraComment($issueKey, $username, $password, $myComment) {

     $jiraAddressComment = $issueKey . "/comment";
     $data = json_encode(array('body' => $myComment));
     jiraCurl($jiraAddressComment, $issueKey, $username, $password, $data);
     unset($data);
}


function jiraTranitions($issueKey, $username, $password, $myTransitions) {

     $jiraAddressComment = $issueKey . "/transitions";
     $data = json_encode(array('transition' => array('id' => $myTransitions)));
     jiraCurl($jiraAddressComment, $issueKey, $username, $password, $data);
     unset($data);
}

function jiraCurl($jiraAddressComplemented, $issueKey, $username, $password, $data) {
    
    $jiraAddress = "http://jira.ogilvy.com.br:8080/rest/api/2/issue/" . $jiraAddressComplemented;

     $s = curl_init(); 
     curl_setopt($s,CURLOPT_URL,$jiraAddress); 
     curl_setopt($s,CURLOPT_POST,true);
     curl_setopt($s,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
     curl_setopt($s,CURLOPT_USERPWD,$username . ":" . $password); 
     curl_setopt($s,CURLOPT_POSTFIELDS, $data);

     curl_exec($s);

}
?>