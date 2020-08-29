<template>
    <div>
        <div>
            <el-button size='small' type='success' @click='openAddType'>新增分类</el-button>
        </div>
        <el-table :data='typeList' class='m-t-30' style='width:100%;'>
            <el-table-column label='分类名称'>
                <template slot-scope='scope'>
                    <span v-size.paddingLeft='20 * scope.row.level'>
                        <i class='type-line' v-size.width='10 * scope.row.level'></i>
                        {{scope.row.name}}
                    </span>
				</template>
            </el-table-column>
            <el-table-column align='center' label='操作'>
                <template slot-scope='scope'>
                    <el-button type='text' @click='openEditType(scope.row)'>编辑</el-button>
                    <el-button type='text' @click='deleteType(scope.row.id, scope.$index)'>删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <x-dialog-box :title='formData.id ? "编辑分类" : "新增分类"' :visible.sync='showAddDialog' @confirm='submit'>
			<div>
                <div>
                    所属分类：
                    <el-select v-size.width='250' v-model='formData.parentId' :disabled='formData.id > 0' size='small'>
                        <el-option :key='0' :value='0' label='顶级分类'></el-option>
                        <template v-for='item in typeList'>
                            <el-option :key='item.id' :value='item.id' :label='item.name'>
                                <span><i class='type-line' v-size.width='10 * (item.level + 1)' v-size.marginLeft='10 * (item.level + 1)'></i>{{item.name}}</span>
                            </el-option>
                        </template>
                    </el-select>
                </div>
                <div class='m-t-15'>
                    分类名称：
                    <el-input v-size.width='250' v-model='formData.name' size='small' clearable maxlength='10'></el-input>
                </div>
            </div>
		</x-dialog-box>

    </div>
</template>

<script>
import { Http } from '@/utils'
let http = new Http()
export default {
    data () {
        return {
            showAddDialog : false,
            typeList : [],
            formData : {
                parentId : 0,
                name : ''
            }
        }
    },
    methods : {
        openAddType () {
            this.formData = {parentId : 0, name : ''}
            this.$nextTick(() => {
                this.showAddDialog = true
            })
        },

        openEditType (scope) {
            this.formData = scope
            this.$nextTick(() => {
                this.showAddDialog = true
            })
        },

        async getTypeList () {
            let res = await (new Http()).get('deviceType/list')
            if(res.data) {
                this.typeList.splice(0)
                this.$nextTick(() => {
                    this.buildList(res.data, 0)
                })
            }
        },

        buildList (typeList, parentId) {
            typeList.forEach(item => {
                if(parentId == item.parentId) {
                    this.typeList.push(item)
                    this.buildList(typeList, item.id)
                }
            })
        },

        async submit() {
            let formData = this.formData
            if(formData.name === '') return this.errorToast('请输入分类名')
            if(!formData.id) {
                let res = await http.post('deviceType/add', formData)
                if(res.success) {
                    this.successToast('新增成功')
                    this.getTypeList()
                    this.showAddDialog = false
                }else {
                    this.errorToast(res.msg)
                }
            }else {
               let res = await http.put('deviceType/edit', formData)
                if(res.success) {
                    this.successToast('修改成功')
                    this.getTypeList()
                    this.showAddDialog = false
                }else {
                    this.errorToast(res.msg)
                } 
            }
            
        },

        async deleteType (id, index) {
            let res = await this.confirm('确定删除该分类吗')
            if(res) {
                let res = await http.delete('deviceType/delete', {id : id})
                if(res.success) {
                    this.successToast('删除成功')
                    this.typeList.splice(index, 1)
                }else {
                    this.errorToast(res.msg)
                }
            }
        }
    },
    created () {
        this.getTypeList()
    }
}
</script>

<style scoped>
    .type-line{
        display: inline-block;
        height: 1PX;
        background: #999;
        vertical-align: middle;
        margin-right: 5px;
    }
</style>