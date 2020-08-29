import modal from './showModal.vue'

var $confirm = {}

$confirm.install = function(Vue, options) {
    Vue.prototype.confirm = function(content) {
        let TPL = Vue.extend(modal)
        let tp = new TPL()
        var node = tp.$mount()
        node.content = content
        const promise = new Promise(function(resolve, reject) {
            node.resolve = resolve
            node.reject = resolve
        })
        document.body.appendChild(node.$el)

        return promise
    }
}

export default $confirm