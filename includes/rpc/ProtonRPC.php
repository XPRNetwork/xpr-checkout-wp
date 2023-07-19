<?php
class ProtonRPC
{
  private $endpoint;

  public function __construct($endpoint)
  {
    $this->endpoint = $endpoint;
  }

  public function verifyPaymentStatusByKey($contract, $scope, $table, $paymentKey, $limit = 10)
  {

    $endpoint = $this->endpoint . '/v1/chain/get_table_rows';

    $data = array(
      'scope' => $scope,
      'code' => $contract,
      'table' => $table,
      'json' => true,
      'index_position' => 2,
      'key_type' => 'sha256',
      'limit' => 100,
      'lower_bound' => $paymentKey,
      'upper_bound' => $paymentKey,

    );

    $formattedKey = '';
    for ($i = 0; $i < strlen($paymentKey); $i += 2) {
      $formattedKey .= substr($paymentKey, $i, 2);
    }

    error_log(print_r($formattedKey, 1));
    error_log(print_r($paymentKey, 1));
    error_log(print_r($this->encodeSha256($paymentKey), 1));
    error_log(print_r($data, 1));
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    error_log(print_r($response, 1));
    if ($response !== false) {
      $responseData = json_decode($response, true);

      foreach ($responseData['rows'] as $row) {
        error_log("Row key " . print_r($row['paymentKey'], 1));
        error_log("provided key " . print_r($paymentKey, 1));
        if ($row['paymentKey'] == $paymentKey && $row['status'] == 1) return true;
      }
      return false;
    } else {

      return false;
    }
    return false;
  }

  private  function u256ToHex($u256)
  {
    $hex = '0x';
    for ($i = 0; $i < 32; $i++) {
      $byte = hexdec(substr($u256, $i * 2, 2));
      $hex .= str_pad($byte, 2, '0', STR_PAD_LEFT);
    }
    return $hex;
  }

  private function encodeSha256($sha256Key)
  {


    $bytes = pack("H*", $sha256Key);

    // Inversion de l'ordre des octets
    $reversedBytes = strrev($bytes);

    // Conversion des octets en représentation hexadécimale
    $formattedKey = bin2hex($reversedBytes);

    return $formattedKey;
  }
}
