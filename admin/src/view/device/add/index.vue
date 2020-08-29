<template>
    <div>
        <div v-size.width='600'>
            <el-form ref='form' :model='formData' :rules='rules' label-width='120px'>
                <el-form-item label='设备类型：' prop='deviceType'>
                    <x-selector  class='w-100' v-model='formData.deviceType' :fixed-count='false' url='deviceType/getType' parent-name='id' key-field='id' label-field='name'></x-selector>
                </el-form-item>
                <el-form-item label='SIM ICCID：' prop='simIccid'>
                    <el-input v-model='formData.simIccid'></el-input>
                </el-form-item>
                <el-form-item label='设备名称：'>
                    <el-input v-model='formData.deviceName'></el-input>
                </el-form-item>
                <el-form-item label='端口数量：'>
                    <el-input v-model='formData.portCount'></el-input>
                </el-form-item>
                <el-form-item label='通讯方式：'>
                    <x-remote-select class='w-100' preset='networkType' v-model='formData.networkType'></x-remote-select>
                </el-form-item>
                <el-form-item label='计时价格：'>
                    <x-remote-select class='w-100' preset='chargePrice' :query='{type : 1}' v-model='formData.priceT'></x-remote-select>
                </el-form-item>
                <el-form-item label='计量价格：'>
                    <x-remote-select class='w-100' preset='chargePrice' :query='{type : 2}' v-model='formData.priceW'></x-remote-select>
                </el-form-item>
                <el-form-item label='所在地区：'>
                    <x-selector v-model='formData.area' :fixed-count='true' :count='3' url='area' parent-name='id' key-field='id' label-field='name'></x-selector>
                </el-form-item>
                <el-form-item label='详细地址：'>
                    <el-input v-model='formData.address'></el-input>
                </el-form-item>
                <el-form-item label='是否启用：'>
					<el-switch v-model='formData.status'></el-switch>
				</el-form-item>
                <el-form-item>
                    <el-button size='small' type='primary' @click='submit'>确 定</el-button>
                    <el-button size='small'>取 消</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script src='./index.js'></script>