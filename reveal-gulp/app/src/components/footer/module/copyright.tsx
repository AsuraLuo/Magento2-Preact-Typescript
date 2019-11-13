import { h, Component } from 'preact'

export class Copyright extends Component<any, any> {
    static displayName: string = `Copyright`

    constructor (props: any) {
        super(props)
    }

    render () {
        const { copyright } = window.commonJson
        return (
            <div className="copyright">
                <p>{copyright}</p>
            </div>
        )
    }
}