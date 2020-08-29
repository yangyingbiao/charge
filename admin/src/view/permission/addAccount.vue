<template>
	<div>
		<div class='form-container'>
			<el-form id='form' :model='formData' :rules='rules'  ref='form' label-width='100px' label-position='left'>
				<el-form-item label='账号' prop='account'>
					<el-input v-model='formData.account' type='text' autocomplete='off'></el-input>
				</el-form-item>
				<el-form-item label='名称' prop='name'>
					<el-input v-model='formData.name' type='text' autocomplete='off'></el-input>
				</el-form-item>
				<el-form-item label='账号角色' prop='role'>
					<div>
						<el-checkbox-group v-model='formData.role'>
                            <template v-for='item in roles'>
                                <el-checkbox :label='item.id' :key='item.id'>{{item.name}}</el-checkbox>
                            </template>
						</el-checkbox-group>
					</div>
				</el-form-item>
				<el-form-item label='备注'>
					<div class='relative'>
						<el-input type='textarea' :rows='5' v-model='formData.remark' :maxlength='30'></el-input>
						<div style='position:absolute;right:10px;bottom:5px;'>{{formData.remark.length}}/30</div>
					</div>
				</el-form-item>
				<el-form-item label='启用状态'>
					<el-switch v-model='formData.status'></el-switch>
				</el-form-item>
				<el-form-item label=''>
					<el-button @click='$router.go(-1)'>返回</el-button>
					<el-button @click='submit' class='m-l-20' type='success'>保存</el-button>
				</el-form-item>
			</el-form>
		</div>
	</div>
</template>

<script>
	import { Http } from '@/utils'
	let http = new Http()
	export default {
		data() {
			return {
				roles : [],
				formData : {
					'account': '',
					'name': '',
					'remark': '',
					'role': [],
					'status': true
				},
				
				rules : {
					account : {
						required : true, message : '请填写账号'
					},
					name : {
						required : true, message : '请填写姓名'
					},
					role : {
						required : true, message : '请选择角色'
					}
				}
			}
		},
		
		methods : {
			submit () {
				this.$refs.form.validate(async validate => {
					if(!validate) {
						return
					}
					let formData = Object.assign({}, this.formData)
					formData.status = Number(formData.status)
                    let res  = await http.post('account/add', formData)
                    if(res.success) {
                        this.successToast('保存成功')
                        this.$router.go(-1)
                    }else{
                        this.errorToast(res.msg)
                    }
				})
			}
		},
		
		async created () {
			(new Http).get('permission/option').then(res => {
                if(res.success && res.data) {
					this.roles = res.data
				}
            })
			
			let accountId = this.$route.params.accountId || 0
			if(accountId > 0) {
				this.formData.id = accountId
				let res = await http.get('account', {id : accountId})
				if(res.success) {
					let data = res.data
					if(data.role) {
						if(typeof data.role == 'string') {
							data.role = JSON.parse(data.role)
						}
					}else {
						data.role = []
					}

					data.status = Boolean(data.status)

					for(let k in data) {
						if(k in this.formData) {
							this.formData[k] = data[k]
						}
					}


				}else{
					this.errorToast(res.msg)
				}
			}
			
		}
	}
</script>

<style scoped lang='scss'>
    .form-container{
        width: 800px;
        margin: 100px auto 0 auto;
    }
</style>>
