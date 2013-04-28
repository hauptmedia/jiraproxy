<?php
include_once('RESTRequest.php');
include_once('JiraConnector.php');

$o_jira_connector = new JiraConnector(
    'http://jira.spielmeister.com',
    'julian.haupt',
    'l0g1n007'
);

try {
    $o_result = $o_jira_connector->createIssue(
        'SPELLJSPUB',
        'Bug',
        'Summary',
        'description'
    );

    $o_result2 = $o_jira_connector->createAttachment(
        $o_result->id,
        'test.png'
    );

} catch (Exception $e) {
    echo "ERR:" . $e->getmessage();
}

print_r($o_result);
print_r($o_result2);