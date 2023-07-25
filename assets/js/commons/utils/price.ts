export function truncateToPrecision(number: number | string, precision: number): string {
  const numberToTruncate = typeof number == 'string'? parseFloat(number) : number
  return numberToTruncate.toFixed(precision);
}