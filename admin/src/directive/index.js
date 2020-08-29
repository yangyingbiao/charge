import Vue from 'vue'

Vue.directive('size', {
	inserted : function(el, binding){
		var keys = Object.keys(binding.modifiers)
		keys.forEach(key => {
			el.style[key] = binding.value / 19.2 + 'rem'
		})
	}
})
