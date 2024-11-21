import {useXPRN} from "xprnkit";

import {
  isValidStoreAccountConfig,
} from "../../utils/account";
import {useCallback, useEffect, useMemo} from "react";
import { RegStoreConfig } from "../../global";

type PropsType = {
  config?: RegStoreConfig;
};

export const NoSessionButton = (props: PropsType) => {
  const {connect,rpc} = useXPRN();

  const isValidConfig = useMemo(() => {
    return isValidStoreAccountConfig(props.config);
  }, [props]);

  const connectSession = useCallback((e: React.MouseEvent) => {
    e.preventDefault();
    connect();
  }, [connect])
  
  useEffect(() => {
    
    if (!rpc) return;
    if (!props.config) return;
    if (!props.config.store) return;
    rpc.get_table_rows({
      json: true,
      code: 'xprcheckout',
      table: 'stores',
      scope: 'xprcheckout',
      index_position:0,
      upper_bound:props.config.store,
      lower_bound:props.config.store
    }).then((res) => {
      
    })

  },[props.config,rpc])

  return (
    <>
      
      <button onClick={(e) => connectSession(e)}>
        {isValidConfig &&
          props.config &&
          `Connect to unregister ${props.config.store} account`}
        {!isValidConfig && "Connect to register a store account"}
      </button>
    </>
  );
};
