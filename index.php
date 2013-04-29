<?php
include_once('RESTRequest.php');
include_once('JiraConnector.php');

if(!array_key_exists('action', $_REQUEST) || $_REQUEST['action'] != 'createIssue') {
    echo "ERR:Not implemented";
    exit;
}

$o_jira_connector = new JiraConnector(
    'http://jira.spielmeister.com',
    'public',
    'ayFliHivVut0'
);


try {

    $a_jira_bug_report = array();

    if(array_key_exists('description', $_REQUEST)) {
        array_push($a_jira_bug_report, $_REQUEST['description'] );
    }

    if(array_key_exists('errorMsg', $_REQUEST)) {
        array_push($a_jira_bug_report, $_REQUEST['errorMsg'] );
    }


    if(array_key_exists('version', $_REQUEST)) {
        array_push($a_jira_bug_report, $_REQUEST['version'] );
    }

    if(count($a_jira_bug_report) == 0) {
        echo "ERR:empty request";
        exit;
    }

    $s_jira_summary = substr(
            str_replace(
            array("\r\n", "\n", "\r"),
            ' ',
            implode(' ', $a_jira_bug_report)
        ), 0, 99
    );

    $s_jira_description = htmlentities( implode("\r\n\r\n", $a_jira_bug_report) );

    $o_result = $o_jira_connector->createIssue(
        'SPELLJS',
        'Bug',
        $s_jira_summary,
        $s_jira_description
    );

    if( array_key_exists( 'file', $_FILES ) ) {
        $o_result2 = $o_jira_connector->createAttachment(
            $o_result->id,
            $_FILES['file']['tmp_name'],
            $_FILES['file']['name']
        );
    }

    echo "OK:" . $o_result->key;

} catch (Exception $e) {
    echo "ERR:" . $e->getmessage();

    mail('info@spielmeister.com', 'Error in bugtracker proxy', $e->getMessage(). "\r\n". print_r($_REQUEST, true));
}
