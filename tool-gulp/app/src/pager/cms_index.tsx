import { render } from 'inferno'
import { initDevTools } from 'inferno-devtools'
import { App } from './container/cms_index'

initDevTools()

render(
    <App />, 
    document.getElementById('app') as HTMLElement    
)