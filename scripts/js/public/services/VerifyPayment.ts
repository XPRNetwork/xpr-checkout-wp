import axios from 'axios'
export function verifyPayment(paymentKey: string) {
  
  
  let data = JSON.stringify({
    "paymentKey": paymentKey
  });

  let config = {
    method: 'post',
    maxBodyLength: Infinity,
    url: 'http://localhost:3002/wp-json/woow/v1/verify-payment',
    headers: { 
      'Content-Type': 'application/json', 
    },
    data : data
  };

  return axios(config)

}