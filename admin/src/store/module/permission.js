import router, { asyncRouterMap } from '@/router/index.js'

let userPermissionList = localStorage.getItem('permission') || []

export default {
    state : {
        permissionList : userPermissionList
    },

    
}