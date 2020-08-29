<template>
	<div>
		<div>
			<span>
				<x-search v-model='filter.name' @search='search()'></x-search>
			</span>
			<span class='m-l-20' v-if='$store.getters.hasPermission("addAccount")'>
				<router-link to='/chargePrice/add'><el-button size='small' type='primary'>新增</el-button></router-link>
			</span>
		</div>
		<section class='m-t-30'>
			<el-table ref='multipleTable' :data='listData'>
				<el-table-column label='名称'>
					<template slot-scope='scope'>{{scope.row.priceName}}</template>
				</el-table-column>
				<el-table-column label='类型'>
					<template slot-scope='scope'>{{$store.state.sysConf.priceChargeType[scope.row.chargeType]}}</template>
				</el-table-column>
				<el-table-column label='计费单位'>
					<template slot-scope='scope'>{{scope.row.chargeUnit}} {{scope.row.chargeType == 1 ? '分钟' : '度'}}</template>
				</el-table-column>
                <el-table-column label='最大功率'>
					<template slot-scope='scope'>{{scope.row.maxPower}}W</template>
				</el-table-column>
                <el-table-column label='临界功率'>
					<template slot-scope='scope'>{{scope.row.criticalPower}}W</template>
				</el-table-column>
                <el-table-column label='检测时间'>
					<template slot-scope='scope'>{{scope.row.checkTime}}分钟</template>
				</el-table-column>
                <el-table-column label='预付费'>
					<template slot-scope='scope'>{{scope.row.prepayment}}元</template>
				</el-table-column>
                <el-table-column label='功率分档'>
					<template slot-scope='scope'>
                        <template v-if='scope.row.powerClass'>
                            <div v-for='(item, index) in scope.row.powerClass' :key='index'>
                                <span>{{item.quantity}}分钟</span>
                                <span>{{item.power}}W</span>
                            </div>
                        </template>
                    </template>
				</el-table-column>
				<el-table-column label='常用预设'>
					<template slot-scope='scope'>
                        <template v-if='scope.row.options'>
                            <div v-for='(item, index) in scope.row.options' :key='index'>
                                <span>{{item.amount}}元</span>
                                <span>{{item.quantity}}{{scope.row.chargeType == 1 ? '小时' : '度'}}</span>
                            </div>
                        </template>
                    </template>
				</el-table-column>
				<el-table-column label='创建时间' align='center'>
					<template slot-scope='scope'>{{scope.row.createTime | parseTime }}</template>
				</el-table-column>
				<el-table-column label='操作'>
					<template slot-scope='scope'>
						<router-link :to='"/chargePrice/edit/" + scope.row.priceId' class='color-primary' v-if='$store.getters.hasPermission("editChargePrice")'>编辑</router-link>
						<el-button size='mini' class='m-l-10' type='text' @click='deletePrice(scope.row.priceId)' v-if='$store.getters.hasPermission("deleteChargePrice")'>删除</el-button>
					</template>
				</el-table-column>
			</el-table>
			
			<div class='m-t-30'>
				<x-pagination ref='pagination' :query='pageQuery' v-on:page='load' url='chargePrice/list'></x-pagination>
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
					name : ''
				}, 
				
				pageQuery : {
					name : ''
				},
				
				listData : []
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
					if(row.options) {
                        if(typeof row.options === 'string') {
                            row.options = JSON.parse(row.options)
                        }
                    }else {
                        row.options = []
                    }

                    if(row.powerClass) {
                        if(typeof row.powerClass === 'string') {
                            row.powerClass = JSON.parse(row.powerClass)
                        }
                    }else {
                        row.powerClass = []
                    }
				})
				
				this.listData = rows
			},
			
			async deletePrice (id){
				let res = await this.confirm('确定删除该充电价格吗')
				if(res) {
					let { success } = await http.delete('chargePrice/delete', {id : id})
					if(success) {
						this.successToast('删除成功')
						this.$refs.pagination.initPage()
					}else {
						this.errorToast(res.msg)
					}
				}
				
			}
				
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