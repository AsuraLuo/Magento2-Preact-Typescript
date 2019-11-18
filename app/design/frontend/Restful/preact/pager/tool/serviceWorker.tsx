export const register: Function = () => {
    if (process.env.NODE_ENV === 'production' && 'serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            const swUrl: string = `${process.env.PUBLIC_URL}/sw.js`

            navigator.serviceWorker
                .register(swUrl)
                .then((registration: any) => {
                    registration.onupdatefound = () => {
                        const installingWorker: any = registration.installing

                        installingWorker.onstatechange = () => {
                            if (installingWorker.state === 'installed') {
                                if (navigator.serviceWorker.controller) {
                                    // At this point, the old content will have been purged and
                                    // the fresh content will have been added to the cache.
                                    // It's the perfect time to display a "New content is
                                    // available; please refresh." message in your web app.
                                    console.log('New content is available; please refresh.')
                                } else {
                                    // At this point, everything has been precached.
                                    // It's the perfect time to display a
                                    // "Content is cached for offline use." message.
                                    console.log('Content is cached for offline use.')
                                }
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error during service worker registration:', error)
                })
        })
    }
}

export const unregister: Function = () => {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.ready.then((registration: any) => {
            registration.unregister()
        })
    }
}