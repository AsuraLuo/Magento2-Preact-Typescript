import { VNode, Component } from 'inferno'

declare global {
    namespace JSX {
        interface Element extends VNode {}
        interface ElementClass extends Component {}
        interface ElementAttributesProperty {
            props: {}
        }
        interface IntrinsicElements {
            [key: string]: any
        }
        interface PropsType {
            children: JSX.Element
            name: string
        }
        interface ElementChildrenAttribute {
            children: {}
        }
    }
}