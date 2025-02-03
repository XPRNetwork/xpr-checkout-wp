<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
  }
function xprcheckout_u64_to_symbol($value) {
    
    $value = (string)$value;
    $precision = bcmod($value, '256');
    if ($precision >= 48 && $precision <= 57) {

        $precision = bcsub($precision, '48');
    }

    $symbol_code = bcdiv($value, '256', 0); // Integer division
    $symbol_name = '';
    while (bccomp($symbol_code, '0') > 0) {
        
        $char_code = bcmod($symbol_code, '256');
        $symbol_name .= chr($char_code);
        $symbol_code = bcdiv($symbol_code, '256', 0);
    }
    return array('symbolName' => $symbol_name, 'precision' => (int)$precision);
}
?>