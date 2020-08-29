import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import store from '@/store'

import layout from '@/view/layout/index.vue'

const keepAlive = true
const hidden = true

let routes = [
    {
        path      : '/login',
        name      : 'login',
        noLogin   : true,
        hidden    : true,
        meta      : {title : '登录'},
        icon      : require('@/assets/images/store.png'),
        component : () => import('@/view/login.vue')
    },

    {
        path      : '*',
        component : () => import('@/view/404.vue'),
        beforeEnter (to, from, next)  {
            if(to.path == '/') {
                //if(!store.getters.hasPermission('home')) {
                    let menus = store.state.asideNavMenu
                    if(menus[0].children && menus[0].children.length > 0) {
                        next(menus[0].path + '/' + menus[0].children[0].path)
                        return
                    }
                //}
            }

            next()
        }
    },
]

export let asyncRouterMap = [
    // {
    //     path      : '/',
    //     meta      : {title : '首页'},
    //     icon      : require('@/assets/images/store.png'),
    //     component : layout,
    //     children  : [
    //         {path : '', name : 'home', role : 'home', meta : {title : '首页', keepAlive }, component : () => import('@/view/index/index.vue')}
    //     ]
    // },

    {
        path      : '/device',
        meta      : {title : '设备管理'},
        icon      : require('@/assets/images/store.png'),
        component : layout,
        roles     : [{role : 'deleteDevice', title : '删除设备'}],
        children  : [
            {path : '', name : 'deviceList', role : 'deviceManage', meta : {title : '设备列表', keepAlive }, component : () => import('@/view/device/index.vue')},
            {path : 'add', name : 'addDevice', role : 'addDevice', meta : {title : '新增设备'}, component : () => import('@/view/device/add/index.vue'), hidden},
            {path : 'edit/:deviceId', name : 'editDevice', role : 'editDevice', meta : {title : '编辑设备'}, component : () => import('@/view/device/add/index.vue'), hidden}
        ]
    },

    {
        path      : '/deviceType',
        meta      : {title : '设备分类'},
        icon      : require('@/assets/images/store.png'),
        component : layout,
        roles     : [{role : 'addDeviceType', title : '新增类型'}, {role : 'editDeviceType', title : '编辑类型'}, {role : 'deleteDeviceType', title : '删除类型'}],
        children  : [
            {path : '', name : 'deviceType', role : 'deviceTypeList', meta : {title : '类型列表', keepAlive}, component : () => import('@/view/deviceType/index.vue')}
        ]  
    },

    {
        path      : '/chargePrice',
        meta      : {title : '充电价格'},
        icon      : require('@/assets/images/store.png'),
        component : layout,
        roles     : [{role : 'deleteChargePrice', title : '删除充电价格'}],
        children  : [
            {path : '', name : 'chargePriceList', role : 'chargePriceList', meta : {title : '价格列表', keepAlive }, component : () => import('@/view/chargePrice/index.vue')},
            {path : 'add', name : 'addChargePrice', role : 'addChargePrice', meta : {title : '新增价格'}, component : () => import('@/view/chargePrice/add/index.vue'), hidden},
            {path : 'edit/:id', name : 'editChargePrice', role : 'editChargePrice', meta : {title : '编辑价格'}, component : () => import('@/view/chargePrice/add/index.vue'), hidden}
        ]  
    },

    {
        path      : '/networkType',
        meta      : {title : '网络类型'},
        icon      : require('@/assets/images/store.png'),
        component : layout,
        roles     : [{role : 'addNetworkType', title : '新增类型'}, {role : 'editNetworkType', title : '编辑类型'}, {role : 'deleteNetworkType', title : '删除类型'}],
        children  : [
            {path : '', name : 'networkType', role : 'networkTypeList', meta : {title : '类型列表', keepAlive}, component : () => import('@/view/networkType/index.vue')}
        ]  
    },

    {
        path      : '/permission',
        meta      : {title : '权限管理'},
        icon      : require('@/assets/images/store.png'),
        component : layout,
        roles     : [{role : 'deletePermissionRole', title : '删除角色'}, {role : 'deleteAccount', title : '删除账号'}, {role : 'resetAccountPassword', title : '重置账号密码'}],
        children  : [
            {path : '', name : 'permissionRole', role : 'permissionRole', meta : {title : '角色管理', keepAlive}, component : () => import('@/view/permission/role.vue')},
            {path : 'addRole', name : 'addPermissionRole', role : 'addPermissionRole', meta : {title : '新增角色', keepAlive}, component : () => import('@/view/permission/addRole.vue'), hidden},
            {path : 'editRole/:roleId', name : 'editPermissionRole', role : 'editPermissionRole', meta : {title : '编辑角色', keepAlive}, component : () => import('@/view/permission/addRole.vue'), hidden},
            {path : 'account', name : 'adminAccountList', role : 'adminAccountList', meta : {title : '账号列表', keepAlive}, component : () => import('@/view/permission/account.vue')},
            {path : 'addAccount', name : 'addAccount', role : 'addAccount', meta : {title : '新增账户', keepAlive}, component : () => import('@/view/permission/addAccount.vue'), hidden},
            {path : 'editAccount/:accountId', name : 'editAccount', role : 'editAccount', meta : {title : '编辑账号', keepAlive}, component : () => import('@/view/permission/addAccount.vue'), hidden},
        ]
    }
]


const router = new VueRouter({
	//mode: 'history',
	routes : routes
})

export function filterAsyncRoutes(routes) {
    const res = []
    routes.forEach(route => {
        if(route.children) {
            let children = filterAsyncRoutes(route.children)
            if(children && children.length > 0) {
                route.children = children
                res.push(route)
            }
        }else {
            if(store.getters.hasPermission(route.role)) {
                res.push(route)
            }
        }
    })
    return res
}

export function getMenu(routes = []) {
    let menus = []
    try {
        if(!Array.isArray(routes)) {
            return []
        }
        routes.forEach(route => {
            if(route.hidden) return
            let menu = {
                path : route.path,
                name : route.name,
                title : route.meta.title
            }
            if(route.icon) {
                menu.icon = route.icon
            }
            if(route.children && route.children.length > 0) {
                let children = getMenu(route.children)
                if(children.length > 0) {
                    menu.children = children
                    menus.push(menu)
                }
            }else {
                if(store.getters.hasPermission(route.role)) {
                    menus.push(menu)
                }
                
            }
        })
    } catch (error) {
        console.log(error.message)
    }
    return menus
}


const whiteList = ['/login']


router.beforeEach((to, from, next) => {
	if(to.meta){
      document.title = to.meta.title || '管理后台'
    }
    if(store.state.loginStatus == true) { //已经登录
        if(to.path == '/login') {
            next({path : '/'})
            return
        }
        if(store.state.asideNavMenu == null) { //未构建菜单
            let accessRoutes = filterAsyncRoutes(asyncRouterMap)
            router.addRoutes(accessRoutes)
            asyncRouterMap = accessRoutes
            let menus = getMenu(asyncRouterMap)
            store.commit('updateAsideNavMenu', menus)

            next({...to, replace : true})
        }else {
            next()
        }
    
    }else {
        if(whiteList.includes(to.path)) {
            next()
        }else {
            next({path : '/login'})
        }
        
    }
})


export default router