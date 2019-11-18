import 'preact/debug'
import { h, render } from 'preact'
import { Store } from 'redux'
import { Provider } from 'preact-redux'
import configureStore from './cms/store'
import { App } from './container/cms_index'
import { register } from './tool/serviceWorker'

const store: Store = configureStore()

render(
    <Provider store={store}>
        <App />
    </Provider>,
    document.getElementById('app') as HTMLElement    
)

register(`${window.commonJson.pub_path}/js/sw.js`, {
    ready () {
        console.log('App is being served from cache by a service worker.')
    },
    registered () {
        console.log('Service worker has been registered.')
    },
    cached () {
        console.log('Content has been cached for offline use.')
    },
    updatefound () {
        console.log('New content is downloading.')
    },
    updated () {
        console.log('New content is available; please refresh.')
        window.location.reload(true)
    },
    offline () {
        console.log('No internet connection found. App is running in offline mode.')
    },
    error (error: Error) {
        console.error('Error during service worker registration:', error)
    }
})