import { createStore } from 'vuex'
import auth from './modules/auth'
import menu from './modules/menu'
import user from './modules/user'
import unit from './modules/unit'
import role from './modules/role'
import item from './modules/item'
import dataMenu from './modules/dataMenu'
import contact from './modules/contact'
import receive from './modules/receive'
import sale from './modules/sale'
import stock from './modules/stock'
import dashboard from './modules/dashboard'
import adjustment from './modules/adjustment'
import payable from './modules/payable'
import receivable from './modules/receivable'
import expense from './modules/expense'
import trash from './modules/trash'
import database from './modules/database'
import purchaseOrder from './modules/purchaseOrder'
import saleOrder from './modules/saleOrder'
import shipment from './modules/shipment'
import invoicePurchase from './modules/invoicePurchase'
import invoiceSale from './modules/invoiceSale'

export default createStore({
  modules: {
    auth,
    menu,
    user,
    unit,
    role,
    item,
    dataMenu,
    contact,
    receive,
    sale,
    stock,
    dashboard,
    adjustment,
    payable,
    receivable,
    expense,
    trash,
    database,
    purchaseOrder,
    saleOrder,
    shipment,
    invoicePurchase,
    invoiceSale
  }
})