import { useEffect, useRef } from 'preact/hooks'

const useInterval: Function = (callback: Function, delay?: number | null) => {
    const savedCallback: any = useRef<Function>(() => {})

    useEffect(() => {
        savedCallback.current = callback
    })

    useEffect(() => {
        if (delay !== null) {
          const interval: NodeJS.Timeout = setInterval(() => savedCallback.current(), delay || 0)

          return () => clearInterval(interval)
        }
    
        return undefined
    }, [delay])
}

export default useInterval