import args from '../util/args'

const modulesConfig: string[] = [
    'cms_index',
    'cms_page',
    'catalog_category',
    'catalog_product',
    'catalog_compare',
    'catalog_search',
    'contact_index',
    'checkout_cart',
    'checkout_index',
    'customer_login',
    'customer_create',
    'customer_account',
    'customer_password',
    'customer_logout',
    'downloadable_product',
    'wishlist_index',
    'customer_address',
    'customer_edit',
    'address_form',
    'vault_cards',
    'paypal_billing',
    'review_customer',
    'review_details',
    'newsletter_index',
    'search_term',
    'sales_order',
    'sales_guest',
    'sales_print',
    'orders_view',
    'gift_card',
    'send_friend'
]
let compileModules: string[] = []
const compileM: string = args.module

if (compileM) {
    if (modulesConfig.includes(compileM)) {
        compileModules.push(compileM)
    }
} else {
    compileModules = modulesConfig
}

export default compileModules