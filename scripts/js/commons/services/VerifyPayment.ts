import axios from 'axios'
export function verifyPayment(paymentKey: string,transactionId:string,network:string="testnet") {
  
  
  let data = JSON.stringify({
    "paymentKey": paymentKey,
    "transactionId": transactionId,
    "network":network
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