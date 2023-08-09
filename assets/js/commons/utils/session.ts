import { LINK_STORAGE_PREFIX } from "../constants/chain";

const sessioRegExp = new RegExp(LINK_STORAGE_PREFIX)
export function canRestoreSession(): boolean {
  
  const localKeys = Object.keys(localStorage);
  return localKeys.some((key) => key.match(sessioRegExp))

}  