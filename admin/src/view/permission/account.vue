<template>
	<div>
		<div>
			<span>
				<x-search v-model='filter.name' @search='search()'></x-search>
			</span>
			<span class='m-l-20' v-if='$store.getters.hasPermission("addAccount")'>
				<router-link to='/permission/addAccount'><el-button size='small' type='success'>新增</el-button></router-link>
			</span>
		</div>
		<section class='m-t-30'>
			<el-table  ref='multipleTable' :data='listData'>
				<el-table-column label='名称'>
					<template slot-scope='scope'>{{scope.row.name}}</template>
				</el-table-column>
				<el-table-column label='账号'>
					<template slot-scope='scope'>{{scope.row.account}}</template>
				</el-table-column>
				<el-table-column label='角色'>
					<template slot-scope='scope'>
						<template v-for='(item, index) in scope.row.roleName'>
							<span class='color-primary' :class='{"m-l-5" : index != 0}' :key='index'>{{item}}</span>
						</template>
					</template>
				</el-table-column>
				<el-table-column label='启用状态'>
					<template slot-scope='scope'>
						<el-switch v-model='scope.row.status' @change='toggleStatus(scope.row)'></el-switch>
					</template>
				</el-table-column>
				<el-table-column label='备注'>
					<template slot-scope='scope'>{{ scope.row.remark}}</template>
				</el-table-column>
				<el-table-column label='创建时间' align='center'>
					<template slot-scope='scope'>{{scope.row.createTime | parseTime }}</template>
				</el-table-column>
				<el-table-column label='操作'>
					<template slot-scope='scope'>
						<router-link :to='"/permission/editAccount/" + scope.row.userId' class='color-primary' v-if='$store.getters.hasPermission("editAccount")'>编辑</router-link>
						<el-button size='mini' class='m-l-10' type='text' @click='openResetPwd(scope.row.userId)' v-if='$store.getters.hasPermission("resetAccountPassword")'>重置密码</el-button>
						<el-button size='mini' class='m-l-10' type='text' @click='deleteAccount(scope.row.userId)' v-if='$store.getters.hasPermission("deleteAccount")'>删除</el-button>
					</template>
				</el-table-column>
			</el-table>
			
			<div class='m-t-30'>
				<x-pagination ref='pagination' :query='pageQuery' v-on:page='load' url='account/list'></x-pagination>
			</div>
		</section>
		
		
		<!-- 重置密码的弹窗 -->
		<x-dialog-box title='重置密码' :visible.sync='showResetPwdDiaLog' @confirm='resetPwd'>
			<el-form :model='formData' ref='form' :rules='rules' label-width='80px' >
				<el-form-item label='新密码：' prop='password'>
					<el-input size='small' clearable type='password' v-model='formData.password' v-size.width='300' autocomplete='off'></el-input>
				</el-form-item>
			</el-form>
		</x-dialog-box>

	</div>
</template>

<script>
	import { Http } from '@/utils'
	
	let http = new Http()
	
	export default {
		name : 'price',
		data () {
			return {
				filter : {
					name : ''
				}, 
				
				pageQuery : {
					name : ''
				},
				
				listData : [],

				showResetPwdDiaLog : false,
				
				formData : {
					accountId : '',
					password : ''
				},

				rules : {
					password : {
						required : true,
						validator :  (rule, value, callback) => {
							if (value === '') {
							  callback(new Error('请填写密码'));
							}else if(value.includes(' ')) {
								callback(new Error('密码不可以包含空格'));
							} else if (value.length < 7 || value.length > 100) {
							  callback(new Error('密码长度必须6-16个字符'));
							} else {
							  callback();
							}
						}
					}
				}
				
			}
		},
		
		methods : {
			search : function(){
				let filter = Object.assign({}, this.filter)
				this.pageQuery = filter
				
				this.$nextTick(() => {
					this.$refs.pagination.initPage()
				})
	
			},
			
			load : function(rows) {
				rows.forEach(row => {
					row.status = Boolean(row.status)
				})
				
				this.listData = rows
			},
			
			async toggleStatus (scope) {
				let res = await http.put('account/changeStatus', {id : scope.userId})
				if(res.success) {
						
				}else{
					scope.status = !scope.status
					this.errorToast(res.msg)
				}
			},
			
			async deleteAccount (userId){
				let res = await this.confirm('确定删除该账号吗')
				if(res) {
					let res = await http.delete('account/delete', {id : userId})
					if(res.success) {
						this.successToast('删除成功')
						this.$refs.pagination.initPage()
					}else {
						this.errorToast(res.msg)
					}
				}
				
			},
			
			openResetPwd (accountId){
				this.formData.password = ''
				this.formData.accountId = accountId
				this.showResetPwdDiaLog = true
			},
			
			resetPwd () {
				this.$refs['form'].validate(async (valid) => {
					//if(!valid) return
					let res = await http.put('account/resetPassword', {id : this.formData.accountId, password : this.formData.password})
					if(res.success){
						this.showResetPwdDiaLog = false
						this.successToast('重置成功')
					}else{
						this.errorToast(res.msg)
					}
				})
				
			},
				
		},
		
		activated () {
			if(!this.ISFIRST){
				this.ISFIRST = true
			}else{
				this.$refs.pagination.getPage()
			}
		}
	}
</script>