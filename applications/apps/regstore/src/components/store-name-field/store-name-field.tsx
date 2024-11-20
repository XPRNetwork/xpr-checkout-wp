import classNames from "classnames"

type StoreNameFieldProps = React.HTMLAttributes<HTMLDivElement> & {step?:string,store?:string,network?:string}
export const StoreNameField: React.FunctionComponent<StoreNameFieldProps> = ({children,store,network,step}) => {
  
  const classes = classNames({
    'p-2 flex items-center text-white font-bold rounded-tl-md rounded-bl-md border-r-1 border-white':true,
  })

  return <div className={classes}>
    { children}
  </div>

}