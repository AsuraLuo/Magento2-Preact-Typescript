import { useState } from 'preact/hooks'

const useDefault: Function = (defaultValue: any, initialValue: any): [any, (nextValue?: any) => void] => {
    const [value, setValue] = useState(initialValue)
  
    if (value === undefined || value === null) {
      return [defaultValue, setValue]
    }
  
    return [value, setValue]
}
  
export default useDefault