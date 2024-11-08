import { JsonRpc } from '@proton/js';
import {Tables} from '../interfaces/xprcheckout'

export type PaymentStatus = 'pending'|'paid'|'refunded'

export async function verifyPaymentStatus(paymentKey: string, endpoints: string[]):Promise<PaymentStatus> {
  
  const rpc = new JsonRpc(endpoints);
  return await rpc.get_table_rows({
    json: true,
    code: 'xprcheckout',
    table: 'payments',
    scope: 'xprcheckout',
    index_position: 2,
    key_type: 'sha256',
    limit:1,
    upper_bound:paymentKey
  }).then((res) => {
    console.log(res)
    if (!res.rows) return 'pending'
    const rows = res.rows as Tables<'PaymentsTable'>[];
    if (!rows[0]) return 'pending'
    switch (rows[0].status) {
      case 0:
        return 'pending'
      case 1:
        return 'paid'
      case -2:
        return 'refunded'
      default:
        return 'pending'
    }
    
    
  })

}