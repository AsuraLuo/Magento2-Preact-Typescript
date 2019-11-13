import { h, Component, Fragment } from 'preact'
import { Header } from '@components/header/header'
import { Footer } from '@components/footer/footer'

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
			<Fragment>
				<Header />
				<h1>{`Welcome to Preact Theme`}</h1>
				<Footer />
			</Fragment>
		)
	}
}