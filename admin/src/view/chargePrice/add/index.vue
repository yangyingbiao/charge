<template>
    <div>
        <div v-size.width='800'>
            <el-form :model='formData' label-width='120px'>
                <el-form-item label='价格名称：'>
                    <el-input v-model='formData.priceName'></el-input>
                </el-form-item>
                <el-form-item label='计费类型：'>
                    <template v-if='$store.state.sysConf.priceChargeType'>
                        <el-radio v-for='(item, key) in $store.state.sysConf.priceChargeType' :key='key' :disabled='id != 0' v-model='formData.chargeType' :label='parseInt(key)'>{{item}}</el-radio>
                    </template>
                </el-form-item>
                <el-form-item label='最小计费单位'>
					<el-input  v-model='formData.chargeUnit'>
						<template slot='append'>
                            <div class='input-append'>{{formData.chargeType == 1 ? '分钟' : '度'}}</div>
                        </template>
					</el-input>
				</el-form-item>
                <el-form-item label='最大功率：'>
                    <el-input v-model='formData.maxPower'>
                        <template slot='append'>
                            <div class='input-append'>W</div>
                        </template>
                    </el-input>
                </el-form-item>
                <el-form-item label='充满临界功率'>
					<el-input placeholder='' v-model='formData.criticalPower'>
						 <template slot='append'>
                            <div class='input-append'>W</div>
                        </template>
					</el-input>
				</el-form-item>
                <el-form-item label='延迟检测时间：'>
					<el-input v-model='formData.checkTime'>
						 <template slot='append'>
                            <div class='input-append'>分钟</div>
                        </template>
					</el-input>
				</el-form-item>
                <el-form-item label='预付费：'>
					<el-input placeholder='' v-model='formData.prepayment'>
						<template slot='append'>
							<div class='input-append'>元</div>
						</template>
					</el-input>
				</el-form-item>
                <el-form-item label='功率分档：'>
					<div class='add-container'>
						<div class='pre-item' v-for='(item, index) in formData.powerClass' :key='index'>
							<el-input v-model='item.quantity' class='input'><div slot='append' class='input-append'>{{formData.chargeType == 1 ? '分钟' : '度'}}</div></el-input>
							<el-input v-model='item.power' class='input m-l-20'><div slot='append' class='input-append'>W</div></el-input>
							<span @click='deletePowerClass(index)' class='delete el-icon-circle-close middle'></span>
						</div>
                        <div class='pre-item'>
                            <div @click='addPowerClass' class='add-btn relative'><i class='absolute xy el-icon-plus'></i></div>
                        </div>
					</div>
				</el-form-item>
                <el-form-item label='线上常用预设：'>
					<div class='add-container'>
						<div class='pre-item' v-for='(item, index) in formData.options' :key='index'>
							<el-input v-model='item.amount' class='input'><div slot='append' class='input-append'>元</div></el-input>
							<el-input v-model='item.quantity' class='input m-l-20'><div slot='append' class='input-append'>{{formData.chargeType == 1 ? '小时' : '度'}}</div></el-input>
							<span @click='deleteOption(index)' class='delete el-icon-circle-close middle'></span>
						</div>
                        <div class='pre-item'>
                            <div @click='addOption' class='add-btn relative'><i class='absolute xy el-icon-plus'></i></div>
                        </div>
					</div>
				</el-form-item>
                <el-form-item>
                    <el-button type='primary' @click='submit'>确 定</el-button>
                    <el-button>取 消</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script src='./index.js'></script>

<style scoped lang='scss'>
    .input-append{
        width: 30px;
        text-align: center;
    }
    .add-container{
        padding:20px;
        background:#f6f9fe;

        .pre-item{
            display: flex;
            position: relative;
            padding-right: 30px;;
            margin-top: 30px;
            &:first-child{
                margin-top: 0;
            }
            .input{
                flex: 1;
            }

            .delete{
                position: absolute;
                right: 0;
            }
        }
        .add-btn{
            flex: 1;
            cursor:pointer;
            border:1px dashed #dcdfe6;
            height:50px;
        }
    }
</style>