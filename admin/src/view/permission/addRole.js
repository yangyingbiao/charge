import { Http } from '@/utils'
import { asyncRouterMap } from  '@/router'
import store from '@/store'
let http = new Http()

function filterAccessRoute(routes) {
	let target = []
	routes.forEach((route, index) => {
		try {
			if(!store.getters.hasPermission(route.role)) return
			let role = {
				id : route.role || index,
				label : route.meta.title
			}

			if(route.roles) {
				role.children = []
				route.roles.forEach(item => {
					role.children.push({id : item.role, label : item.title})
				})
			}

			if(route.children && route.children.length > 0) {
				let children = filterAccessRoute(route.children)
				if(children.length == 0) return
				if(role.children) {
					children.forEach(row => {
						role.children.push(row)
					})
				}else {
					if(children.length == 1) {
						role.id = children[0].id
					}else {
						role.children = children
					}
				}
				
				
				target.push(role)
			}else {
				target.push(role)
			}
		} catch (error) {
			console.log(error.message)
		}
	})

	return target
}


export default {
	data () {
		return {
			roles : [],
			formData : {
				id : '',
				name : '',
				permission : [],
				status : true,
				remark : ''
			},
			
			rules : {
				
			}
		}
	},

	methods : {
		async submit () {
			let formData = Object.assign({}, this.formData)
			formData.permission = this.$refs.roleTree.getCheckedKeys(true)
			if(formData.permission.length == 0) {
				this.errorToast('请选择权限')
				return
			}
			
			formData.status = Number(formData.status)
			
			let res = await http.post(formData.id ? 'permission/edit' : 'permission/add', formData)
			if(res.success) {
				this.successToast('保存成功')
				this.$router.go(-1)
			}else{
				this.errorToast(res.msg)
			}
		}
	},
	
	async created () {
		this.roles = filterAccessRoute(asyncRouterMap)
		let roleId = this.$route.params.roleId || ''
		if(roleId){ //编辑
			this.formData.id = roleId
			let res = await http.get('permission/role', {id : roleId})
			if(res.success) {
				let data = res.data
				for(let k in data) {
					if(!(k in this.formData)) continue
					
					if(k == 'status'){
						data[k] = Boolean(data[k])
					}else if(k == 'permission') {
						if(data[k] !== '') {
							if(typeof data[k] === 'string') {
								data[k] = JSON.parse(data[k])
							}
						}else{
							data[k] = []
						}
					}
					
					this.formData[k] = data[k]
					
				}
			}
		}
		
		
		
	}
}