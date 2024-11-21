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
      'id' => $paymentKey,
    );
    
    $response = wp_remote_post($endpoint, array(
      'body' => wp_json_encode($data),
      'headers' => array('Content-Type' => 'application/json'),
      'timeout' => 45
    ));

    if (is_wp_error($response)) {
      return false; // Handle error
    }

    $responseData = json_decode(wp_remote_retrieve_body($response), true);
    $memo = $this->findValueByKey($responseData, "memo");
    return $memo == $paymentKey;
  }

  public function verifyPaymentStatusByKey($paymentKey)
  {
    $endpoint = $this->endpoint . '/v1/chain/get_table_rows';
    $data = array(
      'scope' => "xprcheckout",
      'code' => "xprcheckout",
      'table' => "payments",
      'json' => true,
      'index_position' => 2,
      'key_type' => 'sha256',
      'limit' => 1,
      'lower_bound' => $this->toEOSIOSha256($paymentKey),
      'upper_bound' => $this->toEOSIOSha256($paymentKey),
    );

    $response = wp_remote_post($endpoint, array(
      'body' => wp_json_encode($data),
      'headers' => array('Content-Type' => 'application/json'),
      'timeout' => 45
    ));

    if (is_wp_error($response)) {
      return null; // Handle error
    }

    $responseData = json_decode(wp_remote_retrieve_body($response), true);
    
    foreach ($responseData['rows'] as $row) {
      if ($row['paymentKey'] == $paymentKey && $row['status'] == 1) return true;
    }

    return null;
  }
  
  public function fetchPayment($paymentKey)
  {
    $endpoint = $this->endpoint . '/v1/chain/get_table_rows';
    $data = array(
      'scope' => "xprcheckout",
      'code' => "xprcheckout",
      'table' => "payments",
      'json' => true,
      'index_position' => 2,
      'key_type' => 'sha256',
      'limit' => 1,
      'lower_bound' => $this->toEOSIOSha256($paymentKey),
      'upper_bound' => $this->toEOSIOSha256($paymentKey),
    );

    $response = wp_remote_post($endpoint, array(
      'body' => wp_json_encode($data),
      'headers' => array('Content-Type' => 'application/json'),
      'timeout' => 45
    ));

    if (is_wp_error($response)) {
      return null; // Handle error
    }

    $responseData = json_decode(wp_remote_retrieve_body($response), true);
    
    if ($responseData['rows'] && $responseData['rows'][0])return $responseData['rows'][0];
    return null;
  }

  public function fetchPayments($store)
  {
    $endpoint = $this->endpoint . '/v1/chain/get_table_rows';
    $data = array(
      'scope' => 'xprcheckout',
      'code' => 'xprcheckout',
      'table' => 'payments',
      'json' => true,
      'index_position' => 3,
      'key_type' => 'i64',
      'limit' => 100,
      'lower_bound' => $store,
      'upper_bound' => $store,
      'reverse' => true
    );

    $response = wp_remote_post($endpoint, array(
      'body' => wp_json_encode($data),
      'headers' => array('Content-Type' => 'application/json'),
      'timeout' => 45
    ));

    if (is_wp_error($response)) {
      return null; // Handle error
    }

    return json_decode(wp_remote_retrieve_body($response), true);
  }

  public function fetchBalances($store)
  {
    $endpoint = $this->endpoint . '/v1/chain/get_table_rows';
    $data = array(
      'scope' => $store,
      'code' => 'xprcheckout',
      'table' => 'balances',
      'json' => true,
      'limit' => 100,
    );

    $response = wp_remote_post($endpoint, array(
      'body' => wp_json_encode($data),
      'headers' => array('Content-Type' => 'application/json'),
      'timeout' => 45
    ));

    if (is_wp_error($response)) {
      return null; // Handle error
    }

    return json_decode(wp_remote_retrieve_body($response), true);
  }

  public function findTransaction($actor, $afterDate, $paymentKey)
  {
    $date = urlencode($afterDate);
    $endpoint = $this->endpoint . "/v2/history/get_actions?limit=999&account=$actor&filter=*:transfer&after=$date";
    

    $response = wp_remote_get($endpoint, array(
      'headers' => array('Content-Type' => 'application/json'),
      'timeout' => 45
    ));

    if (is_wp_error($response)) {
      return null; // Handle error
    }

    return json_decode(wp_remote_retrieve_body($response), true);
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
