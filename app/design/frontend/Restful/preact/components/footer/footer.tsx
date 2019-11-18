import { h, Component } from 'preact'
import Copyright from './module/copyright'

export class Footer extends Component<any, any> {
    static displayName: string = `Footer`

    constructor (props: any) {
        super(props)
    }

    render () {
        const { copyright } = window.commonJson

        return (
            <footer className="footer">
                <Copyright copyright={copyright} />
            </footer>
        )
    }
}