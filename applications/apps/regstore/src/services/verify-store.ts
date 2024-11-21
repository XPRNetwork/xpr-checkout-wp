import { JsonRpc } from '@proton/js';
import {Tables} from '../interfaces/xprcheckout'

export async function verifyStore(store: string, endpoints: string[]):Promise<boolean> {
  
  const rpc = new JsonRpc(endpoints);
  return await rpc.get_table_rows({
    json: true,
    code: 'xprcheckout',
    table: 'stores',
    scope: 'xprcheckout',
    index_position:0,
    upper_bound:store,
    lower_bound:store
  }).then((res) => {
    if (!res.rows) return false
    const rows = res.rows as Tables<'StoreTable'>[];
    return !!rows[0] && rows[0].store === store;
  })

}