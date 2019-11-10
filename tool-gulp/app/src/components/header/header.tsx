import { Component } from 'inferno'

export class Header extends Component<any, any> {
    static displayName: string = `Header`

    constructor (props: any) {
        super(props)
    }

    render () {
        return (
            <header className="header">
                Inferno Header
            </header>
        )
    }
}