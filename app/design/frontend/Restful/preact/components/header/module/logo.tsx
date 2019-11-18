import { h, FunctionComponent } from 'preact'

interface LogoProps {
    url: string
    src: string
    width: number
    height: number
    alt?: string
}

const Logo: FunctionComponent<LogoProps> = (props: LogoProps) => {
    const { url, src, width, height, alt } = props

    return (
        <a href={url}>
            <img src={src} width={width} height={height} alt={alt} />
        </a>
    )
}

Logo.displayName = `Logo`

export default Logo