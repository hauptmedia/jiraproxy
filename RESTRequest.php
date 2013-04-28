<?php
class RESTRequest {
    protected $ch;
    protected $acceptType;
    protected $contentType;


    protected $requestBody;
    protected $requestLength;


    protected $timeout = 60;
    protected $ssl_verifypeer = false;

    public function __construct() {
        $this->ch           = curl_init();
        $this->acceptType   = 'application/json';
        $this->contentType  = 'Content-Type: application/json';

    }

    public function __destruct() {
        curl_close($this->ch);
    }

    public function reset() {
        curl_close($this->ch);
        $this->ch = curl_init();

        $this->requestBody       = null;
        $this->requestLength     = 0;
    }

    protected function execute () {
        $this->setCurlOpts();
        $result = curl_exec($this->ch);
        $this->reset();
        return $result;
    }

    protected function setCurlOpts () {
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array (
            'Accept: ' . $this->acceptType, $this->contentType,
            'X-Atlassian-Token: nocheck'
            )
        );
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
    }

    public function setBasicAuth ($username, $password) {
        curl_setopt($this->ch, CURLOPT_USERPWD, $username . ':' . $password);
    }

    public function attachFile($name, $filename) {
        $fileContents = file_get_contents($filename);
        $boundary = "----------------------------".substr(md5(rand(0,32000)), 0, 12);

        $data = "--".$boundary."\r\n";
        $data .= "Content-Disposition: form-data; name=\"".$name."\"; filename=\"".basename($this->filename)."\"\r\n";
        $data .= "Content-Type: ".mime_content_type($this->filename)."\r\n";
        $data .= "\r\n";
        $data .= $fileContents."\r\n";
        $data .= "--".$boundary."--";

        $this->requestBody = $data;
        $this->contentType = 'Content-Type: multipart/form-data; boundary='.$boundary;
    }

    public function get($url) {
        curl_setopt($this->ch, CURLOPT_URL, $url);

        return $this->execute();
    }

    public function post ($url, $data = null) {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));

        return $this->execute();
    }

    public function put ($url, $data = null) {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));

        return $this->execute();
    }

    public function delete ($url) {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        return $this->execute();
    }
}
?>