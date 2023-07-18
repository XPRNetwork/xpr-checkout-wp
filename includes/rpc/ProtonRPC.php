<?php
class ProtonRPC
{
  private $endpoint;

  public function __construct($endpoint)
  {
    $this->endpoint = $endpoint;
  }

  public function sendRPCRequest($method, $params = array())
  {
    $data = array(
      'jsonrpc' => '2.0',
      'id' => '1',
      'method' => $method,
      'params' => $params
    );

    $ch = curl_init($this->endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
  }

  public function readTable($contract, $scope, $table, $limit = 10)
  {
    $params = array(
      'code' => $contract,
      'scope' => $scope,
      'table' => $table,
      'json' => true,
      'limit' => $limit
    );

    $response = $this->sendRPCRequest('chain.get_table_rows', $params);

    if (isset($response['result'])) {
      return $response['result']['rows'];
    } else {
      echo 'Erreur lors de la lecture de la table : ' . $response['error']['message'];
      return array();
    }
  }
}
