import { h, FunctionComponent } from 'preact'

const Logo: FunctionComponent = (props: any) => {
    return (
        <a href={props.logo.url}>
            <img src={props.logo.src} alt={props.logo.alt} />
        </a>
    )
}

Logo.displayName = `Logo`

export default Logo