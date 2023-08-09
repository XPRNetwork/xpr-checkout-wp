import axios from 'axios'
export function getTokensPrices(baseDomain:string) {
  
  let config = {
    method: 'post',
    maxBodyLength: Infinity,
    url: `${baseDomain}/wp-json/wookey/v1/tokens-prices`,
    headers: { 
      'Content-Type': 'application/json', 
    },
  };

  return axios(config)

}