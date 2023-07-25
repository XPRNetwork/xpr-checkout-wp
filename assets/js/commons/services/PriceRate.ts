import axios from 'axios'
export function getConvertedToUSD(storeCurrency:string,orderAmount:number) {
  
  
  let data = JSON.stringify({
    "storeCurrency": storeCurrency,
    "amount": orderAmount,
  });

  let config = {
    method: 'post',
    maxBodyLength: Infinity,
    url: 'http://localhost:3002/wp-json/woow/v1/price-rates',
    headers: { 
      'Content-Type': 'application/json', 
    },
    data : data
  };

  return axios(config)

}