import { useState, useCallback } from 'preact/hooks'

const useSetState = <T extends object>(
    initialState: T = {} as T
): [T, (patch: Partial<T> | ((prevState: T) => Partial<T>)) => void] => {
    const [state, set] = useState<T>(initialState)

    const setState: any = useCallback(
        (patch: any) => {
            set(prevState => Object.assign({}, prevState, patch instanceof Function ? patch(prevState) : patch))
        },
        [set]
    );
  
    return [state, setState]
}

export default useSetState