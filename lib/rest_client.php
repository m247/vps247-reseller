<?php
/**
 * Copyright (c) 2011 M247 Ltd
 * Original by Geoff Garside - 25/06/2008
 * Modified by Phil Evans - 17/02/2011
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

require_once "HTTP/Request.php";

define('REST_CLIENT_GET',     1);
define('REST_CLIENT_POST',    2);
define('REST_CLIENT_PUT',     3);
define('REST_CLIENT_DELETE',  4);

class RESTClient {
    private $root_url = "";
    private $curr_url = "";
    private $key = "";
    private $response = "";
    private $responseBody = "";
    private $responseCode = null;
    private $req = null;
    public function __construct($root_url = "", $key = "") {
        $this->root_url = $root_url;
        $this->key = $key;
        return true;
    }
    
    public function createNonXmlGetRequest($url) {
        if (preg_match("/^https?:\/\//", $url) == 0) {
          $this->curr_url = $this->root_url . $url;
        } else {
          $this->curr_url = $url;
        }
        $this->req =& new HTTP_Request($this->curr_url);
        $this->req->addHeader("User-Agent",   "VPS Client v0.1");
        $this->req->addHeader("Accept",       "text/html,image/png");
        $this->req->addHeader("X-VPS247-API-KEY", $this->key);
        $this->req->setMethod(HTTP_REQUEST_METHOD_GET);
    }
    
    public function createAuthGetRequest($url,$username,$password) {
        if (preg_match("/^https?:\/\//", $url) == 0) {
          $this->curr_url = $this->root_url . $url;
        } else {
          $this->curr_url = $url;
        }
        $this->req =& new HTTP_Request($this->curr_url);
        $this->req->addHeader("User-Agent",   "VPS Client v0.1");
        $this->req->addHeader("Accept","text/xml,application/xml");
        $this->req->setBasicAuth($username,$password);
        $this->req->setMethod(HTTP_REQUEST_METHOD_GET);
    }

    public function createRequest($url, $method, $arr = null) {
        if (preg_match("/^https?:\/\//", $url) == 0) {
          $this->curr_url = $this->root_url . $url;
        } else {
          $this->curr_url = $url;
        }

        $this->req =& new HTTP_Request($this->curr_url);

        $this->req->addHeader("User-Agent",   "VPS Client v0.1");
        $this->req->addHeader("Accept",       "text/xml,application/xml");
        if ($method == REST_CLIENT_POST || $method == REST_CLIENT_PUT) {
          $this->req->addHeader("Content-Type", "application/x-www-form-urlencoded");
        } else {
          $this->req->addHeader("Content-Type", "text/xml");
        }
        $this->req->addHeader("X-VPS247-API-KEY", $this->key);

        switch($method) {
            case REST_CLIENT_GET:
                $this->req->setMethod(HTTP_REQUEST_METHOD_GET);
                break;
            case REST_CLIENT_POST:
                $this->req->setMethod(HTTP_REQUEST_METHOD_POST);
                $this->addPostData($arr);
                break;
            case REST_CLIENT_PUT:
                $this->req->setMethod(HTTP_REQUEST_METHOD_POST);
                $arr["_method"]="PUT";
                $this->addPostData($arr);
                break;
            case REST_CLIENT_DELETE:
                $this->req->setMethod(HTTP_REQUEST_METHOD_DELETE);
                // to-do
                break;
        }
    }
    private function addPostData($arr) {
        if ($arr != null) {
            foreach ($arr as $key => $value) {
                $this->req->addPostData($key, $value);
            }
        }
    }
    public function sendRequest() {
        $this->response = $this->req->sendRequest();
        if (PEAR::isError($this->response)) {
            echo $this->response->getMessage();
            die();
        } else {
            $this->responseBody = $this->req->getResponseBody();
            $this->responseCode = $this->req->getResponseCode();
        }
    }
    public function getResponse() {
        return $this->responseBody;
    }
    public function getResponseCode() {
        return $this->responseCode;
    }
}
?>
