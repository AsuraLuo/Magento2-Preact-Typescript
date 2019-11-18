import { ThemeConfig } from '../packages'

const themeConfig: ThemeConfig = {
    default: {
        name: 'preact',
        area: 'frontend',
        src: 'Restful/preact',
        locale: 'en_US',
        parent: '',
        mode: 'development',
        styles: 'scss',
        scripts: 'tsx',
        domain: 'http://dev.preact.cn/',
        files: [
            'css/app.css'
        ]
    }
}

export default themeConfig