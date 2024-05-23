import axios from 'axios'
export function getConvertedToUSD(baseDomain:string,storeCurrency:string,orderAmount:number) {
  
  
  let data = JSON.stringify({
    "storeCurrency": storeCurrency,
    "amount": orderAmount,
  });

  let config = {
    method: 'post',
    maxBodyLength: Infinity,
    url: `${baseDomain}/wp-json/xprcheckout/v1/price-rates`,
    headers: { 
      'Content-Type': 'application/json', 
    },
    data : data
  };

  return axios(config)

}