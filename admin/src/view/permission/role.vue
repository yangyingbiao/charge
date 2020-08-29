<template>
	<div>
		<div>
			<span>
				<x-search placeholder='角色名' v-model='filter.roleName' @search='search()'></x-search>{{filter.roleName}}
			</span>
			<span class='m-l-20'>
				<router-link to='/permission/addRole'><el-button size='small' type='primary'>新增</el-button></router-link>
			</span>
		</div>
		<section class='m-t-30'>
			<el-table ref='multipleTable' :data='listData'>
				<el-table-column label='角色名称'>
					<template slot-scope='scope'>{{scope.row.name}}</template>
				</el-table-column>
				<el-table-column label='启用状态'>
					<template slot-scope='scope'>
						<el-switch v-model='scope.row.status' @change='toggleStatus(scope.row)'></el-switch>
					</template>
				</el-table-column>
				<el-table-column label='创建时间' align='center'>
					<template slot-scope='scope'>{{scope.row.createTime | parseTime }}</template>
				</el-table-column>
				<el-table-column label='备注'>
					<template slot-scope='scope'>{{ scope.row.remark}}</template>
				</el-table-column>
				<el-table-column label='操作'>
					<template slot-scope='scope'>
						<router-link :to='"/permission/editRole/" + scope.row.id'>编辑</router-link>
						<el-button size='mini' class='m-l-10' type='text' @click='deleteRole(scope.row.id)'>删除</el-button>
					</template>
				</el-table-column>
			</el-table>
			
			<div class='m-t-30'>
				<x-pagination ref='pagination' :query='pageQuery' v-on:page='load' url='permission/list'></x-pagination>
			</div>
		</section>

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
					roleName : ''
				}, 
				
				pageQuery : {
					roleName : ''
				},
				
				listData : [],
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
			
			load : function(rows){
				rows.forEach(row => {
					row.status = Boolean(row.status)
				})
				
				this.listData = rows
			},
			
			async toggleStatus (scope) {
				let res = await http.put('permission/changeStatus', {id : scope.id})
				if(res.success) {
						
				}else{
					scope.status = !scope.status
					this.errorToast(res.msg)
				}
			},
			
			async deleteRole (roleId){ //删除角色
				let res = await this.confirm('确定删除该角色吗')
				if(res) {
					let res = await http.delete('permission/delete', {id : roleId})
					if(res.success) {
						this.successToast('删除成功')
						this.$refs.pagination.initPage()
					}else {
						this.errorToast(res.msg)
					}
				}
				
			}
				
		}
	}
</script>