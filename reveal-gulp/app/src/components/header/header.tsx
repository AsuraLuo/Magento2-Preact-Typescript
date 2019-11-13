import { h, Component } from 'preact'
import Logo from './module/logo'

export class Header extends Component<any, any> {
    static displayName: string = `Header`

    constructor (props: any) {
        super(props)
    }

    render () {
        const { logo } = window.commonJson

        return (
            <header className="header">
                <div className="panel">

                </div>
                <div className="content">
                    <Logo logo={logo} />
                </div>
            </header>
        )
    }
}