import { Component, version } from 'inferno'

export class App extends Component<any, any> {
	static displayName: string = 'App'

    constructor (props: any, context: any) {
		super(props, context)
    }
    
    render () {
		return (
			<div>
				<h1>{`Welcome to Inferno ${version}`}</h1>
			</div>
		)
	}
}