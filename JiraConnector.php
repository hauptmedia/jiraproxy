<?php
class JiraConnector {
    protected $url;
    protected $username;
    protected $password;

    public function __construct($url, $username=NULL, $password=NULL) {
        $this->url      = $url;
        $this->username = $username;
        $this->password = $password;
    }

    protected function createRESTRequest() {
        $o_request = new RESTRequest();

        if( $this->username ) {
            $o_request->setBasicAuth( $this->username, $this->password );
        }

        $o_request->addAdditionalHttpHeader('X-Atlassian-Token: nocheck');

        return $o_request;
    }

    /**
     * Returns
     * stdClass Object
     * (
     *  [id] => 13321
     *  [key] => PROJECT-15
     *  [self] => http://jira.acme.com/rest/api/2/issue/13321
     * )
     *
     * @param $projectKey
     * @param $issuetypeName
     * @param $summary
     * @param $description
     * @return array
     * @throws Exception
     */
    public function createIssue($projectKey, $issuetypeName, $summary, $description) {
        $o_request = $this->createRESTRequest();

        $o_result = $o_request->post(
            $this->url . '/rest/api/2/issue/',
            array(
                "fields" => array(
                    "project"       => array(
                        "key"       => $projectKey
                    ),

                    "summary"       => $summary,
                    "description"   => $description,

                    "issuetype"     => array(
                        "name"      => $issuetypeName
                    )
                )
            )
        );


        if (property_exists($o_result, 'errorMessages')) {
            $exceptionMessage = '';

            foreach ($o_result->errorMessages as $errorMessage) {
                $exceptionMessage .= $errorMessage;
            }

            throw new Exception($exceptionMessage);
        }

        if (property_exists($o_result, 'errors')) {
            $exceptionMessage = '';

            foreach ($o_result->errors as $errorMessage) {
                $exceptionMessage .= $errorMessage;
            }

            throw new Exception($exceptionMessage);
        }

        return $o_result;
    }

    public function createAttachment($s_issueId, $s_filename, $s_remote_filename = NULL) {
        $o_request = $this->createRESTRequest();

        $o_request->attachFile("file", $s_filename, $s_remote_filename);

        $o_result = $o_request->post(
            $this->url . '/rest/api/2/issue/' . $s_issueId . '/attachments'
        );

        return $o_result;
    }
}