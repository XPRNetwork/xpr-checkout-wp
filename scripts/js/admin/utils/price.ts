export function truncateToPrecision(number: number,precision: number) {
  const multiplier = Math.pow(10, precision);
  return Math.trunc(number * multiplier) / multiplier;
}