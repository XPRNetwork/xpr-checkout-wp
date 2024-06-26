<?php
class ProtonRPC
{
  private $endpoint;

  public function __construct($endpoint)
  {
    $this->endpoint = $endpoint;
  }

  public function verifyTransaction($paymentKey)
  {

    $endpoint = $this->endpoint . '/v1/history/get_transaction';
    $data = array(
      'id' => $transactionId,
    );
    error_log("check transaction");
    error_log($endpoint);
    error_log(print_r($data, 1));

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    if ($response !== false) {
      $responseData = json_decode($response, true);
      $memo = $this->findValueByKey($responseData, "memo");
      return $memo == $paymentKey;
    }

    return false;
  }

  public function verifyPaymentStatusByKey($paymentKey)
  {

    $endpoint = $this->endpoint . '/v1/chain/get_table_rows';

    $data = array(
      'scope' => "wookey",
      'code' => "wookey",
      'table' => "payments",
      'json' => true,
      'index_position' => 2,
      'key_type' => 'sha256',
      'limit' => 1,
      'lower_bound' => $this->toEOSIOSha256($paymentKey),
      'upper_bound' => $this->toEOSIOSha256($paymentKey),

    );

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    if ($response !== false) {
      $responseData = json_decode($response, true);

      foreach ($responseData['rows'] as $row) {
        if ($row['paymentKey'] == $paymentKey && $row['status'] == 1) return true;
      }

      
      return null;
    } else {

      return null;
    }
    return null;
  }
  public function fetchPayments($store)
  {

    $endpoint = $this->endpoint . '/v1/chain/get_table_rows';
    $data = array(
      'scope' => 'wookey',
      'code' => 'wookey',
      'table' => 'payments',
      'json' => true,
      'index_position' => 3,
      'key_type' => 'i64',
      'limit' => 100,
      'lower_bound' => $store,
      'upper_bound' => $store,
      'reverse' => true

    );

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    if ($response !== false) {
      return json_decode($response, true);
    } else {

      return null;
    }
    return null;
  }

  public function fetchBalances($store)
  {

    $endpoint = $this->endpoint . '/v1/chain/get_table_rows';
    $data = array(
      'scope' => $store,
      'code' => 'wookey',
      'table' => 'balances',
      'json' => true,
      'limit' => 100,


    );

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    if ($response !== false) {
      return json_decode($response, true);
    } else {

      return null;
    }
    return null;
  }

  public function findTransaction($actor,$afterDate,$paymentKey)
  {

    $date = urlencode($afterDate);
    $endpoint = $this->endpoint ."/v2/history/get_actions?limit=999&account=$actor&filter=*:transfer&after=$date";
    
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    if ($response !== false) {
      return json_decode($response,true);
    } else {

      return null;
    }
    return null;
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

  function findValueByKey(array $array, $keyToFind)
  {
    foreach ($array as $key => $value) {
      if ($key === $keyToFind) {
        return $value;
      }

      if (is_array($value)) {
        $result = $this->findValueByKey($value, $keyToFind);
        if ($result !== null) {
          return $result;
        }
      }
    }

    return null; // Si la clé n'est pas trouvée
  }
}
