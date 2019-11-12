import { ThemeConfig } from '../packages'

const themeConfig: ThemeConfig = {
    default: {
        name: 'preact',
        area: 'frontend',
        src: 'Restful/preact',
        locale: 'en_US',
        parent: '',
        mode: 'production',
        styles: 'scss',
        scripts: 'tsx',
        files: [
            'css/app.css'
        ]
    }
}

export default themeConfig