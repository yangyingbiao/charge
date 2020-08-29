import modal from './showModal.vue'

var $alert = {}

$alert.install = function(Vue, options) {
    Vue.prototype.$alert = function(content) {
        let TPL = Vue.extend(modal)
        let tp = new TPL()
        var node = tp.$mount()
        node.content = content
        node.showCancel = false
        //node.callback = callback
        document.body.appendChild(node.$el)
    }
}

export default $alert