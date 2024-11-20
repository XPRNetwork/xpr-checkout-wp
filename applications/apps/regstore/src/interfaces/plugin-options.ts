export type AppPluginOptions = {
  isTestnet: boolean;
  networkCheckBoxSelector:string;
  actorFieldSelector:string;
  networkFieldSelector:string;
  mainnetActor:string;
  testnetActor: string;
  mainnetAccessToken: string;
  testnetAccessToken: string;
}
 
export type StoreAccountConfig = {
  actorName: string;
  accessToken:string
}