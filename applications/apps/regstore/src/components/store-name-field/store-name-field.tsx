import classNames from "classnames"
import { useRegstore } from "../../provider/regstore-provider";

type StoreNameFieldProps = React.HTMLAttributes<HTMLDivElement> 
export const StoreNameField: React.FunctionComponent<StoreNameFieldProps> = ({children}) => {
  
  const {storeName} = useRegstore()

  const classes = classNames({
    'p-2 flex items-center text-white font-bold rounded-tl-md rounded-bl-md border-r-1 border-white':true,
  })

  return <div className={classes}>
    {storeName && <>@{storeName}</>}
    {(!storeName || storeName === '') && <>No store</>}
    { children}
  </div>

}