import { ThemeConfig } from '../packages'

const themeConfig: ThemeConfig = {
    default: {
        name: 'inferno',
        area: 'frontend',
        src: 'Restful/inferno',
        locale: 'en_US',
        parent: '',
        mode: 'production',
        styles: 'less',
        scripts: 'tsx',
        files: [
            'css/app.css'
        ]
    }
}

export default themeConfig