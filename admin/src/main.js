import Vue from 'vue'
import App from './App.vue'

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
Vue.use(ElementUI);

import '@/assets/css/css.scss'


Vue.config.productionTip = false

import router from '@/router/index.js'
import store from '@/store/index.js'

import '@/directive/index.js'
import '@/plugins/index.js'
import '@/components/index.js'

import * as filters from '@/filter'
Object.keys(filters).forEach(key => {
	Vue.filter(key, filters[key])
})

Vue.prototype.successToast = function(msg) {
	Vue.prototype.$message({
		showClose: true,
		message: msg,
		type: 'success'
  })
  
  return true
}

Vue.prototype.errorToast = function(msg) {
	Vue.prototype.$message({
		showClose: true,
		message: msg,
		type: 'error'
  })
  
  return false
}

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')
