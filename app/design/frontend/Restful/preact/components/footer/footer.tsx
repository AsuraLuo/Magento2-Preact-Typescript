import { Component } from 'inferno'
import { Copyright } from './module/copyright'

export class Footer extends Component<any, any> {
    static displayName: string = `Footer`

    constructor (props: any) {
        super(props)
    }

    render () {
        return (
            <footer className="footer">
                <Copyright />
            </footer>
        )
    }
}