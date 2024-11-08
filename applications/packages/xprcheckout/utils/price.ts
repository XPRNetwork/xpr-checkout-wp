export function truncateToPrecision(number: number | string, precision: number): string {
  const numberToTruncate = typeof number == 'string'? parseFloat(number) : number
  return numberToTruncate.toFixed(precision);
}

export function toPrecision(value: number, precision: number,mode:'ceil'|'floor'|'round'|'none' = 'ceil',forceDecimal:boolean = true) {
  
  const multiplier = Math.pow(10, precision);
  let powValue = value * multiplier;
  switch (mode) {
    
    case 'ceil':
      powValue = Math.ceil(powValue);
      break;
    case 'floor':
      powValue = Math.floor(powValue);
      break;
    case 'round':
      powValue = Math.round(powValue);
      break;
    case 'none':
      powValue = Math.ceil(powValue);
      break;
  }

  const mutatedValue = powValue / multiplier
  if (forceDecimal) return mutatedValue.toFixed(precision)
  return mutatedValue.toString() ;

}