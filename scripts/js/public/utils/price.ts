export function truncateToPrecision(number: number,precision: number) {
  const multiplier = Math.pow(10, precision);
  console.log(number * multiplier,precision,'number * multiplier');
  console.log(Math.trunc(number * multiplier),"truncated value");
  return Math.trunc(number * multiplier) / multiplier;
}