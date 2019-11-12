import { Component } from 'inferno'

export class Logo extends Component<any, any> {
    static displayName: string = `Logo`

    constructor (props: any) {
        super(props)
    }

    render () {
        const { logo } = window.commonJson
        return (
            <a href={logo.url}>
                <img src={logo.src} alt={logo.alt} />
            </a>
        )
    }
}