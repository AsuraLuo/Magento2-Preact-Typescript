import { Component } from 'inferno'

export class Footer extends Component<any, any> {
    static displayName: string = `Footer`

    constructor (props: any) {
        super(props)
    }

    render () {
        return (
            <footer className="footer">
                Inferno Footer
            </footer>
        )
    }
}