import { useState, useCallback } from 'preact/hooks'

const useToggle: Function = (initialValue: boolean): [boolean, (nextValue?: any) => void] => {
    const [value, setValue] = useState<boolean>(initialValue)

    const toggle: any = useCallback(
        (nextValue?: any) => {
            if (typeof nextValue === 'boolean') {
                setValue(nextValue)
            } else {
                setValue(currentValue => !currentValue)
            }
        },
        [setValue]
    )

    return [value, toggle]
}

export default useToggle