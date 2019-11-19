import { useEffect, EffectCallback } from 'preact/hooks'

const useEffectOnce: Function = (effect: EffectCallback) => {
    useEffect(effect, [])
}

export default useEffectOnce