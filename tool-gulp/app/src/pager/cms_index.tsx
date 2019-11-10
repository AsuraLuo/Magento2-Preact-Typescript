import { render } from 'inferno'
import { Store } from 'redux'
import { Provider } from 'inferno-redux'
import { initDevTools } from 'inferno-devtools'
import configureStore from './cms/store'
import { App } from './container/cms_index'

const store: Store = configureStore()

initDevTools()

render(
    <Provider store={store}>
        <App />
    </Provider>,
    document.getElementById('app') as HTMLElement    
)