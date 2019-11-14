import { h, FunctionComponent } from 'preact'

interface CopyrightProps {
    copyright: string
}

const Copyright: FunctionComponent<CopyrightProps> = (props: CopyrightProps) => {
    const { copyright } = props

    return (
        <div className="copyright">
            <p>{copyright}</p>
        </div>
    )
}

Copyright.displayName = `Copyright`

export default Copyright