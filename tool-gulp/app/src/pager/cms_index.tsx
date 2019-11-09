import { Component, render, version } from 'inferno'
import { initDevTools } from 'inferno-devtools' 

class App extends Component<any, any> {
    public inferno_version: string | undefined

    constructor(props: any, context: any) {
		super(props, context)

		this.inferno_version = version
    }
    
    render () {
		return (
			<div>
				<h1>{`Welcome to Inferno ${version} TSX ${this.inferno_version}`}</h1>
			</div>
		)
	}
}

initDevTools()

render(
    <App />, 
    document.getElementById('app') as HTMLElement    
)