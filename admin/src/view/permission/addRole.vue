<template>
	<div>
		<section class='relative bc-fff'>
			<el-form id='form' :model='formData' :rules='rules'  ref='form' label-width='100px' label-position='left'>
				<el-form-item label='角色名称'>
					<el-input v-model='formData.name' type='text' autocomplete='off'></el-input>
				</el-form-item>
				<el-form-item label='添加权限'>
					<div>
						<el-tree :data='roles' :default-checked-keys='formData.permission' show-checkbox node-key='id' ref='roleTree' :props='{children : "children", label : "label"}'></el-tree>
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
		</section>
	</div>
</template>

<script src='./addRole.js'></script>

<style lang='scss' scoped>
	#form{
		width:800px;
		margin:auto;
	}
</style>