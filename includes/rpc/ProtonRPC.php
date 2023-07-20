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
      'lower_bound' => $this->toEOSIOSha256($paymentKey),
      'upper_bound' => $this->toEOSIOSha256($paymentKey),

    );

    $formattedKey = '';
    for ($i = 0; $i < strlen($paymentKey); $i += 2) {
      $formattedKey .= substr($paymentKey, $i, 2);
    }

    error_log(print_r($formattedKey, 1));
    error_log(print_r($paymentKey, 1));
    error_log(print_r($this->toEOSIOSha256($paymentKey), 1));
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


  private function toEOSIOSha256($sha256Key)
  {
    $part1 = substr($sha256Key, 0, 32);
    $part2 = substr($sha256Key, 32);

    // Inverser les bytes de chaque partie
    $reversedPart1 = strrev(pack("H*", $part1));
    $reversedPart2 = strrev(pack("H*", $part2));

    // Reconvertir les parties en strings
    $reversedString1 = unpack("H*", $reversedPart1)[1];
    $reversedString2 = unpack("H*", $reversedPart2)[1];

    // Rassembler les deux parties
    return  $reversedString1 . $reversedString2;
  }
}
