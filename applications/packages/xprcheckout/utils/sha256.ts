export function  toEOSIOSha256(sha256Key: string): string {
  const part1 = sha256Key.substring(0, 32);
  const part2 = sha256Key.substring(32);

  // Inverser les bytes de chaque partie
  const reversedPart1 = reverseHexString(part1);
  const reversedPart2 = reverseHexString(part2);

  // Rassembler les deux parties
  return reversedPart1 + reversedPart2;
}

function reverseHexString(hexString: string): string {
  let reversed = '';
  for (let i = hexString.length - 2; i >= 0; i -= 2) {
    reversed += hexString.substr(i, 2);
  }
  return reversed;
}