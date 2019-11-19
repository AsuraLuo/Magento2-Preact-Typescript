import { useEffect, useState, useMemo } from 'preact/hooks'

const createBreakpoint: Function = (
    breakpoints: { [name: string]: number } = { desktop: 1440, laptop: 1024, tablet: 768, mobile: 640 }
) => () => {
    const [screen, setScreen ] = useState(0)

    useEffect(() => {
        const setSideScreen: any = (): void => {
            setScreen(window.innerWidth)
        }

        setSideScreen()
        window.addEventListener('resize', setSideScreen)
        return () => {
            window.removeEventListener('resize', setSideScreen)
        }
    })

    const sortedBreakpoints: [string, number][] = useMemo(() => Object.entries(breakpoints).sort((a: any, b: any) => (a[1] >= b[1] ? 1 : -1)), [
        breakpoints
    ])

    const result: string = sortedBreakpoints.reduce((acc, [name, width]) => {
        if (screen >= width) {
            return name
        } else {
            return acc
        }
    }, sortedBreakpoints[0][0])

    return result
}

export default createBreakpoint