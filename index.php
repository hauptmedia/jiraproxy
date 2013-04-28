<?php
include('RESTRequest.php');

$o_request = new RESTRequest();
$o_request->setBasicAuth( 'julian.haupt', 'l0g1n007' );

$s_result = $o_request->post(
    'http://jira.spielmeister.com/rest/api/2/issue/',
    array(
        "fields" => array(
            "project" => array(
                "key" => "SPELLJS"
            ),

            "summary" => "test",
            "description" => "description",

            "issuetype" => array(
                "name" => "Bug"
            )
        )
    )
);

print $s_result;
