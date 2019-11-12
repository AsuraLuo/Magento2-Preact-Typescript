import { h, Component } from 'preact'

export class App extends Component<any, any> {
	static displayName: string = 'App'

    constructor (props: any, context: any) {
		super(props, context)
	}
	
	componentDidMount () {
        console.log(`Magento2 App is bootstrap ...`)
    }
    
    render () {
		return (
			<div>
				<h1>{`Welcome to Preact Theme`}</h1>
			</div>
		)
	}
}