<?php
require_once 'rest_client.php';

define('VPS247_ROOT', 'http://admin.vps247.com');

class VpsClient {
  private $rest_client;

  public function __construct($key) {
    $this->rest_client = new RESTClient(VPS247_ROOT, $key);
  }

  public function authenticate_end_user($username, $plaintext_password) {
    list($code, $resp) = $this->get('/end_users');
    if ($code == 200) {
      $end_users = simplexml_load_string($resp);
      foreach ($end_users as $end_user) {
        if ($end_user->username == $username && $end_user->{'crypted-password'} == sha1($plaintext_password))

          return (string)$end_user->id;
      }
    }
    return false;
  }
  
  public function check_vm_permission($vm_id, $end_user_id) {
    list($code, $resp) = $this->get('/vms/' . $vm_id);
    if ($code == 200) {
      $vm = simplexml_load_string($resp);
      if ($vm->{'end-user-id'} == $end_user_id)
        return true;
    }
    return false;
  }

  public function get_vm($id) {
    list($code, $resp) = $this->get("/vms/{$id}");
    if ($code == 200) {
      return simplexml_load_string($resp);
    }
    return false;
  }
  
  public function stop_vm($id) {
    list($code, $resp) = $this->post("/vms/{$id}/stop");
    print_r($resp);
    if ($code == 200) {
      return true;
    }
    return false;
  }
  public function force_stop_vm($id) {
    list($code, $resp) = $this->post("/vms/{$id}/stop_now");
    if ($code == 200) {
      return true;
    }
    return false;
  }
  public function start_vm($id) {
    list($code, $resp) = $this->post("/vms/{$id}/start");
    print_r($resp);
    if ($code == 200) {
      return true;
    }
    return false;
  }

  public function get_vms() {
    list($code, $resp) = $this->get('/vms');
    if ($code == 200) {
      return simplexml_load_string($resp);
    }
    return false;
  }
  public function get_private_vlans() {
    list($code, $resp) = $this->get('/private_vlans');
    if ($code == 200) {
      return simplexml_load_string($resp);
    }
    return false;
  }
  public function get_private_vlan($id) {
    list($code, $resp) = $this->get("/private_vlans/{$id}");
    if ($code == 200) {
      return simplexml_load_string($resp);
    }
    return false;
  }
  public function get_private_vlan_vms($private_vlan_id) {
    list($code, $resp) = $this->get("/private_vlans/{$private_vlan_id}/vms");
    if ($code == 200) {
      return simplexml_load_string($resp);
    }
    return false;
  }  
    
  public function create_private_vlan($name,$end_user_id) {
    list($code, $resp) = $this->post('/private_vlans',array("private_vlan" => array("name" => $name, "end_user_id" => $end_user_id)));
    if ($code == 201) {
      return simplexml_load_string($resp);
    }
    return false;
  }
  
  public function delete_private_vlan($id) {
    list($code, $resp) = $this->delete("/private_vlans/{$id}");
    #var_dump($code);
    #exit;
    if ($code == 200) {
      #return simplexml_load_string($resp);
      return true;
    }
    return false;
  }
  
  public function set_vm_private_vlan($vm_id,$private_vlan_id) {
    list($code, $resp) = $this->put("/vms/{$vm_id}",array("vm" => array("private_vlan_id" => $private_vlan_id)));
    if ($code == 200) {
      return simplexml_load_string($resp);
    }
    return false;
  }

  // Generic Functions
  public function get($path) {
    return $this->rest_request($path, REST_CLIENT_GET, null);
  }

  public function get_noxml($path) {
    $this->rest_client->createNonXmlGetRequest($path);
    $this->rest_client->sendRequest();

    return array( $this->rest_client->getResponseCode(),
                  $this->rest_client->getResponse() );
  }

  public function get_auth($path,$username,$password) {
    $this->rest_client->createAuthGetRequest($path,$username,$password);
    $this->rest_client->sendRequest();

    return array( $this->rest_client->getResponseCode(),
                  $this->rest_client->getResponse() );
  }

  public function post($path, $data = null) {
    return $this->rest_request($path, REST_CLIENT_POST, $data);
  }
  
  public function put($path, $data = null) {
    return $this->rest_request($path, REST_CLIENT_PUT, $data);
  }
  
  public function delete($path, $data = null) {
    return $this->rest_request($path, REST_CLIENT_DELETE, $data);
  }
  
  

  // Private functions
  private function rest_request($path, $method, $data) {
    $this->rest_client->createRequest($path, $method, $data);
    $this->rest_client->sendRequest();

    return array( $this->rest_client->getResponseCode(),
                  $this->rest_client->getResponse() );
  }
}
?>