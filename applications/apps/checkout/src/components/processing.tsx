import classNames from "classnames";
import React from "react";

type ProcessingPropsType = React.HTMLAttributes<HTMLDivElement> & {label?:string};

export const Processing: React.FunctionComponent<ProcessingPropsType> = ({ className,label }) => {
  
  const classes = classNames({
    'flex flex-col justify-center items-center':true,
    [`${className}`]: className,
  })

  return (
    <div className={classes}>
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        strokeWidth="2"
        strokeLinecap="round"
        strokeLinejoin="round"
        className="lucide lucide-refresh-cw animate-spin w-20 h-20 stroke-purple-600"
      >
        <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
        <path d="M21 3v5h-5" />
        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
        <path d="M8 16H3v5" />
      </svg>
      <span>{label}</span>
    </div>
  );
};
