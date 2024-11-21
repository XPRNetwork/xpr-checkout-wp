<?php 
function toPrecision($value, $precision, $mode = 'ceil', $forceDecimal = true) {
  $multiplier = pow(10, $precision);
  $powValue = $value * $multiplier;

  switch ($mode) {
      case 'ceil':
          $powValue = ceil($powValue);
          break;
      case 'floor':
          $powValue = floor($powValue);
          break;
      case 'round':
          $powValue = round($powValue);
          break;
      case 'none':
          $powValue = ceil($powValue);
          break;
  }

  $mutatedValue = $powValue / $multiplier;
  
  if ($forceDecimal) {
      return number_format($mutatedValue, $precision, '.', '');
  }
  return strval($mutatedValue);
}