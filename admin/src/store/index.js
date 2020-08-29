import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)

import Cookies from 'js-cookie'

const userInfoKey = 'user-info-key'
const loginTokenKey = 'loginToken-key'
const refreshTokenTokenKey = 'lrefreshToken-key'
const loginExpireKey = 'loginExpire-key'

let loginToken = localStorage.getItem(loginTokenKey) || ''
let refreshToken = localStorage.getItem(refreshTokenTokenKey) || ''
let loginExpire = Cookies.get(loginExpireKey) || 0
if(loginExpire < 10) loginExpire = 0
let loginStatus = loginToken && refreshToken && (loginExpire >= 10)

const store = new Vuex.Store({
  state: {
    asideToggle : true,
    userPermissionList : JSON.parse(localStorage.getItem('permission') || "[]"),
    loginStatus : loginStatus,
    loginToken : loginToken,
    refreshToken : refreshToken,
    loginExpire : loginExpire,
    loginRefreshKey : localStorage.getItem('refresh-login-key') || '', //这个key没有用的，纯属忽悠人

    asideNavMenu : null,
    userInfo : JSON.parse(localStorage.getItem(userInfoKey) || "{}"),

    sysConf : {}
  },

  mutations : {
    updateUserInfo (state, userInfo) {
      state.userInfo = userInfo
      localStorage.setItem(userInfoKey, JSON.stringify(userInfo))
    },

    updateUserPermission(state, permission) {
      state.userPermissionList = permission
      localStorage.setItem('permission', JSON.stringify(permission))
    },

    updateAsideNavMenu (state, asideNavMenu) {
      state.asideNavMenu = asideNavMenu
    },

    updateLoginToken (state, loginToken) {
      state.loginStatus = true
      state.loginToken = loginToken
      localStorage.setItem(loginTokenKey, loginToken)
    },

    updateRefreshToken (state, refreshToken) {
      state.refreshToken = refreshToken
      localStorage.setItem(refreshTokenTokenKey, refreshToken)
    },

    updateLoginRefreshKey(state, key) {
      state.loginRefreshKey = key
      localStorage.setItem('refresh-login-key', key)
    },

    updateLoginExpire(state, expire) {
      let time = new Date() * 1 + expire * 1000 //到期时间戳（微秒）
      state.loginExpire = time
      Cookies.set(loginExpireKey, time, {expires : new Date(time)})
    },

    updateSysConf(state, sysConf) {
      state.sysConf = sysConf
    }

  },

  getters : {
    hasPermission : (state) => (permission) => {
      if(state.userInfo.super == 1) return true
      return state.userPermissionList.includes(permission)
    }
  }

})

export default store