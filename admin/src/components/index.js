import Vue from 'vue'

import dialogBox from './dialog-box.vue'
import pagination from './pagination.vue'
import search from './search.vue'
import selector from './selector.vue'
import remoteSelect from './remote-select.vue'

[
    pagination,
    search,
    dialogBox,
    selector,
    remoteSelect
]
.forEach(component => {
    Vue.component('x-' + component.name, component)
})