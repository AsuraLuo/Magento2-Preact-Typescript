import { Component, render, version } from 'inferno'

class App extends Component<any, any> {
    private readonly tsxVersion: number

    constructor(props: any, context: any) {
		super(props, context);

		this.tsxVersion = 3.21
    }
    
    public render() {
		return (
			<div>
				<h1>{`Welcome to Inferno ${version} TSX ${this.tsxVersion}`}</h1>
			</div>
		)
	}
}

render(
    <App />, 
    document.getElementById('app') as HTMLElement    
)