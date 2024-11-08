export function wait(ms: number): Promise<void>{
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      resolve();
      console.log('awaited ',ms)
    },ms)
    
  })
}