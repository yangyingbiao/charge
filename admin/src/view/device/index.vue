<template>
	<div>
		<div>
			<span>
				<x-search v-model='filter.name' @search='search()'></x-search>
			</span>
			<span class='m-l-20' v-if='$store.getters.hasPermission("addDevice")'>
				<router-link to='/device/add'><el-button size='small' type='primary'>新增</el-button></router-link>
			</span>
		</div>
		<section class='m-t-30'>
			<el-table ref='multipleTable' :data='listData'>
				<el-table-column label='设备名称'>
					<template slot-scope='scope'>{{scope.row.deviceName}}</template>
				</el-table-column>
				<el-table-column label='设备号'>
					<template slot-scope='scope'>{{scope.row.deviceId}}</template>
				</el-table-column>
                <el-table-column label='计时价格'>
                    <template slot-scope='scope'>
                        <template v-if='scope.row.priceTData'>
                            <el-popover trigger='hover'>
                                <div class='price-options' v-if='scope.row.priceTData.options'>
                                    <template v-for='(item, index) in scope.row.priceTData.options'>
                                        <div :key='index'>{{item.amount}}元{{item.quantity}}小时</div>
                                    </template>
                                </div>
                                <el-button type='text' slot='reference'>{{scope.row.priceTData.name}}</el-button>
                            </el-popover>
                        </template>
                    </template>
				</el-table-column>
                <el-table-column label='计量价格'>
					<template slot-scope='scope'>
                         <template v-if='scope.row.priceWData'>
                            <el-popover trigger='hover'>
                                <div class='price-options' v-if='scope.row.priceWData.options'>
                                    <template v-for='(item, index) in scope.row.priceWData.options'>
                                        <div :key='index'>{{item.amount}}元{{item.quantity}}度</div>
                                    </template>
                                </div>
                                <el-button type='text' slot='reference'>{{scope.row.priceWData.name}}</el-button>
                            </el-popover>
                        </template>
                    </template>
				</el-table-column>
				<el-table-column label='创建时间' align='center'>
					<template slot-scope='scope'>{{scope.row.createTime | parseTime }}</template>
				</el-table-column>
				<el-table-column label='操作'>
					<template slot-scope='scope'>
						<router-link :to='"/device/edit/" + scope.row.deviceId' class='color-primary' v-if='$store.getters.hasPermission("editDevice")'>编辑</router-link>
						<el-button class='m-l-10' type='text' @click='deleteDevice(scope.row.deviceId)' v-if='$store.getters.hasPermission("deleteDevice")'>删除</el-button>
					</template>
				</el-table-column>
			</el-table>
			
			<div class='m-t-30'>
				<x-pagination ref='pagination' :query='pageQuery' v-on:page='load' url='device/list'></x-pagination>
			</div>
		</section>

	</div>
</template>

<script src='./index.js'></script>

<style scoped src='./index.scss' lang='scss'></style>