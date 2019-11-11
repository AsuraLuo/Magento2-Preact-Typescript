import { Component } from 'inferno'
import { Logo } from './module/logo'

export class Header extends Component<any, any> {
    static displayName: string = `Header`

    constructor (props: any) {
        super(props)
    }

    render () {
        return (
            <header className="header">
                <div className="panel">

                </div>
                <div className="content">
                    <Logo />
                </div>
            </header>
        )
    }
}