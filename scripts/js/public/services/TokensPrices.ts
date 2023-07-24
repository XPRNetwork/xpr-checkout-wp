import axios from 'axios'
export function getTokensPrices() {
  
  let config = {
    method: 'post',
    maxBodyLength: Infinity,
    url: 'http://localhost:3002/wp-json/woow/v1/tokens-prices',
    headers: { 
      'Content-Type': 'application/json', 
    },
  };

  return axios(config)

}