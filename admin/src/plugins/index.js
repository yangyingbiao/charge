import Vue from 'vue'

import $alert from '@/plugins/alert.js'
$alert.install(Vue)
Vue.use($alert)

import $confirm from '@/plugins/confirm.js'
$confirm.install(Vue)
Vue.use($confirm)