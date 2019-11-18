import 'preact/debug'
import { h, render } from 'preact'
import { Store } from 'redux'
import { Provider } from 'preact-redux'
import configureStore from './cms/store'
import { App } from './container/cms_index'

const store: Store = configureStore()

render(
    <Provider store={store}>
        <App />
    </Provider>,
    document.getElementById('app') as HTMLElement    
)