export type AppPluginOptions = {
  isTestnet: boolean;
  networkCheckBoxSelector:string;
  actorFieldSelector:string;
  mainnetAccountFieldSelector:string;
  mainnetActor:string;
  testnetActor: string;
  mainnetAccessToken: string;
  testnetAccessToken: string;
}
 
export type StoreAccountConfig = {
  actorName: string;
  accessToken:string
}