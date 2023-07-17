
import ProtonWeb from '@proton/web-sdk';
import { MAINNET_CHAIN_ID, TESTNET_CHAIN_ID } from './constants/chain';
import { MAINNET_ENDPOINTS, TESTNET_ENDPOINTS } from './constants/endpoints';
import { WOO_CHECKOUT_FORM_SELECTOR } from './constants/woo';
interface ProtonWCControllerOption {

  mainwallet?:string;
  testwallet?:string;
  testnet?:boolean;
  appName?:string;
  appLogo?:string;

}
export class ProtonWCController {

  private wooCheckForm:HTMLElement | null = null
  private pluginOptions: ProtonWCControllerOption = window.selector_options! as ProtonWCControllerOption;
  private txId: string | undefined = undefined;

  constructor() {
    
    console.log(this.pluginOptions)
    this.wooCheckForm = document.body.querySelector(WOO_CHECKOUT_FORM_SELECTOR);
    if (!this.wooCheckForm) return;
    this.wooCheckForm.addEventListener('submit', (e) => {
      if (!this.txId) {
        console.log('no TX')
        e.preventDefault();
        this.connectProton()
      }
    })

  }

  async connectProton() {
    
    const { session, link } = await ProtonWeb({
      linkOptions: {
        chainId: this.pluginOptions.testnet ? TESTNET_CHAIN_ID : MAINNET_CHAIN_ID,
        endpoints: this.pluginOptions.testnet ? TESTNET_ENDPOINTS : MAINNET_ENDPOINTS,
      },
      transportOptions: {
        requestAccount: this.pluginOptions.testnet ? this.pluginOptions.testwallet : this.pluginOptions.mainwallet, 
      },
      selectorOptions: {
        appName: this.pluginOptions.appName,
      }
    })

  }


}


window.addEventListener('DOMContentLoaded', () => { 

  console.log('dom content loaded!!!')
  window.protonWcController = new ProtonWCController()


})