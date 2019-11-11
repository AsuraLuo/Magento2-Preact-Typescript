import { createStore, Store, applyMiddleware, compose } from 'redux'
import thunk from 'redux-thunk'
import { createLogger } from 'redux-logger'
import appReducer from './reducers'

const composeEnhancer: Function = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose
const configureStore: any = () => {
    const store: Store = createStore(
        appReducer,
        composeEnhancer(
            applyMiddleware(thunk, createLogger())
        )
    )

    return store
}

export default configureStore